<?php

namespace App\State;

use stdClass;
use App\Entity\User;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserRoleChangeCheckerProcessor implements ProcessorInterface
{
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $currentUser = $this->security->getUser();

        $uow = $this->entityManager->getUnitOfWork();
        $originalUser = $uow->getOriginalEntityData( $data );
        
        if(!$originalUser){
            throw new \Exception('User not found');
        }

        $originalRoles = $originalUser['roles'];

        if(!in_array('ROLE_USER', $originalRoles)){
            array_push($originalRoles, 'ROLE_USER');
        }
        
        $newRoles = $data->getRoles();

        // If roles are being changed
        if ($originalRoles !== $newRoles && !in_array('ROLE_DIRECTOR', $currentUser->getRoles())) {
            throw new \Exception('Only directors can change roles.');
        }

        // Pass the data to the next processor
        return $data;
    }
}