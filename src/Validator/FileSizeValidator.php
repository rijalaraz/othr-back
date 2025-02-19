<?php

namespace App\Validator;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FileSizeValidator extends ConstraintValidator
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\FileSize */

        if (null === $value || '' === $value) {
            return;
        }

        $public = $this->container->getParameter("public_path");

        $uploadMaxFileSize = $this->container->getParameter('upload_max_file_size');

        $filename = $value->getUrl();

        $filepath = $public.'/'.$filename;
        if(file_exists($filepath)) {
            $filesize = filesize($filepath);
            if($filesize > $uploadMaxFileSize) {
                unlink($filepath);
                // TODO: implement the validation here
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatBytes($uploadMaxFileSize,0))
                    ->addViolation();
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
