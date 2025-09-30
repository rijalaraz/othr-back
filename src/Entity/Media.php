<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Validator\MediaUrl;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Media
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
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"advert_write","advert_read","advert:put","event_write","network_write","event_put","user_write","company_write","post_write","postelement_write"})
     * @MediaUrl()
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="images")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $event;

    /**
     * @ORM\OneToOne(targetEntity=Post::class, mappedBy="image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="activityImages")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $activityUser;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="customers")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $customerUser;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="achievements")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @Groups({"event_read","network_read","user_read","user_user_read","network_type_read","userevent_read","event_high","network_events_read","event_calendar","user_others","network_home","post_read","postelement_read","relation_read","user_jwt"})
     * @return string|null
     */
    public function getMediaUrl(): ?string
    {
        if (preg_match("/uploads/i", $this->url)) {
            return $_ENV['PROTOCOL'].'://'.$_ENV['HOST'].'/'.$this->url;
        } else {
            return $this->url;
        }
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        // set (or unset) the owning side of the relation if necessary
        $newImage = null === $post ? null : $this;
        if ($post->getImage() !== $newImage) {
            $post->setImage($newImage);
        }

        return $this;
    }

    public function getActivityUser(): ?User
    {
        return $this->activityUser;
    }

    public function setActivityUser(?User $activityUser): self
    {
        $this->activityUser = $activityUser;

        return $this;
    }

    public function getCustomerUser(): ?User
    {
        return $this->customerUser;
    }

    public function setCustomerUser(?User $customerUser): self
    {
        $this->customerUser = $customerUser;

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
