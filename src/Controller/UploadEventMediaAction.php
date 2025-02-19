<?php

namespace App\Controller;

use App\Entity\Event;
use App\Service\UploadService;

class UploadEventMediaAction
{
    private $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function __invoke(Event $data)
    {
        // image
        if(!empty($data->getImage())) {
            $base64 = $data->getImage()->getUrl();
            if(!is_file($base64)) {
                $filename = $this->uploadService->uploadFile($base64,'medias');
                $data->getImage()->setUrl($filename);
            }
        }

        // images
        if(!empty($data->getImages())) {
            $aImages = $data->getImages();
            foreach($aImages as $image) {
                $base64 = $image->getUrl();
                if(!is_file($base64)) {
                    $filename = $this->uploadService->uploadFile($base64,'medias');
                    $image->setUrl($filename);
                }
            }
        }

        return $data;
    }
}