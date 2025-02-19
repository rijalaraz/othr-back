<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AdvertRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Validator\FileSize;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ApiResource(
 *  denormalizationContext={"groups"="advert_write"},
 *  normalizationContext={"groups"={"advert_read"}},
 *  attributes={
 *      "order"={"createdAt":"desc"}
 *  },
 *  collectionOperations={
 *      "post"={
 *          "controller"="App\Controller\UploadAdvertMediaAction"
 *      },
 *      "home_highlights"={
 *         "method"="GET",
 *         "pagination_enabled"=false,
 *      }
 *  }
 * )
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=AdvertRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 *
 */
class Advert
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
     * @ORM\OneToOne(targetEntity=Media::class, cascade={"persist", "remove"})
     * @Groups({"advert_write","advert_read"})
     * @FileSize()
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"advert_write","advert_read"})
     */
    private $url;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
