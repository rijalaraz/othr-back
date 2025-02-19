<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PostElementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\FileSize;
use Symfony\Component\Serializer\Annotation\Groups;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ApiResource(
 *  denormalizationContext={"groups"="postelement_write"},
 *  normalizationContext={"groups"={"postelement_read"}},
 *  attributes={
 *      "order"={"createdAt":"desc"}
 *  },
 *  collectionOperations={
 *      "post",
 *      "home_highlights"={
 *         "method"="GET",
 *         "pagination_enabled"=false,
 *      }
 *  }
 * )
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=PostElementRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class PostElement
{
    use DateTrait;
    use SoftDeleteableEntity;
    public const TITLE = 'title';
    public const SUB_TITLE = 'sub_title';
    public const PARAGRAPH ='paragraph';
    public const MEDIA = 'media';
    public const TYPES = [self::TITLE, self::SUB_TITLE,self::PARAGRAPH,self::MEDIA];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"postelement_write","postelement_read","post_read"})
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"postelement_write","postelement_read","post_read"})
     */
    private $type;

    /**
     * @ORM\OneToOne(targetEntity=Media::class, cascade={"persist", "remove"})
     * @Groups({"postelement_write","postelement_read","post_read"})
     * @Assert\Type(type="App\Entity\Media")
     * @Assert\Valid
     * @FileSize()
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"postelement_write","postelement_read","post_read"})
     */
    private $orders;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="postElements")
     */
    private $post;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

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

    public function getOrders(): ?string
    {
        return $this->orders;
    }

    public function setOrders(?string $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

}
