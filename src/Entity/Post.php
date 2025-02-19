<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Validator\FileSize;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ApiResource(
 *  denormalizationContext={"groups"="post_write"},
 *  normalizationContext={"groups"={"post_read"}},
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
 *)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Post
{
    use DateTrait;
    use SoftDeleteableEntity;
    public const VIDEO = 'video';
    public const PODCAST = 'podcast';
    public const ARTICLE = 'article';
    public const TYPES = [self::VIDEO, self::PODCAST,self::ARTICLE];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user_read","network_read","post_write","post_read"})
     * @Assert\NotBlank(message="Le titre est obligatoire")
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"network_read","post_write","post_read"})
     * @Assert\NotBlank(message="Le contenu est obligatoire")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"network_read","post_write","post_read"})
     * @Assert\NotBlank(message="Le type  est obligatoire")
     */
    private $type;

    /**
     * @ORM\OneToOne(targetEntity=Media::class,inversedBy="post",cascade={"persist", "remove"}, fetch="EAGER")
     * @Groups({"post_write","post_read"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Type(type="App\Entity\Media")
     * @Assert\Valid
     * @FileSize()
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Network::class, inversedBy="posts")
     * @Groups({"post_write","post_read"})
     */
    private $network;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="posts")
     * @Groups({"post_read","post_write"})
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=PostElement::class, mappedBy="post")
     * @Groups({"post_read","post_write"})
     */
    private $postElements;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @Groups({"post_read","post_write"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=PostView::class, mappedBy="post")
     */
    private $postViews;


    public function __construct()
    {
        $this->postElements = new ArrayCollection();
        $this->postViews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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

    public function getNetwork(): ?Network
    {
        return $this->network;
    }

    public function setNetwork(?Network $network): self
    {
        $this->network = $network;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|PostElement[]
     */
    public function getPostElements(): Collection
    {
        return $this->postElements;
    }

    public function addPostElement(PostElement $postElement): self
    {
        if (!$this->postElements->contains($postElement)) {
            $this->postElements[] = $postElement;
            $postElement->setPost($this);
        }

        return $this;
    }

    public function removePostElement(PostElement $postElement): self
    {
        if ($this->postElements->contains($postElement)) {
            $this->postElements->removeElement($postElement);
            // set the owning side to null (unless already changed)
            if ($postElement->getPost() === $this) {
                $postElement->setPost(null);
            }
        }

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * 
     * @Groups({"post_read"})
     */
    public function getAudience()
    { 
      return $this->getPostViews()->count();
    }
    /**
     * @return Collection|PostView[]
     */
    public function getPostViews(): Collection
    {
        return $this->postViews;
    }

    public function addPostView(PostView $postView): self
    {
        if (!$this->postViews->contains($postView)) {
            $this->postViews[] = $postView;
            $postView->setPost($this);
        }

        return $this;
    }

    public function removePostView(PostView $postView): self
    {
        if ($this->postViews->contains($postView)) {
            $this->postViews->removeElement($postView);
            // set the owning side to null (unless already changed)
            if ($postView->getPost() === $this) {
                $postView->setPost(null);
            }
        }

        return $this;
    }

}
