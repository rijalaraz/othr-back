<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NetworkMemberRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class NetworkMember
{
    use DateTrait;
    use SoftDeleteableEntity;

    public const TYPE_MAIN_ADMIN = 'main_admin';
    public const TYPE_ADMIN = 'admin';
    public const TYPE_USER = 'user';
    public const TYPES = [self::TYPE_MAIN_ADMIN, self::TYPE_ADMIN, self::TYPE_USER];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"network_read","user_read","user_write","user_jwt"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Network", inversedBy="networkMembers", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"user_read","user_write","user_jwt"})
     */
    private $network;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="networkMembers")
     * @Groups({"network_read"})
     */
    private $user;


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

    public function getNetwork(): ?Network
    {
        return $this->network;
    }

    public function setNetwork(?Network $network): self
    {
        $this->network = $network;

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

}
