<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TicketRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 * @ApiResource(
 *  normalizationContext={"groups"="ticket_read"},
 *  denormalizationContext={"groups"="ticket_write"},
 *  itemOperations={
 *      "put"={
 *          "security"="false",
 *          "security_message"="Vous ne pouvez pas modifier un billet"
 *      },
 *      "get",
 *      "delete"
 *  }
 * )
 */
class Ticket
{
    use DateTrait;
    use SoftDeleteableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"ticket_read","userevent_read","event_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ticket_read","ticket_write","userevent_read","event_write","event_read"})
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(min=3, minMessage="Le nom doit faire entre 3 et 255 caractères", max=255, maxMessage="Le nom doit faire entre 3 et 255 caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"ticket_read","ticket_write","userevent_read","event_write","event_read"})
     * @Assert\NotBlank(message="La description est obligatoire")
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups({"ticket_read","ticket_write","userevent_read","event_write","event_read"})
     * @Assert\NotBlank(message="Le prix du billet est obligatoire")
     * @Assert\Type(type="numeric",message="Le prix du billet doit être un décimal")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="tickets")
     */
    private $event;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserEvent", mappedBy="ticketType", cascade={"persist", "remove"})
     */
    private $userEvents;

    public function __construct()
    {
        $this->userEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getPrice(): ?float
    {
        return floatval($this->price);
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

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

    /**
     * @return Collection|UserEvent[]
     */
    public function getUserEvents(): Collection
    {
        return $this->userEvents;
    }

    public function addUserEvent(UserEvent $userEvent): self
    {
        if (!$this->userEvents->contains($userEvent)) {
            $this->userEvents[] = $userEvent;
            $userEvent->setTicketType($this);
        }

        return $this;
    }

    public function removeUserEvent(UserEvent $userEvent): self
    {
        if ($this->userEvents->contains($userEvent)) {
            $this->userEvents->removeElement($userEvent);
            // set the owning side to null (unless already changed)
            if ($userEvent->getTicketType() === $this) {
                $userEvent->setTicketType(null);
            }
        }

        return $this;
    }
}
