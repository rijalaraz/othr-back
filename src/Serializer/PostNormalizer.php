<?php


namespace App\Serializer;

use App\Entity\Post;
use App\Entity\PostView;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Security;

final class PostNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    private $decorated;
    private $security;
    private $em;

    public function __construct(NormalizerInterface $decorated, Security $security, EntityManagerInterface $em)
    {
        $this->decorated = $decorated;
        $this->security = $security;
        $this->em = $em;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        if($object instanceof Post && $this->security->getUser()) {

            if(isset($context['item_operation_name']) &&  $context['item_operation_name']=="get") {
                $currentUser = $this->security->getUser();
                $status = false;
                foreach ($object->getPostViews() as $value) {
                    if($value->getUser()==$currentUser){
                        $status = true;
                        break;
                    }
                }

                if(!$status) {
                    $postView = new PostView();
                    $postView->setPost($object);
                    $postView->setUser($currentUser);
                    $this->em->persist($postView);
                    $this->em->flush();
                }
            }

        }

        return $this->decorated->normalize($object, $format, $context);
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
