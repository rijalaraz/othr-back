<?php

namespace App\Service;

use App\Entity\Media;

class MediaService {

    private $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function processMediaData(Media $media)
    {
        $base64 = $media->getUrl();
        if($this->isBase64($base64)) {
            $filename = $this->uploadService->uploadFile($base64, 'medias');
            $media->setUrl($filename);
        }
    }

    private function isBase64($strBase64)
    {
        if (empty($strBase64) || !preg_match("/;base64,/", $strBase64)) {
            return false;
        }
        
        $s = explode(';base64,', $strBase64)[1];

        // Check if there are valid base64 characters
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;
    
        // Decode the string in strict mode and check the results
        $decoded = base64_decode($s, true);
        if(false === $decoded) return false;
    
        // Encode the string again
        if(base64_encode($decoded) != $s) return false;
    
        return true;
    }

}