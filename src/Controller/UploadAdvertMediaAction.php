<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Service\UploadService;

class UploadAdvertMediaAction
{
    private $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function __invoke(Advert $data)
    {
        // image
        if (!empty($data->getImage())) {
            $base64 = $data->getImage()->getUrl();
            $filename = $this->uploadService->uploadFile($base64, 'medias');
            $data->getImage()->setUrl($filename);
        }

        return $data;
    }
}
