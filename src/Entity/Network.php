<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\OrganizerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\FileSize;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\CountOrderFilter;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NetworkRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 * @ApiResource(
 *  denormalizationContext={"groups"="network_write"},
 *  normalizationContext={"groups"="network_read"},
 *  collectionOperations={
 *      "post"={
 *          "controller"="App\Controller\UploadNetworkMediaAction",
 *          "security"="is_granted('ROLE_SUPER_ADMIN')",
 *          "security_message"="Seuls les super-administrateurs peuvent créer un réseau"
 *      },
 *      "get",
 *      "home_networks"={
 *         "method"="GET",
 *         "path"="/networks/home",
 *         "pagination_enabled"=false,
 *         "normalization_context"={"groups"={"network_home"}}
 *      },
 *      "home_highlights"={
 *         "method"="GET",
 *         "pagination_enabled"=false,
 *      },
 *  },
 *  itemOperations={
 *      "put"={
 *          "controller"="App\Controller\UploadNetworkMediaAction"
 *      },
 *      "get",
 *      "delete"={
 *          "security"="is_granted('ROLE_SUPER_ADMIN')",
 *          "security_message"="Seuls les super-administrateurs peuvent supprimer un réseau"
 *      },
 *      "join_network"={
 *         "method"="POST",
 *         "path"="/networks/{id}/memberequest",
 *         "controller"="App\Controller\JoinNetworkAction"
 *      },
 *      "subscribe_to_network"={
 *         "method"="POST",
 *         "path"="/networks/{id}/subscribe",
 *         "controller"="App\Controller\SubscribeToNetworkAction"
 *      }
 *  }
 * )
 * @ApiFilter(OrderFilter::class, properties={"nbMembersOffline":"DESC"}, arguments={"orderParameterName"="order"})
 * @ApiFilter(CountOrderFilter::class, properties={"networkMembers":"DESC","events":"DESC"}, arguments={"orderParameterName"="orderbycount"})
 */
class Network implements OrganizerInterface
{
    use DateTrait;
    use SoftDeleteableEntity;

    public const THE_MOST_MEMBERS = 'THE_MOST_MEMBERS';
    public const THE_MOST_EVENTS = 'THE_MOST_EVENTS';
    public const THE_MOST_OFFLINE_MEMBERS = 'THE_MOST_OFFLINE_MEMBERS';
    public const THE_MOST_ACTIVE_NETWORKS = 'THE_MOST_ACTIVE_NETWORKS';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"network_read","event_read","user_read","userevent_read","network_type_read","network_home","post_read","user_jwt"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"network_write","network_read","event_read","user_read","userevent_read","network_type_read","network_events_read","event_high","network_home","post_read","user_jwt"})
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(min=3, minMessage="Le nom doit faire entre 3 et 255 caractères", max=255, maxMessage="Le nom doit faire entre 3 et 255 caractères")
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"network_write","network_read","event_read"})
     * @Assert\NotBlank(message="L'adresse est obligatoire")
     */
    private $address;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\joinColumn(onDelete="SET NULL", nullable=true)
     * @Groups({"network_write","network_read","event_read","user_read","post_read"})
     * @FileSize()
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\NetworkType", inversedBy="networks")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"network_write","network_read","event_read","user_read","userevent_read","network_home","user_jwt"})
     * @Assert\NotBlank(message="Le type de réseau est obligatoire")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="network")
     * @Groups({"network_read"})
     */
    private $events;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="subscriptions")
     */
    private $subscribers;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", cascade={"persist", "remove"})
     * @ORM\joinColumn(onDelete="SET NULL")
     * @Groups({"network_write","network_read","event_read","user_read","userevent_read","event_high","event_calendar"})
     * @Assert\NotBlank(message="Le logo est obligatoire")
     * @FileSize()
     */
    private $logo;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", cascade={"persist", "remove"})
     * @ORM\joinColumn(onDelete="SET NULL")
     * @Groups({"network_write","network_read","event_read","network_type_read","network_home"})
     * @Assert\NotBlank(message="L'image de représentation est obligatoire")
     * @FileSize()
     */
    private $imageRepresentation;

    /**
     * @ORM\Column(type="text")
     * @Groups({"network_write","network_read","event_read"})
     * @Assert\NotBlank(message="La description Qu'est-ce que est obligatoire")
     */
    private $descriptionWho;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NetworkMember", mappedBy="network", orphanRemoval=true)
     * @Groups({"network_read"})
     */
    private $networkMembers;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", cascade={"persist", "remove"})
     * @ORM\joinColumn(onDelete="SET NULL")
     * @Groups({"network_write","network_read","event_read"})
     * @Assert\NotBlank(message="La vidéo est obligatoire")
     */
    private $video;

    /**
     * @ORM\Column(type="text")
     * @Groups({"network_write","network_read","event_read"})
     * @Assert\NotBlank(message="La description Pourquoi est obligatoire")
     */
    private $descriptionWhy;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", cascade={"persist", "remove"})
     * @ORM\joinColumn(onDelete="SET NULL")
     * @Groups({"network_write","network_read","event_read"})
     * @Assert\NotBlank(message="L'image d'ambiance est obligatoire")
     * @FileSize()
     */
    private $imageDescription;

    /**
     * @ORM\Column(type="text")
     * @Groups({"network_write","network_read","event_read"})
     * @Assert\NotBlank(message="La description Rejoindre est obligatoire")
     */
    private $descriptionHow;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"network_write","network_read","event_read"})
     * @Assert\NotBlank(message="L'adresse email est obligatoire")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"network_write","network_read","event_read"})
     * @Assert\NotBlank(message="Le site web est obligatoire")
     */
    private $website;

    /**
     * Nombre de d'adhérents au national indépendamment de l'application
     * @ORM\Column(type="integer")
     * @Groups({"network_write","network_read","event_read","user_read","network_home"})
     */
    private $nbMembersOffline;

    /**
     * @var string
     */
    private $resultType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Zone", inversedBy="networks")
     */
    private $zone;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="network")
     * @Groups({"network_read"})
     */
    private $posts;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
        $this->networkMembers = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    public function setResultType(string $resultType)
    {
        $this->resultType = $resultType;

        return $this;
    }

    public function getResultType(): ?string
    {
        return $this->resultType;
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

    public function getType(): ?NetworkType
    {
        return $this->type;
    }

    public function setType(?NetworkType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setNetwork($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getNetwork() === $this) {
                $event->setNetwork(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getSubscribers(): Collection
    {
        return $this->subscribers;
    }

    public function addSubscriber(User $subscriber): self
    {
        if (!$this->subscribers->contains($subscriber)) {
            $this->subscribers[] = $subscriber;
            $subscriber->addSubscription($this);
        }

        return $this;
    }

    public function removeSubscriber(User $subscriber): self
    {
        if ($this->subscribers->contains($subscriber)) {
            $this->subscribers->removeElement($subscriber);
            $subscriber->removeSubscription($this);
        }

        return $this;
    }

    public function getImageRepresentation(): ?Media
    {
        return $this->imageRepresentation;
    }

    public function setImageRepresentation(?Media $imageRepresentation): self
    {
        $this->imageRepresentation = $imageRepresentation;

        return $this;
    }

    /**
     * @return Collection|NetworkMember[]
     */
    public function getNetworkMembers(): Collection
    {
        return $this->networkMembers;
    }

    public function addNetworkMember(NetworkMember $networkMember): self
    {
        if (!$this->networkMembers->contains($networkMember)) {
            $this->networkMembers[] = $networkMember;
            $networkMember->setNetwork($this);
        }

        return $this;
    }

    public function removeNetworkMember(NetworkMember $networkMember): self
    {
        if ($this->networkMembers->contains($networkMember)) {
            $this->networkMembers->removeElement($networkMember);
            // set the owning side to null (unless already changed)
            if ($networkMember->getNetwork() === $this) {
                $networkMember->setNetwork(null);
            }
        }

        return $this;
    }

    public function getDescriptionWho(): ?string
    {
        return $this->descriptionWho;
    }

    public function setDescriptionWho(string $descriptionWho): self
    {
        $this->descriptionWho = $descriptionWho;

        return $this;
    }

    public function getVideo(): ?Media
    {
        return $this->video;
    }

    public function setVideo(?Media $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getDescriptionWhy(): ?string
    {
        return $this->descriptionWhy;
    }

    public function setDescriptionWhy(string $descriptionWhy): self
    {
        $this->descriptionWhy = $descriptionWhy;

        return $this;
    }

    public function getImageDescription(): ?Media
    {
        return $this->imageDescription;
    }

    public function setImageDescription(?Media $imageDescription): self
    {
        $this->imageDescription = $imageDescription;

        return $this;
    }

    public function getDescriptionHow(): ?string
    {
        return $this->descriptionHow;
    }

    public function setDescriptionHow(string $descriptionHow): self
    {
        $this->descriptionHow = $descriptionHow;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getLogo(): ?Media
    {
        return $this->logo;
    }

    public function setLogo(?Media $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Nombre d'adhérents au national
     */
    public function getNbMembersOffline(): ?int
    {
        return $this->nbMembersOffline;
    }

    public function setNbMembersOffline(int $nbMembersOffline): self
    {
        $this->nbMembersOffline = $nbMembersOffline;

        return $this;
    }

    /**
     * Nombre de membres de l'application
     * @Groups({"network_read","event_read","user_read","network_home"})
     */
    public function getNbNetworkMembers() {
        return $this->networkMembers->count();
    }

    public function getNumberOfNextEvents(?int $nbNextDays = 0): int
    {
        $today = new \DateTime();
        $today->setTime(00, 00, 00);
        $nextDays = clone $today;
        $nextDays->add(new \DateInterval('P'.$nbNextDays.'D'));
        return $this->events->filter(function(Event $event) use($today, $nextDays) {
            $date = new \DateTime($event->getStartDate());
            return $date >= $today && $date <= $nextDays;
        })->count();
    }

    /**
     * @Groups({"network_read","event_read","network_home"})
     */
    public function getNbNext15DaysEvents(): int
    {
        return $this->getNumberOfNextEvents(15);
    }

    public function getNumberOfLastEvents(?int $nbLastDays = 0): int
    {
        $today = new \DateTime();
        $today->setTime(00, 00, 00);
        $lastDays = clone $today;
        $lastDays->sub(new \DateInterval('P'.$nbLastDays.'D'));
        return $this->events->filter(function(Event $event) use($today, $lastDays) {
            $date = new \DateTime($event->getStartDate());
            return $date <= $today && $date >= $lastDays;
        })->count();
    }

    /**
     * @Groups({"network_read","event_read","network_home"})
     */
    public function getNbLast15DaysEvents(): int
    {
        return $this->getNumberOfLastEvents(15);
    }

    public function getNumberOfLastNetworkMembers(?int $nbLastDays = 0): int
    {
        $today = new \DateTime();
        $today->setTime(00, 00, 00);
        $lastDays = clone $today;
        $lastDays->sub(new \DateInterval('P'.$nbLastDays.'D'));
        return $this->networkMembers->filter(function(NetworkMember $networkMember) use($today, $lastDays) {
            return $networkMember->getCreatedAt() <= $today && $networkMember->getCreatedAt() >= $lastDays;
        })->count();
    }

    /**
     * @Groups({"network_read","event_read","network_home"})
     */
    public function getNbLast15DaysNetworkMembers(): int
    {
        return $this->getNumberOfLastNetworkMembers(15);
    }

    /**
     * @Groups({"network_read","event_read","userevent_read","network_type_read","network_home"})
     */
    public function getHighlight()
    {
        if(!$this->resultType) {
            return null;
        }

        switch ($this->resultType) {
            case self::THE_MOST_MEMBERS:
                $value = $this->getNbNetworkMembers();
            break;

            case self::THE_MOST_EVENTS:
                $value = $this->getNbNext15DaysEvents();
            break;

            case self::THE_MOST_OFFLINE_MEMBERS:
                $value = $this->getNbMembersOffline();
            break;

            case self::THE_MOST_ACTIVE_NETWORKS:
                $value = $this->getNbLast15DaysEvents() + $this->getNbLast15DaysNetworkMembers();
            break;
        }

        $high = [
            'type' => $this->resultType,
            'value' => $value
        ];

        return $high;
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

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setNetwork($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getNetwork() === $this) {
                $post->setNetwork(null);
            }
        }

        return $this;
    }
}
