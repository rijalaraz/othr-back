<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SponsorshipRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ApiResource(
 *  denormalizationContext={"groups"={"sponsor_write"}},
 *  normalizationContext={"groups"={"sponsor_read"}}
 * )
 * @ORM\Entity(repositoryClass=SponsorshipRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Sponsorship
{
    use DateTrait;
    use SoftDeleteableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"user_read","sponsor_read","sponsor_write"})
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sponsor")
     * @Groups({"sponsor_write","sponsor_read"})
     */
    private $sponsor;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sponsored")
     * @Groups({"sponsor_write","sponsor_read"})
     */
    private $sponsored;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSponsor(): ?User
    {
        return $this->sponsor;
    }

    public function setSponsor(?User $sponsor): self
    {
        $this->sponsor = $sponsor;

        return $this;
    }

    public function getSponsored(): ?User
    {
        return $this->sponsored;
    }

    public function setSponsored(?User $sponsored): self
    {
        $this->sponsored = $sponsored;

        return $this;
    }
}
