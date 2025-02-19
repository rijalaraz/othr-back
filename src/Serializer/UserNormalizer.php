<?php


namespace App\Serializer;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Security;
use App\Repository\UserRepository;


final class UserNormalizer implements NormalizerInterface, SerializerAwareInterface
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
        $data = $this->decorated->normalize($object, $format, $context);

        if ($object instanceof User && $this->security->getUser()) {

            $contextRelationship = $context;
            $contextRelationship['groups'] = ['user_user_read'];

            /**
             * @var User
             */
            $currentUser = $this->security->getUser();
            $currentUserId = $currentUser->getId();

            /**
             * @var UserRepository
             */
            $userRepository = $this->em->getRepository(User::class);
            $aUsers = $userRepository->filterByZone($currentUser);
            $aUserId = [];
            foreach ($aUsers as $user) {
                $aUserId[] = $user->getId();
            }

            if (isset($data['isFriend']) || isset($data['users']) || isset($data['teams']) || isset($data['swapableUsers'])) {
                $decorated = $this->decorated;

                $users = [];
                $teams = [];
                foreach ($object->getRelationships() as $rel) {
                    if(in_array($rel->getTargetUser()->getId(), $aUserId)) {
                        if (isset($data['isFriend']) && $currentUserId == $rel->getTargetUser()->getId()) {
                            $data['isFriend'] = true;
                        }
                        $users[] = $decorated->normalize($rel->getTargetUser(), $format, $contextRelationship);
                        if($rel->getTeam()) {
                            $teams[] = $decorated->normalize($rel->getTargetUser(), $format, $contextRelationship);
                        }
                    }
                }
                if (isset($data['users'])) {
                    $data['users'] = $users;
                }
                if (isset($data['teams'])) {
                    $data['teams'] = $teams;
                }

                if (isset($data['swapableUsers'])) {
                    $swapableUsers = [];
                    foreach ($object->getRelationships() as $rel) {
                        foreach ($rel->getTargetUser()->getRelationships() as $relationship) {
                            $swapUser = $decorated->normalize($relationship->getTargetUser(), $format, $contextRelationship);
                            if(!in_array($swapUser, $users) && $swapUser['id'] !== $object->getId() && !in_array($swapUser, $swapableUsers) && in_array($swapUser['id'], $aUserId)) {
                                $swapableUsers[] = $swapUser;
                                if(count($swapableUsers) == 10) {
                                    break 2;
                                }
                            }
                        }
                    }
                    $data['swapableUsers'] = $swapableUsers;
                }
            }

        }

        return $data;
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
