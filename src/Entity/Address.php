<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Address
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
     * @ORM\Column(type="string", length=255)
     * @Groups({"event_write","event_read","user_read","network_write","network_read","event_put","user_write","userevent_read","user_jwt"})
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"event_write","event_read","user_read","network_write","network_read","event_put","user_write","userevent_read","event_high","event_calendar","user_others","user_jwt"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"event_write","event_read","user_read","network_write","network_read","event_put","user_write","userevent_read","user_jwt"})
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"event_write","event_read","user_read","network_write","network_read","event_put","user_write","userevent_read","user_jwt"})
     */
    private $place;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"event_write","event_read","user_read","network_write","network_read","event_put","user_write","userevent_read","user_jwt"})
     */
    private $info;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"event_write","event_read","user_read","network_write","network_read","event_put","user_write","userevent_read","user_jwt"})
     */
    private $region;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"event_write","event_read","user_read","network_write","network_read","event_put","user_write","userevent_read","user_jwt"})
     */
    private $country;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): self
    {
        $this->info = $info;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): self
    {
        $this->event = $event;

        // set the owning side of the relation if necessary
        if ($event->getAddress() !== $this) {
            $event->setAddress($this);
        }

        return $this;
    }

    public function getNetwork(): ?Network
    {
        return $this->network;
    }

    public function setNetwork(Network $network): self
    {
        $this->network = $network;

        // set the owning side of the relation if necessary
        if ($network->getAddress() !== $this) {
            $network->setAddress($this);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        // set the owning side of the relation if necessary
        if ($user->getAddress() !== $this) {
            $user->setAddress($this);
        }

        return $this;
    }
}
