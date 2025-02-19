<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Validator\FileSize;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use App\Entity\UserEvent;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 * @ApiResource(
 *  denormalizationContext={"disable_type_enforcement"=true,"groups"="event_write"},
 *  normalizationContext={"groups"={"event_read"}},
 *  attributes={
 *      "order"={"startDate":"asc"}
 *  },
 *  collectionOperations={
 *      "post"={
 *          "controller"="App\Controller\UploadEventMediaAction"
 *      },
 *      "get",
 *      "events_highlight"={
 *         "method"="GET",
 *         "path"="/events/highlight",
 *         "normalization_context"={"groups"={"event_high"}}
 *      },
 *      "home_highlights"={
 *         "method"="GET",
 *         "pagination_enabled"=false,
 *      },
 *  },
 *  itemOperations={
 *      "put"={
 *          "controller"="App\Controller\UploadEventMediaAction",
 *          "security"="is_granted('ROLE_SUPER_ADMIN') or object.user == user",
 *          "security_message"="Désolé, mais seul l'organisateur peut modifier son évènement",
 *          "denormalization_context"={"groups"={"event_put"}}
 *      },
 *      "get",
 *      "delete"={
 *          "security"="is_granted('ROLE_SUPER_ADMIN') or object.user == user",
 *          "security_message"="Désolé, mais seul l'organisateur peut supprimer son évènement"
 *      }
 *  }
 * )
 * @ApiFilter(OrderFilter::class, properties={"startDate": "ASC"})
 * @ApiFilter(DateFilter::class, properties={"startDate"})
 * @ApiFilter(SearchFilter::class, properties={"network.id": "exact","user.id": "exact"})
 * @ApiFilter(GroupFilter::class, arguments={"parameterName": "groups", "overrideDefaultGroups": true})
 */
class Event
{
    use DateTrait;
    use SoftDeleteableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"event_read","userevent_read","network_read","event_high","network_events_read","event_calendar"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"event_write","event_read","event_put","userevent_read","network_read","event_high","network_events_read","event_calendar"})
     * @Assert\NotBlank(message="Le titre est obligatoire")
     * @Assert\Length(min=3, minMessage="Le titre doit faire entre 3 et 255 caractères", max=255, maxMessage="Le titre doit faire entre 3 et 255 caractères")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"event_write","event_read","event_put"})
     * @Assert\NotBlank(message="La description est obligatoire")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Media", mappedBy="event", cascade={"persist", "remove"})
     * @Groups({"event_write","event_read","event_put"})
     */
    private $images;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"event_write","event_read","event_put","userevent_read","event_high","event_calendar"})
     * @Assert\NotBlank(message="L'adresse est obligatoire")
     */
    private $address;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"event_write","event_read","event_put","userevent_read","network_read","network_events_read","event_high"})
     * @Assert\NotBlank(message="La photo est obligatoire")
     * @FileSize()
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"event_write","event_read","event_high","userevent_read","event_calendar"})
     * @Assert\NotBlank(message="L'utilisateur est obligatoire")
     */
    public $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Network", inversedBy="events")
     * @Groups({"event_write","event_read","userevent_read","event_high","network_events_read","event_calendar"})
     */
    private $network;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"event_write","event_read","event_put","userevent_read","event_calendar"})
     * @Assert\NotBlank(message="Le nombre de places est obligatoire")
     * @Assert\Type(type="int",message="Le nombre de places doit être un entier")
     */
    private $nbTickets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ticket", mappedBy="event", cascade={"persist", "remove"})
     * @Groups({"event_write","event_read"})
     */
    private $tickets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserEvent", mappedBy="event", orphanRemoval=true)
     */
    private $userEvents;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"event_write","event_read","event_put","userevent_read","network_read","event_high","network_events_read","event_calendar"})
     * @Assert\NotBlank(message="Le Début est obligatoire")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"event_write","event_read","event_put","userevent_read","network_read","event_high","network_events_read","event_calendar"})
     * @Assert\NotBlank(message="La Fin est obligatoire")
     */
    private $endDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Zone", inversedBy="events")
     */
    private $zone;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->userEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Media $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setEvent($this);
        }

        return $this;
    }

    public function removeImage(Media $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getEvent() === $this) {
                $image->setEvent(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function setImage(Media $image): self
    {
        $this->image = $image;

        return $this;
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

    public function getNetwork(): ?Network
    {
        return $this->network;
    }

    public function setNetwork(?Network $network): self
    {
        $this->network = $network;

        return $this;
    }

    public function getNbTickets(): ?int
    {
        return $this->nbTickets;
    }

    public function setNbTickets(int $nbTickets): self
    {
        $this->nbTickets = $nbTickets;

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setEvent($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getEvent() === $this) {
                $ticket->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserEvent[]
     */
    public function getUserEvents(): Collection
    {
        return $this->userEvents->filter(function(UserEvent $userEvent) {
            return $userEvent->getPayment()->getPaymentStatus() != Payment::STATUS_CANCELED;
        });
    }

    public function addUserEvent(UserEvent $userEvent): self
    {
        if (!$this->userEvents->contains($userEvent)) {
            $this->userEvents[] = $userEvent;
            $userEvent->setEvent($this);
        }

        return $this;
    }

    public function removeUserEvent(UserEvent $userEvent): self
    {
        if ($this->userEvents->contains($userEvent)) {
            $this->userEvents->removeElement($userEvent);
            // set the owning side to null (unless already changed)
            if ($userEvent->getEvent() === $this) {
                $userEvent->setEvent(null);
            }
        }

        return $this;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate->format(\DateTimeInterface::RFC3339_EXTENDED);
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate->format(\DateTimeInterface::RFC3339_EXTENDED);
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Nombre de participants à l'Event
     * @Groups({"event_read","event_high","event_calendar","userevent_read"})
     */
    public function getNbParticipants(): ?int
    {
        $aNbPlaces = $this->getUserEvents()->map(function(UserEvent $userEvent){
            return $userEvent->getNbPlaces();
        })->toArray();
        return is_array($aNbPlaces) && count($aNbPlaces) > 0 ? array_sum($aNbPlaces) : 0;
    }

    /**
     * Prix minimum de billet
     * @Groups({"event_read"})
     */
    public function getTicketPrice(): ?float
    {
        $aTicketPrices = $this->getTickets()->map(function(Ticket $ticket){
            return $ticket->getPrice();
        })->toArray();
        return is_array($aTicketPrices) && count($aTicketPrices) > 0 ? min($aTicketPrices) : 0;
    }

    /**
     * Nombre de places restantes
     * @Groups({"event_read","event_calendar","userevent_read"})
     */
    public function getNbRemainingPlaces(): ?int
    {
        return $this->getNbTickets() - $this->getNbParticipants();
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

}
