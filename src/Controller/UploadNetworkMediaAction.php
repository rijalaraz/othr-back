<?php

namespace App\Controller;

use App\Entity\Network;
use App\Entity\NetworkMember;
use App\Service\UploadService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class UploadNetworkMediaAction 
{
    private $uploadService;
    private $security;

    public function __construct(UploadService $uploadService, Security $security)
    {
        $this->uploadService = $uploadService;
        $this->security = $security;
    }

    public function __invoke(Network $data, Request $request)
    {

        if($request->getMethod() == 'PUT') {
            $canTheCurrentUserModifyThisNetwork = false;

            $currentUser = $this->security->getUser();

            $members = $data->getNetworkMembers();
            foreach($members as $member) {
                if($member->getType() == NetworkMember::TYPE_MAIN_ADMIN || $member->getType() == NetworkMember::TYPE_ADMIN) {
                    if($member->getUser() == $currentUser) {
                        $canTheCurrentUserModifyThisNetwork = true;
                    break;
                    }
                }
            }
            if(!$canTheCurrentUserModifyThisNetwork) {
                throw new \Exception("Seuls les administrateurs d'un réseau peuvent le modifier");
            }
        }

        // image
        if(!empty($data->getImage())) {
            $base64 = $data->getImage()->getUrl();
            if(!is_file($base64)) {
                $filename = $this->uploadService->uploadFile($base64,'medias');
                $data->getImage()->setUrl($filename);
            }
        }

        // logo
        if(!empty($data->getLogo())) {
            $base64 = $data->getLogo()->getUrl();
            if(!is_file($base64)) {
                $filename = $this->uploadService->uploadFile($base64,'medias');
                $data->getLogo()->setUrl($filename);
            }
        }

        // imageRepresentation
        if(!empty($data->getImageRepresentation())) {
            $base64 = $data->getImageRepresentation()->getUrl();
            if(!is_file($base64)) {
                $filename = $this->uploadService->uploadFile($base64,'medias');
                $data->getImageRepresentation()->setUrl($filename);
            }
        }

        // imageDescription
        if(!empty($data->getImageDescription())) {
            $base64 = $data->getImageDescription()->getUrl();
            if(!is_file($base64)) {
                $filename = $this->uploadService->uploadFile($base64,'medias');
                $data->getImageDescription()->setUrl($filename);
            }
        }

        return $data;
    }

} 