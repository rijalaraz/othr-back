<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Validator\MediaUrl;
use Doctrine\Common\Collections\Collection;

class MediaUrlValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /**
         * @var MediaUrl
         */
        $mediaUrl = $constraint;

        if (null === $value || '' === $value) {
            return;
        }

        $url = $value;
        if(!$this->isBase64($url) && !$this->isValidUrl($url)) {
            $this->context->buildViolation($mediaUrl->message)
                ->setParameter('{{ value }}' , $url)
                ->addViolation();
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

    private function isValidUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED)) {
            return true;
        } else {
            return false;
        }
    }
}