<?php


namespace App\Model;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *  attributes={"pagination_enabled"=false},
 *  denormalizationContext={"groups"="home_write"},
 *  normalizationContext={"groups"="home_read"},
 *  collectionOperations={},
 *  itemOperations={
 *      "home_highlights"={
 *         "method"="GET",
 *         "path"="/home/{id}",
 *         "normalization_context"={"groups"={"home_read","user_others","event_high","network_home"}}
 *      },
 *     }
 * )
 */
class Home
{
    /**
     * @var string $id
     * @ApiProperty(identifier=true)
     */
    public $id;

    /**
     * @var array $events
     * @Groups({"home_read"})
     */
    public $events = [];

    /**
     * @var array $others
     * @Groups({"home_read"})
     */
    public $users = [];

    /**
     * @var array $networks
     * @Groups({"home_read"})
     */
    public $networks = [];

     /**
     * @var array $stats
     * @Groups({"home_read"})
     */
    public $stats = [];
}
