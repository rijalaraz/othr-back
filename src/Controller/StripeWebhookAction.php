<?php

namespace App\Controller;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookAction
{
    private $manager;
    private $stripe;

    public function __construct(EntityManagerInterface $manager, StripeClient $stripe)
    {
        $this->manager = $manager;
        $this->stripe = $stripe;
    }

    /**
     * @Route(
     *     name="stripe_post_webhook",
     *     path="/api/stripe_webhooks",
     *     methods={"POST"},
     *     defaults={
     *         "_api_item_operation_name"="post_webhook"
     *     }
     * )
     */
    public function __invoke()
    {
        // If you are testing your webhook locally with the Stripe CLI you
        // can find the endpoint's secret by running `stripe listen`
        // Otherwise, find your endpoint's secret in your webhook settings in the Developer Dashboard
        $endpoint_secret = $_ENV['STRIPE_ENDPOINT_SECRET'];

        $payload = @file_get_contents('php://input');
        if(!empty($_SERVER['HTTP_STRIPE_SIGNATURE'])) {
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        }
        $event = null;

        try {
            if(!empty($endpoint_secret) && !empty($sig_header)) {
                $event = \Stripe\Webhook::constructEvent(
                    $payload, $sig_header, $endpoint_secret
                );
            } else {
                $event = \Stripe\Event::constructFrom(
                    json_decode($payload, true)
                );
            }
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            return new Response('Invalid payload', 400, []);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return new Response('Invalid signature', 400, []);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                
                /**
                 * @var PaymentIntent
                 */
                $paymentIntent = $event->data->object;
                
                /**
                 * @var Payment
                 */
                $payment = $this->manager->getRepository(Payment::class)->findOneBy(['stripePaymentIntent' => $paymentIntent->id]);
               
                if($payment) {
                    $payment->setStripePaymentError(null);
                    $payment->setPaymentStatus(Payment::STATUS_PAID);
                    $this->manager->persist($payment);
                    $this->manager->flush();
    
                    $message = "Le paiement est terminé";
                    return new Response($message, 200, []);
                } else {
                    $message = "Aucune tentative de paiement n'a été faite";
                    return new Response($message, 400, []);
                }

                break;

            case 'payment_intent.payment_failed':
                /**
                 * @var PaymentIntent
                 */
                $paymentIntent = $event->data->object;

                /**
                 * @var Payment
                 */
                $payment = $this->manager->getRepository(Payment::class)->findOneBy(['stripePaymentIntent' => $paymentIntent->id]);

                if($payment) {
                    // See https://stripe.com/docs/error-codes
                    if($paymentIntent->last_payment_error->code === 'card_declined') {
                        // See https://stripe.com/docs/declines/codes
                    } else {

                    }
                    $lastPaymentError = json_decode(json_encode($paymentIntent->last_payment_error), true);
                    $payment->setStripePaymentError($lastPaymentError);
                    $payment->setPaymentStatus(Payment::STATUS_CANCELED);
                    $this->manager->persist($payment);
                    $this->manager->flush();
                }

                $message = $paymentIntent->last_payment_error->message;
                return new Response($message, 400, []);
                break;

            case 'payment_method.attached':
                $paymentMethod = $event->data->object;
                break;

            default:
                // Unexpected event type
                $message = "Evènement inconnu";
                return new Response($message, 400, []);
        }

    }
}