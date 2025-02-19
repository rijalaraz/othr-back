<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NetworkTypeRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 * @ApiResource(
 *  denormalizationContext={"groups":"network_type_write"},
 *  normalizationContext={"groups":"network_type_read"}
 * )
 */
class NetworkType
{
    use DateTrait;
    use SoftDeleteableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"network_type_read","network_read","event_read","user_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"network_type_write","network_type_read","network_read","event_read","user_read","userevent_read","network_home","user_jwt"})
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(min=3, minMessage="Le nom doit faire entre 3 et 255 caractères", max=255, maxMessage="Le nom doit faire entre 3 et 255 caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Groups({"network_type_write","network_type_read","network_read","event_read","user_read"})
     * @Assert\NotBlank(message="La description est obligatoire")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"network_type_write","network_type_read","network_read","event_read","user_read"})
     * @Assert\NotBlank(message="La couleur est obligatoire")
     */
    private $color;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Network", mappedBy="type")
     * @Groups({"network_type_read"})
     */
    private $networks;

    public function __construct()
    {
        $this->networks = new ArrayCollection();
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection|Network[]
     */
    public function getNetworks(): Collection
    {
        return $this->networks;
    }

    public function addNetwork(Network $network): self
    {
        if (!$this->networks->contains($network)) {
            $this->networks[] = $network;
            $network->setType($this);
        }

        return $this;
    }

    public function removeNetwork(Network $network): self
    {
        if ($this->networks->contains($network)) {
            $this->networks->removeElement($network);
            // set the owning side to null (unless already changed)
            if ($network->getType() === $this) {
                $network->setType(null);
            }
        }

        return $this;
    }
}
