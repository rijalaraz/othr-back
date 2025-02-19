<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ApiResource(
 *  denormalizationContext={"groups"="category_write"},
 *  normalizationContext={"groups"={"category_read"}},
 * )
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Category
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"post_read","category_write","category_read"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="category")
     * @Groups({"post_read"})
     */
    private $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPost(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $posts): self
    {
        if (!$this->posts->contains($posts)) {
            $this->posts[] = $posts;
            $posts->setCategory($this);
        }

        return $this;
    }

    public function removePost(Post $posts): self
    {
        if ($this->posts->contains($posts)) {
            $this->posts->removeElement($posts);
            // set the owning side to null (unless already changed)
            if ($posts->getCategory() === $this) {
                $posts->setCategory(null);
            }
        }

        return $this;
    }
}
