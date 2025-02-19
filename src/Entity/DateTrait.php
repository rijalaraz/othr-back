<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait DateTrait
{

    /**
     * Date when entity is created
     *
     * @var DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * Date when entity is updated
     *
     * @var DateTime
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * Function called before persist
     *
     * @return void
     * @ORM\PrePersist
     */
    public function onCreate()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Function called before update
     *
     * @return void
     * @ORM\PreUpdate
     */
    public function onUpdate()
    {
        $this->updatedAt = new DateTime();
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}