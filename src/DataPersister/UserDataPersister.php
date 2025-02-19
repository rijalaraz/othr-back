<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use App\Service\MediaService;

final class UserDataPersister implements ContextAwareDataPersisterInterface
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
            $data instanceof User && (
                ($context['collection_operation_name'] ?? null) === 'post' ||
                ($context['item_operation_name'] ?? null) === 'put'
            )
        ) {

            // logo
            if(!empty($data->getLogo())) {
                $this->mediaService->processMediaData($data->getLogo());
            }

            // image
            if(!empty($data->getImage())) {
                $this->mediaService->processMediaData($data->getImage());
            }

            // activityImages
            if(!empty($data->getActivityImages())) {
                foreach ($data->getActivityImages() as $activityImage) {
                    $this->mediaService->processMediaData($activityImage);
                }
            }

            // achievements
            if(!empty($data->getAchievements())) {
                foreach($data->getAchievements() as $achievement) {
                    $this->mediaService->processMediaData($achievement);
                }
            }

            // customers
            if(!empty($data->getCustomers())) {
                foreach ($data->getCustomers() as $customer) {
                    $this->mediaService->processMediaData($customer);
                }
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