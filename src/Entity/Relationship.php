<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RelationshipRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 * @ApiResource(
 *  normalizationContext={"groups"="relation_read"},
 *  attributes={
 *      "order"={"createdAt":"desc"}
 *  }
 * )
 */
class Relationship
{
    use DateTrait;
    use SoftDeleteableEntity;

    public $reverse = true;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"relation_read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="relationships")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"relation_read"})
     */
    private $sourceUser;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"relation_read"})
     */
    private $targetUser;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $team = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceUser(): ?User
    {
        return $this->sourceUser;
    }

    public function setSourceUser(?User $sourceUser): self
    {
        $this->sourceUser = $sourceUser;

        return $this;
    }

    public function getTargetUser(): ?User
    {
        return $this->targetUser;
    }

    public function setTargetUser(?User $targetUser): self
    {
        $this->targetUser = $targetUser;

        return $this;
    }

    /**
     * Function called after persist
     *
     * @param LifecycleEventArgs $args
     * @return void
     * @ORM\PostPersist
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        if($this->reverse) {
            $entity = $args->getObject(); // getEntity() would be removed from Symfony5

            $source = $entity->getSourceUser();
            $target = $entity->getTargetUser();

            $rel = new Relationship();
            $rel->reverse = false;
            $rel->setSourceUser($target);
            $rel->setTargetUser($source);

            $em = $args->getObjectManager();
            $em->persist($rel);

            $date = $entity->getCreatedAt();
            $rel->setCreatedAt($date);
            $em->persist($rel);

            $em->flush();
        }
    }

    public function getTeam(): ?bool
    {
        return $this->team;
    }

    public function setTeam(bool $team): self
    {
        $this->team = $team;

        return $this;
    }
}
