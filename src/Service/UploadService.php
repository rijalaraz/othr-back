<?php


namespace App\Service;

use Mimey\MimeTypes;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UploadService extends BaseService
{

    public function uploadFile($base64, $dir = null): string {
        
        $extension = $this->base64Extension($base64);

        $public = $this->contenaire->getParameter("public_path");
        $uploads = $this->contenaire->getParameter("upload_dir");

        $name = md5(uniqid(substr($base64, 0, 100), true));

        if($dir) {
            $subDir = $extension;
            $subDir .= '/'.rand(0, 9);
            $dir .= '/'.$subDir;
            $fileName = $uploads.'/'.$dir.'/'.$name.'.'.$extension;
        } else {
            $fileName = $uploads.'/'.$name.'.'.$extension;
        }

        $uploadDir = $public.'/'.$uploads;

        if($dir) {
            $uploadDir .= '/'. $dir;
        }
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0774, true);
        }

        $filePath = $public.'/'.$fileName;

        $this->base64ToImage($base64, $filePath);

        $this->fileSizeExceedsLimit($filePath);

        return $fileName;
    }

    private function base64Extension($str64) {

        $isBase64 = $this->isBase64String($str64);

        if(!$isBase64) {
            throw new BadRequestHttpException("Le format du fichier base64 n'est pas valide");
        }

        $mimeType = explode(':', explode(';base64,', $str64)[0])[1]; //  "image/jpeg"

        $mimes = new MimeTypes();
        $extension = $mimes->getExtension($mimeType);

        if(empty($extension)) {
            throw new BadRequestHttpException("Le format du fichier base64 n'est pas valide");
        }
       
        return $extension; 
    }

    private function base64ToImage($base64, $filePath) {
        $file = fopen($filePath, "wb");
    
        $data = explode(',', $base64);
    
        fwrite($file, base64_decode($data[1]));
        fclose($file);
    }

    private function isBase64String($strBase64){

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

    private function fileSizeExceedsLimit($filepath) {
        $uploadMaxFileSize = $this->contenaire->getParameter('upload_max_file_size');

        if(file_exists($filepath)) {
            $filesize = filesize($filepath);
            if($filesize > $uploadMaxFileSize) {
                unlink($filepath);
                throw new BadRequestHttpException(sprintf("La taille d'un fichier ne doit pas dépasser les %s", $this->formatBytes($uploadMaxFileSize,0)));
            }
        }
    }

    private function formatBytes($bytes, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
    
        $bytes /= pow(1024, $pow); 
    
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }

}