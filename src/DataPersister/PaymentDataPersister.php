<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Payment;
use Stripe\StripeClient;

final class PaymentDataPersister implements ContextAwareDataPersisterInterface
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
        if($data instanceof Payment && (($context['item_operation_name'] ?? null) === 'confirm_payment')) {
            $this->stripe->paymentIntents->confirm(
                $data->getStripePaymentIntent(), [
                    'payment_method' => $data->getPaymentMethodId()
                ]
            );
            $data->setPaymentStatus(Payment::STATUS_PAID);
        }
        
        $result = $this->decorated->persist($data, $context);

        return $result;
    }

    public function remove($data, array $context = [])
    {
        return $this->decorated->remove($data, $context);
    }
}