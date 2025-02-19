<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 * @ApiResource(
 *  normalizationContext={"groups"="notif_read"},
 *  denormalizationContext={"groups"="notif_write"},
 *  collectionOperations={
 *      "post"={
 *          "access_control"="is_granted(constant('App\\Security\\NotificationVoter::CREATE_NOTIFICATION'), object)"
 *       },
 *      "get"
 *  },
 *  itemOperations={
 *      "put"={
 *          "security"="false",
 *          "security_message"="Vous ne pouvez pas modifier une notification"
 *      },
 *      "get",
 *      "delete"
 *  }
 * )
 */
class Notification
{
    use DateTrait;
    use SoftDeleteableEntity;

    public const SENT = 'sent';
    public const DELIVERED = 'delivered';
    public const READ = 'read';
    public const FAILED = 'failed';
    public const DELETED = 'deleted';
    public const STATUTES = [self::SENT, self::DELIVERED, self::READ, self::FAILED, self::DELETED];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"notif_write","notif_read"})
     * @Assert\NotBlank(message="Le prénom est obligatoire")
     * @Assert\Length(min=2, minMessage="Le type doit faire entre 2 et 255 caractères", max=255, maxMessage="Le type doit faire entre 2 et 255 caractères")
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     * @Groups({"notif_write","notif_read"})
     * @Assert\NotBlank(message="Le message est obligatoire")
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="senderNotifications")
     * @Groups({"notif_write","notif_read"})
     * @Assert\NotBlank(message="L'envoyeur est obligatoire")
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="receiverNotifications")
     * @Groups({"notif_write","notif_read"})
     * @Assert\NotBlank(message="Le receveur est obligatoire")
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiver;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"notif_write","notif_read"})
     * @Assert\NotBlank(message="Le statut est obligatoire")
     */
    private $status;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"notif_write","notif_read"})
     */
    private $metadata = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }
}
