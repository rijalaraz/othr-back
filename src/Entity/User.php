<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use App\Entity\OrganizerInterface;
use App\Entity\Relationship;
use App\Validator\FileSize;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 * @ApiResource(
 *  denormalizationContext={"groups"="user_write"},
 *  normalizationContext={"groups"={"user_read","event_high","network_home"}},
 *  collectionOperations={
 *      "post",
 *      "get",
 *      "home_others"={
 *         "method"="GET",
 *         "path"="/users/home",
 *         "pagination_enabled"=false,
 *         "normalization_context"={"groups"={"user_others"}}
 *      },
 *      "others_the_most"={
 *         "method"="GET",
 *         "path"="/users/top/{the_most}",
 *         "pagination_enabled"=false,
 *         "normalization_context"={"groups"={"user_others"}}
 *      },
 *      "others_highlights"={
 *         "method"="GET",
 *         "path"="/users/highlight",
 *         "pagination_enabled"=false,
 *         "normalization_context"={"groups"={"user_others"}}
 *      },
 *      "home_highlights"={
 *         "method"="GET",
 *         "pagination_enabled"=false,
 *      },
 *  },
 *  itemOperations={
 *    "put"={
 *         "security"="is_granted('ROLE_SUPER_ADMIN') or object == user",
 *         "security_message"="Désolé, mais seul un administrateur ou le détenteur d'un compte peut le modifier"
 *    },
 *    "forgotten_password"={
 *         "method"="PATCH",
 *         "path"="/users/forgotten_password",
 *         "denormalization_context"={"groups"={"user_forgotten_password"}},
 *         "normalization_context"={"groups"={"user_forgotten_password"}},
 *          "read"=false
 *      },
 *     "reset_password"={
 *         "method"="PATCH",
 *         "path"="/users/reset_password/{token}",
 *         "denormalization_context"={"groups"={"user_reset_password"}},
 *         "normalization_context"={"groups"={"user_add_new_password"}},
 *         "read"=false
 *      },
 *      "get",
 *      "delete",
 *      "patch"={
 *         "input_formats"={"json"={"application/merge-patch+json"}},
 *         "path"="/users/{id}/validate",
 *         "denormalization_context"={"groups"={"user_validation"}},
 *         "security"="is_granted(constant('App\\Entity\\User::ROLE_NETWORK_ADMIN')) or is_granted(constant('App\\Entity\\User::ROLE_NETWORK'))",
 *         "security_message"="Seuls les administrateurs de réseaux ou les sous-administrateurs de réseaux peuvent valider un membre"
 *      }
 *  }
 * )
 * @ApiFilter(OrderFilter::class, properties={"createdAt"}, arguments={"orderParameterName"="order"})
 * @ApiFilter(GroupFilter::class, arguments={"parameterName": "groups", "overrideDefaultGroups": true})
 * @UniqueEntity("email", message="Un utilisateur ayant cette adresse email existe déjà")
 */
class User implements UserInterface, OrganizerInterface {

    use DateTrait;
    use SoftDeleteableEntity;

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const ROLE_USER = 'ROLE_USER';
    public const ROLES = [self::ROLE_SUPER_ADMIN, self::ROLE_USER];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user_read","user_user_read","event_read","network_read","notif_read","user_others","forgotten_password","userevent_read","post_read","sponsor_read","relation_read","user_jwt"})
     *
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user_read","event_read","notif_read","user_others","user_jwt"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user_write","user_reset_password"})
     * @Assert\NotBlank(message="Le mot de passe est obligatoire")
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity=Media::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Groups({"user_read","user_user_read","event_read","user_write","network_read","user_others","post_read","relation_read","user_jwt"})
     * @Assert\Type(type="App\Entity\Media")
     * @Assert\Valid
     * @FileSize()
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Color::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"user_read","user_write"})
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_read","user_write","user_user_read","event_read","notif_read","user_others","customer_read","user_jwt"})
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(min=2, minMessage="Le nom doit faire entre 2 et 255 caractères", max=255, maxMessage="Le nom doit faire entre 2 et 255 caractères")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_read","user_write","user_user_read","event_read","network_read","notif_read","user_others","customer_read","user_jwt"})
     * @Assert\NotBlank(message="Le prénom est obligatoire")
     * @Assert\Length(min=2, minMessage="Le prénom doit faire entre 2 et 255 caractères", max=255, maxMessage="Le prénom doit faire entre 2 et 255 caractères")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_read","user_write","relation_read","user_others","user_jwt"})
     */
    private $job;

    /**
     * @ORM\ManyToOne(targetEntity=WorkingSector::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"user_read","user_write"})
     */
    private $workingSector;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user_read","event_read","user_write","user_jwt"})
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user_read","user_write","event_read","userevent_read","notif_read","user_others","user_forgotten_password","user_add_new_password"})
     * @Assert\NotBlank(message="L'email est obligatoire")
     * @Assert\Email(message = "{{ value }} n'est pas un email valide.")
     */
    private $email;

    /**
     * @ORM\OneToOne(targetEntity=Media::class, cascade={"persist", "remove"})
     * @ORM\joinColumn(onDelete="SET NULL")
     * @Groups({"user_read","event_read","user_write"})
     */
    private $video;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"user_read","event_read","user_write"})
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity=Media::class, cascade={"persist", "remove"})
     * @ORM\joinColumn(onDelete="SET NULL")
     * @Groups({"user_read","event_read","user_write","userevent_read","event_high","event_calendar"})
     * @Assert\Type(type="App\Entity\Media")
     * @Assert\Valid
     * @FileSize()
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user_read","event_read","user_write","userevent_read","event_high","event_calendar"})
     */
    private $website;

    /**
     * @ORM\OneToMany(targetEntity=Activity::class, mappedBy="user", orphanRemoval=true, cascade={"persist"})
     * @Groups({"user_read","user_write"})
     * @Assert\All({
     *     @Assert\Type(type="App\Entity\Activity")
     * })
     * @Assert\Valid
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="activityUser", cascade={"persist", "remove"})
     * @ORM\joinColumn(onDelete="SET NULL")
     * @Groups({"user_read","event_read","user_write"})
     * @Assert\All({
     *     @Assert\Type(type="App\Entity\Media")
     * })
     * @Assert\Valid
     */
    private $activityImages;

    /**
     * @ORM\OneToMany(targetEntity=Argument::class, mappedBy="user", orphanRemoval=true, cascade={"persist"})
     * @Groups({"user_read","user_write"})
     * @Assert\All({
     *     @Assert\Type(type="App\Entity\Argument")
     * })
     * @Assert\Valid
     */
    private $arguments;

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="user", cascade={"persist", "remove"})
     * @ORM\joinColumn(onDelete="SET NULL")
     * @Groups({"user_read","event_read","user_write"})
     * @Assert\All({
     *     @Assert\Type(type="App\Entity\Media")
     * })
     * @Assert\Valid
     */
    private $achievements;

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="customerUser", cascade={"persist", "remove"})
     * @ORM\joinColumn(onDelete="SET NULL")
     * @Groups({"user_read","user_write"})
     * @Assert\All({
     *     @Assert\Type(type="App\Entity\Media")
     * })
     * @Assert\Valid
     */
    private $customers;

    /**
     * @Groups({"user_read"})
     */
    public $users = [];

    /**
     * @Groups({"user_read"})
     */
    public $teams = [];

    /**
     * @Groups({"user_read"})
     */
    public $swapableUsers = [];

    /**
     * @ORM\OneToMany(targetEntity=Relationship::class, mappedBy="sourceUser", orphanRemoval=true)
     */
    private $relationships;

    /**
     * @ORM\OneToMany(targetEntity=NetworkMember::class, mappedBy="user", cascade={"persist"})
     * @Groups({"user_read","user_write","user_jwt"})
     */
    private $networkMembers;

    /**
     * @ORM\OneToOne(targetEntity=Address::class, cascade={"persist", "remove"})
     * @Groups({"user_read","event_read","user_write","user_others","user_jwt"})
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="user", orphanRemoval=true)
     */
    private $events;

    /**
     * @ORM\ManyToMany(targetEntity=Network::class, inversedBy="subscribers")
     * @ORM\JoinTable(name="subscriber_subscription",
     * joinColumns={@ORM\JoinColumn(name="subscriber_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="subscription_id", referencedColumnName="id")}
     * )
     */
    private $subscriptions;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="sender", orphanRemoval=true)
     */
    private $senderNotifications;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="receiver", orphanRemoval=true)
     */
    private $receiverNotifications;

    /**
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="user", cascade={"persist", "remove"})
     * @Groups({"user_read","event_read","user_write"})
     */
    private $services;

    /**
     * @ORM\OneToMany(targetEntity=UserEvent::class, mappedBy="user", orphanRemoval=true)
     */
    private $userEvents;

    /**
     * @ORM\OneToMany(targetEntity=Payment::class, mappedBy="user", orphanRemoval=true)
     */
    private $payments;

    /**
     * @ORM\ManyToMany(targetEntity=Zone::class, inversedBy="users")
     * @Groups({"user_write"})
     */
    private $zones;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;
    /**
     * @Groups({"user_read"})
     */
    public $isFriend = false;

    /**
     * @ORM\OneToMany(targetEntity=Sponsorship::class, mappedBy="sponsor", orphanRemoval=true)
     * @Groups({"user_read"})
     */
    private $sponsor;

    /**
     * @ORM\OneToMany(targetEntity=Sponsorship::class, mappedBy="sponsored", orphanRemoval=true)
     * @Groups({"user_read"})
     */
    private $sponsored;

    /**
     * @ORM\OneToMany(targetEntity=UserTokenDevice::class, mappedBy="user", orphanRemoval=true)
     */
    private $userTokenDevices;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="user")
     * @Groups({"user_read"})
     */
    private $posts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stripeCustomer;

    public function __construct() {
        $this->events = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->senderNotifications = new ArrayCollection();
        $this->receiverNotifications = new ArrayCollection();
        $this->getNbUserEvents = new ArrayCollection();
        $this->relationships = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->sponsor = new ArrayCollection();
        $this->sponsored = new ArrayCollection();
        $this->userTokenDevices = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->activityImages = new ArrayCollection();
        $this->arguments = new ArrayCollection();
        $this->customers = new ArrayCollection();
        $this->achievements = new ArrayCollection();
        $this->networkMembers = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->zones = new ArrayCollection();
    }

    public function getId():  ? int {
        return $this->id;
    }

    public function getEmail() :  ? string {
        return $this->email;
    }

    public function setEmail(string $email) : self{
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     * @Groups({"user_jwt"})
     * @see UserInterface
     */
    public function getUsername(): string {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self{
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string {
        return (string) $this->password;
    }
    /**
     * @see UserInterface
     */
    public function getPlainPassword(): string {

    }

    public function setPassword(string $password): self{
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt() {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName():  ? string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName) : self{
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName():  ? string {
        return $this->lastName;
    }

    public function setLastName(string $lastName) : self{
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber():  ? string {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber) : self{
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection {
        return $this->events;
    }

    public function addEvent(Event $event): self {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setUser($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getUser() === $this) {
                $event->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @Groups({"network_read","event_read","user_read","user_others","post_read","sponsor_read","relation_read","userevent_read"})
     */
    public function getName() {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @return Collection|Network[]
     */
    public function getSubscriptions(): Collection {
        return $this->subscriptions;
    }

    public function addSubscription(Network $subscription): self {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
        }

        return $this;
    }

    public function removeSubscription(Network $subscription): self {
        if ($this->subscriptions->contains($subscription)) {
            $this->subscriptions->removeElement($subscription);
        }

        return $this;
    }

    public function getDescription():  ? string {
        return $this->description;
    }

    public function setDescription(string $description) : self{
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getSenderNotifications(): Collection {
        return $this->senderNotifications;
    }

    public function addSenderNotification(Notification $senderNotification): self {
        if (!$this->senderNotifications->contains($senderNotification)) {
            $this->senderNotifications[] = $senderNotification;
            $senderNotification->setSender($this);
        }

        return $this;
    }

    public function removeSenderNotification(Notification $senderNotification): self {
        if ($this->senderNotifications->contains($senderNotification)) {
            $this->senderNotifications->removeElement($senderNotification);
            // set the owning side to null (unless already changed)
            if ($senderNotification->getSender() === $this) {
                $senderNotification->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getReceiverNotifications(): Collection {
        return $this->receiverNotifications;
    }

    public function addReceiverNotification(Notification $receiverNotification): self {
        if (!$this->receiverNotifications->contains($receiverNotification)) {
            $this->receiverNotifications[] = $receiverNotification;
            $receiverNotification->setReceiver($this);
        }

        return $this;
    }

    public function removeReceiverNotification(Notification $receiverNotification): self {
        if ($this->receiverNotifications->contains($receiverNotification)) {
            $this->receiverNotifications->removeElement($receiverNotification);
            // set the owning side to null (unless already changed)
            if ($receiverNotification->getReceiver() === $this) {
                $receiverNotification->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserEvent[]
     */
    public function getUserEvents() : Collection {
        return $this->userEvents;
    }

    public function addUserEvent(UserEvent $userEvent): self {
        if (!$this->userEvents->contains($userEvent)) {
            $this->userEvents[] = $userEvent;
            $userEvent->setUser($this);
        }

        return $this;
    }

    public function removeUserEvent(UserEvent $userEvent): self {
        if ($this->userEvents->contains($userEvent)) {
            $this->userEvents->removeElement($userEvent);
            // set the owning side to null (unless already changed)
            if ($userEvent->getUser() === $this) {
                $userEvent->setUser(null);
            }
        }

        return $this;
    }

    public function isThisUserMySwaapr( ? User $user) : bool {
        return $this->relationships->exists(function ($key, $relationship) use ($user) {
            return $relationship->getSourceUser() === $user || $relationship->getTargetUser() === $user;
        });
    }

    private function getNbNotifications(int $nbLastDays, string $notificationType) : int{
        $today = new \DateTime();
        $today->setTime(00, 00, 00);
        $lastDays = clone $today;
        $lastDays->sub(new \DateInterval('P' . $nbLastDays . 'D'));
        return $this->senderNotifications->filter(function (Notification $notif) use ($today, $lastDays, $notificationType) {
            return $notif->getCreatedAt() <= $today && $notif->getCreatedAt() >= $lastDays &&
            $notif->getType() == $notificationType;
        })->count();
    }

    /**
     * @Groups({"user_others"})
     */
    public function getNbLast15DaysRecos() : int {
        return $this->getNbNotifications(15, NotificationType::RECOMMAND_USER);
    }

    /**
     * @Groups({"user_others"})
     */
    public function getNbLast30DaysRecos(): int {
        return $this->getNbNotifications(30, NotificationType::RECOMMAND_USER);
    }

    /**
     * @Groups({"user_others"})
     */
    public function getNbLast15DaysSwapRequests() : int {
        return $this->getNbNotifications(15, NotificationType::SWAAPE_REQUEST);
    }

    /**
     * @Groups({"user_others"})
     */
    public function getNbLast30DaysSwapRequests() : int {
        return $this->getNbNotifications(30, NotificationType::SWAAPE_REQUEST);
    }

    /**
     * @return Collection|Relationship[]
     */
    public function getRelationships(): Collection {
        return $this->relationships;
    }

    public function addRelationship(Relationship $relationship): self {
        if (!$this->relationships->contains($relationship)) {
            $this->relationships[] = $relationship;
            $relationship->setSourceUser($this);
        }

        return $this;
    }

    public function removeRelationship(Relationship $relationship): self {
        if ($this->relationships->contains($relationship)) {
            $this->relationships->removeElement($relationship);
            // set the owning side to null (unless already changed)
            if ($relationship->getSourceUser() === $this) {
                $relationship->setSourceUser(null);
            }
        }

        return $this;
    }

    private function getNbRelationships(int $nbLastDays) : int{
        $today = new \DateTime();
        $today->setTime(00, 00, 00);
        $lastDays = clone $today;
        $lastDays->sub(new \DateInterval('P' . $nbLastDays . 'D'));
        return $this->relationships->filter(function (Relationship $rel) use ($today, $lastDays) {
            return $rel->getCreatedAt() <= $today && $rel->getCreatedAt() >= $lastDays;
        })->count();
    }

    /**
     * @Groups({"user_others"})
     */
    public function getNbLast15DaysRelships(): int {
        return $this->getNbRelationships(15);
    }

    /**
     * @Groups({"user_others"})
     */
    public function getNbLast30DaysRelships(): int {
        return $this->getNbRelationships(30);
    }

    /**
     * @Groups({"user_others"})
     */
    public function getNbLast15DaysActions(): int {
        return $this->getNbLast15DaysRecos() + $this->getNbLast15DaysRelships() + $this->getNbLast15DaysUserEvents() + $this->getNbLast15DaysSwapRequests();
    }

    private function getNbUserEvents(int $nbLastDays) : int{
        $today = new \DateTime();
        $today->setTime(00, 00, 00);
        $lastDays = clone $today;
        $lastDays->sub(new \DateInterval('P' . $nbLastDays . 'D'));
        return $this->userEvents->filter(function (UserEvent $userEvent) use ($today, $lastDays) {
            $date = new \DateTime($userEvent->getRegistrationDate());
            return $date <= $today && $date >= $lastDays && $userEvent->getPayment()->getPaymentStatus() != Payment::STATUS_CANCELED;
        })->count();
    }

    /**
     * @Groups({"user_others"})
     */
    public function getNbLast15DaysUserEvents(): int {
        return $this->getNbUserEvents(15);
    }

    /**
     * @Groups({"user_others"})
     */
    public function getNbLast30DaysUserEvents(): int {
        return $this->getNbUserEvents(30);
    }

    /**
     * @Groups({"user_others"})
     */
    public function getNbLast30DaysActions(): int {
        return $this->getNbLast30DaysRecos() + $this->getNbLast30DaysRelships() + $this->getNbLast30DaysUserEvents() + $this->getNbLast30DaysSwapRequests();
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setUser($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getUser() === $this) {
                $payment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sponsorship[]
     */
    public function getSponsor() : Collection {
        return $this->sponsor;
    }

    public function addSponsor(Sponsorship $sponsor): self {
        if (!$this->sponsor->contains($sponsor)) {
            $this->sponsor[] = $sponsor;
            $sponsor->setSponsor($this);
        }

        return $this;
    }

    public function removeSponsor(Sponsorship $sponsor): self {
        if ($this->sponsor->contains($sponsor)) {
            $this->sponsor->removeElement($sponsor);
            // set the owning side to null (unless already changed)
            if ($sponsor->getSponsor() === $this) {
                $sponsor->setSponsor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sponsorship[]
     */
    public function getSponsored(): Collection {
        return $this->sponsored;
    }

    public function addSponsored(Sponsorship $sponsored): self {
        if (!$this->sponsored->contains($sponsored)) {
            $this->sponsored[] = $sponsored;
            $sponsored->setSponsored($this);
        }

        return $this;
    }

    public function removeSponsored(Sponsorship $sponsored): self {
        if ($this->sponsored->contains($sponsored)) {
            $this->sponsored->removeElement($sponsored);
            // set the owning side to null (unless already changed)
            if ($sponsored->getSponsored() === $this) {
                $sponsored->setSponsored(null);
            }
        }

        return $this;
    }

    /**
     * @Groups({"user_read"})
     */
    public function getTotalAmount() {
        $array = $this->getSponsor()->map(function (Sponsorship $sponsor) {
            return $sponsor->getAmount();

        });
        return is_array($array) && count($array) > 0 ? array_sum($array) : 0;
    }

    /**
     * @return Collection|UserTokenDevice[]
     */
    public function getUserTokenDevices(): Collection {
        return $this->userTokenDevices;
    }

    public function addUserTokenDevice(UserTokenDevice $userTokenDevice): self {
        if (!$this->userTokenDevices->contains($userTokenDevice)) {
            $this->userTokenDevices[] = $userTokenDevice;
            $userTokenDevice->setUser($this);
        }

        return $this;
    }

    public function removeUserTokenDevice(UserTokenDevice $userTokenDevice): self {
        if ($this->userTokenDevices->contains($userTokenDevice)) {
            $this->userTokenDevices->removeElement($userTokenDevice);
            // set the owning side to null (unless already changed)
            if ($userTokenDevice->getUser() === $this) {
                $userTokenDevice->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @Groups({"user_others","user_read"})
     */
    public function getNbRecos(): int
    {
        return $this->senderNotifications->filter(function(Notification $notif) {
            return $notif->getType() == NotificationType::RECOMMAND_USER;
        })->count();
    }

    /**
     * @Groups({"user_others","user_read"})
     */
    public function getNbSwap(): int
    {
        return $this->relationships->count();
    }

    public function getToken():  ? string{
        return $this->token;
    }

    public function setToken( ? string $token) : self {
        $this->token = $token;

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
            $post->setUser($this);
        }

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(string $job): self
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setUser($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            // set the owning side to null (unless already changed)
            if ($activity->getUser() === $this) {
                $activity->setUser(null);
            }
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    public function getStripeCustomer(): ?string
    {
        return $this->stripeCustomer;
    }

    public function setStripeCustomer(?string $stripeCustomer): self
    {
        $this->stripeCustomer = $stripeCustomer;

        return $this;
    }

    /**
     * @return Collection|Argument[]
     */
    public function getArguments(): Collection
    {
        return $this->arguments;
    }

    public function addArgument(Argument $argument): self
    {
        if (!$this->arguments->contains($argument)) {
            $this->arguments[] = $argument;
            $argument->setUser($this);
        }

        return $this;
    }

    public function removeArgument(Argument $argument): self
    {
        if ($this->arguments->contains($argument)) {
            $this->arguments->removeElement($argument);
            // set the owning side to null (unless already changed)
            if ($argument->getUser() === $this) {
                $argument->setUser(null);
            }
        }

        return $this;
    }

    public function getWorkingSector(): ?WorkingSector
    {
        return $this->workingSector;
    }

    public function setWorkingSector(?WorkingSector $workingSector): self
    {
        $this->workingSector = $workingSector;

        return $this;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function setColor(?Color $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getActivityImages(): Collection
    {
        return $this->activityImages;
    }

    public function addActivityImage(Media $activityImage): self
    {
        if (!$this->activityImages->contains($activityImage)) {
            $this->activityImages[] = $activityImage;
            $activityImage->setActivityUser($this);
        }

        return $this;
    }

    public function removeActivityImage(Media $activityImage): self
    {
        if ($this->activityImages->contains($activityImage)) {
            $this->activityImages->removeElement($activityImage);
            // set the owning side to null (unless already changed)
            if ($activityImage->getActivityUser() === $this) {
                $activityImage->setActivityUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Media $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
            $customer->setCustomerUser($this);
        }

        return $this;
    }

    public function removeCustomer(Media $customer): self
    {
        if ($this->customers->contains($customer)) {
            $this->customers->removeElement($customer);
            // set the owning side to null (unless already changed)
            if ($customer->getCustomerUser() === $this) {
                $customer->setCustomerUser(null);
            }
        }

        return $this;
    }

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function setImage(?Media $image): self
    {
        $this->image = $image;

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

    /**
     * @return Collection|Media[]
     */
    public function getAchievements(): Collection
    {
        return $this->achievements;
    }

    public function addAchievement(Media $achievement): self
    {
        if (!$this->achievements->contains($achievement)) {
            $this->achievements[] = $achievement;
            $achievement->setUser($this);
        }

        return $this;
    }

    public function removeAchievement(Media $achievement): self
    {
        if ($this->achievements->contains($achievement)) {
            $this->achievements->removeElement($achievement);
            // set the owning side to null (unless already changed)
            if ($achievement->getUser() === $this) {
                $achievement->setUser(null);
            }
        }

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
            $networkMember->setUser($this);
        }

        return $this;
    }

    public function removeNetworkMember(NetworkMember $networkMember): self
    {
        if ($this->networkMembers->contains($networkMember)) {
            $this->networkMembers->removeElement($networkMember);
            // set the owning side to null (unless already changed)
            if ($networkMember->getUser() === $this) {
                $networkMember->setUser(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setUser($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            // set the owning side to null (unless already changed)
            if ($service->getUser() === $this) {
                $service->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Zone[]
     */
    public function getZones(): Collection
    {
        return $this->zones;
    }

    public function addZone(Zone $zone): self
    {
        if (!$this->zones->contains($zone)) {
            $this->zones[] = $zone;
        }

        return $this;
    }

    public function removeZone(Zone $zone): self
    {
        if ($this->zones->contains($zone)) {
            $this->zones->removeElement($zone);
        }

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

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(string $website): self
    {
        $this->website = $website;

        return $this;
    }
}
