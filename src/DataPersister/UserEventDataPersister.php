<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Payment;
use App\Entity\UserEvent;
use Stripe\StripeClient;

final class UserEventDataPersister implements ContextAwareDataPersisterInterface
{
    private $decorated;
    private $stripe;

    public function __construct(ContextAwareDataPersisterInterface $decorated, StripeClient $stripe)
    {
        $this->decorated = $decorated;
        $this->stripe = $stripe;
    }

    public function supports($data, array $context = []): bool
    {
        return $this->decorated->supports($data, $context);
    }

    public function persist($data, array $context = [])
    {
        if($data instanceof UserEvent && (($context['collection_operation_name'] ?? null) === 'post')) {
            if( empty($data->getUser()->getStripeCustomer()) ) {
                $customer = $this->stripe->customers->create([
                    'name' => $data->getUser()->getName(),
                    'email' => $data->getUser()->getEmail(),
                ]);
                $data->getUser()->setStripeCustomer($customer->id);
            }
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => intval($data->getPayment()->getAmount() * 100),
                'currency' => $data->getPayment()->getCurrency(),
                'description' => $data->getEvent()->getTitle(),
                'customer' => $data->getUser()->getStripeCustomer(),
            ]);
            if($paymentIntent) {
                $data->getPayment()->setStripePaymentError(null);
                $data->getPayment()->setPaymentStatus(Payment::STATUS_WAITING);
                $data->getPayment()->setStripePaymentIntent($paymentIntent->id);
            }
        }

        $result = $this->decorated->persist($data, $context);

        return $result;
    }

    public function remove($data, array $context = [])
    {
        return $this->decorated->remove($data, $context);
    }
}