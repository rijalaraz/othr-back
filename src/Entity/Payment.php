<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Stripe\StripeClient;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 * @ApiResource(
 *  normalizationContext={"groups"="payment_read"},
 *  itemOperations={
 *      "get",
 *      "confirm_payment"={
 *          "method"="PATCH",
 *          "path"="/payments/{id}/confirm",
 *          "denormalization_context"={"groups"={"payment_confirm"}},
 *          "validation_groups"={"payment_confirm"}
 *      }
 *  }
 * )
 */
class Payment
{
    use DateTrait;
    use SoftDeleteableEntity;

    public const STATUS_PAID = 'paid';
    public const STATUS_NOT_PAID = 'not_paid';
    public const STATUS_WAITING = 'waiting';
    public const STATUS_CANCELED = 'canceled';
    public const STATUTES = [self::STATUS_PAID, self::STATUS_NOT_PAID, self::STATUS_WAITING, self::STATUS_CANCELED];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"userevent_read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="payments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"userevent_write"})
     */
    private $user;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups({"userevent_write","payment_read"})
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=25)
     * @Groups({"userevent_read","payment_read"})
     */
    private $paymentStatus;

    /**
     * @ORM\Column(type="string", length=3)
     * @Groups({"userevent_write","payment_read"})
     */
    private $currency;

    /**
     * @ORM\OneToOne(targetEntity=UserEvent::class, mappedBy="payment", cascade={"persist", "remove"})
     */
    private $userEvent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"payment_confirm","payment_read"})
     */
    private $stripePaymentIntent;

    /**
     * @var integer $nbPlaces
     * @Groups({"userevent_write"})
     * @Assert\NotBlank(message="Le nombre de places est obligatoire")
     * @Assert\Type(type="int",message="Le nombre de places doit être un entier")
     */
    private $nbPlaces;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"userevent_read"})
     */
    private $stripePaymentError = [];

    /**
     * @Groups({"payment_confirm"})
     */
    private $paymentMethodId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(string $paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getUserEvent(): ?UserEvent
    {
        return $this->userEvent;
    }

    public function setUserEvent(UserEvent $userEvent): self
    {
        $this->userEvent = $userEvent;

        // set the owning side of the relation if necessary
        if ($userEvent->getPayment() !== $this) {
            $userEvent->setPayment($this);
        }

        return $this;
    }

    /**
     * @Groups({"userevent_read"})
     */
    public function getStripePaymentIntent(): ?string
    {
        return $this->stripePaymentIntent;
    }

    public function setStripePaymentIntent(?string $stripePaymentIntent): self
    {
        $this->stripePaymentIntent = $stripePaymentIntent;

        return $this;
    }

    /**
     * @Groups({"userevent_read"})
     */
    public function getStripeClientSecret()
    {
        $stripe = new StripeClient($_ENV['STRIPE_SECRET_KEY']);
        if($this->stripePaymentIntent) {
            $paymentIntent = $stripe->paymentIntents->retrieve(
                $this->stripePaymentIntent,
                []
            );
            return $paymentIntent->client_secret;
        }
    }

    /**
     * @Groups({"userevent_read","payment_read"})
     */
    public function getStripePaymentMethod()
    {
        $stripe = new StripeClient($_ENV['STRIPE_SECRET_KEY']);
        if($this->stripePaymentIntent) {
            $paymentIntent = $stripe->paymentIntents->retrieve(
                $this->stripePaymentIntent,
                []
            );
            if($paymentIntent->last_payment_error) {
                return $paymentIntent->last_payment_error->payment_method->id;
            } else if ($paymentIntent->payment_method) {
                return $paymentIntent->payment_method;
            } else {
                return '';
            }
        }
    }

    public function getStripePaymentError(): ?array
    {
        return $this->stripePaymentError;
    }

    public function setStripePaymentError(?array $stripePaymentError): self
    {
        $this->stripePaymentError = $stripePaymentError;

        return $this;
    }

    public function getPaymentMethodId(): ?string
    {
        return $this->paymentMethodId;
    }

    public function setPaymentMethodId(?string $paymentMethodId): self
    {
        $this->paymentMethodId = $paymentMethodId;

        return $this;
    }
}
