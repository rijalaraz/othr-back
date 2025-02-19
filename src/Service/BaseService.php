<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class BaseService
 * @package App\Service
 */
class BaseService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ContainerInterface
     */
    protected $contenaire;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * BaseService constructor.
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     */
    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container,TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->contenaire = $container;
        $this->tokenStorage = $tokenStorage;
    }
}