<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Post;
use App\Service\MediaService;

final class PostDataPersister implements ContextAwareDataPersisterInterface
{
    private $decorated;
    private $mediaService;

    public function __construct(ContextAwareDataPersisterInterface $decorated, MediaService $mediaService)
    {
        $this->decorated = $decorated;
        $this->mediaService = $mediaService;
    }

    public function supports($data, array $context = []): bool
    {
        return $this->decorated->supports($data, $context);
    }

    public function persist($data, array $context = [])
    {
        if (
            $data instanceof Post && (
                ($context['collection_operation_name'] ?? null) === 'post' ||
                ($context['item_operation_name'] ?? null) === 'put'
            )
        ) {
            // image
            if(!empty($data->getImage())) {
                $this->mediaService->processMediaData($data->getImage());
            }
        }

        $result = $this->decorated->persist($data, $context);

        return $result;
    }

    public function remove($data, array $context = [])
    {
        return $this->decorated->remove($data, $context);
    }
}