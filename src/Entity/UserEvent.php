<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserEventRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 * @ApiResource(
 *  denormalizationContext={"groups"="userevent_write"},
 *  normalizationContext={"groups"="userevent_read"},
 *  attributes={
 *      "order"={"event.startDate":"asc"}
 *  },
 *  collectionOperations={
 *      "post",
 *      "get"
 *  }
 * )
 * @ApiFilter(DateFilter::class, properties={"event.startDate"})
 * @ApiFilter(SearchFilter::class, properties={"payment.paymentStatus": "exact"})
 */
class UserEvent
{
    use DateTrait;
    use SoftDeleteableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"userevent_read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userEvents")
     * @Groups({"userevent_write"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="userEvents")
     * @Groups({"userevent_write","userevent_read"})
     * @Assert\NotBlank(message="L'événement est obligatoire")
     */
    private $event;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"userevent_write"})
     */
    private $registrationDate;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"userevent_write"})
     * @Assert\NotBlank(message="Le nombre de places est obligatoire")
     * @Assert\Type(type="int",message="Le nombre de places doit être un entier")
     */
    private $nbPlaces;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ticket", inversedBy="userEvents")
     * @Groups({"userevent_write","userevent_read"})
     * @Assert\NotBlank(message="Le type de ticket est obligatoire")
     */
    private $ticketType;

    /**
     * @ORM\OneToOne(targetEntity=Payment::class, inversedBy="userEvent", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"userevent_write","userevent_read"})
     */
    private $payment;

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

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getRegistrationDate(): ?string
    {
        return $this->registrationDate->format(\DateTimeInterface::RFC3339_EXTENDED);
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nbPlaces;
    }

    public function setNbPlaces(int $nbPlaces): self
    {
        $this->nbPlaces = $nbPlaces;

        return $this;
    }

    public function getTicketType(): ?Ticket
    {
        return $this->ticketType;
    }

    public function setTicketType(?Ticket $ticketType): self
    {
        $this->ticketType = $ticketType;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }
}
