<?php

namespace App\Services;

use App\Entity\User;
use App\Entity\Appointment;
use Symfony\Bundle\SecurityBundle\Security;

class VeterinarianAssignmentService
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function setVeterinary(Appointment $appointment, ?User $veterinary): void
    {
        
        if ($veterinary && !in_array('ROLE_VETERINARIAN', $veterinary->getRoles())) {
            $name = $veterinary->getName();
            throw new \ValueError("$name is not a user with ROLE_VETERINARIAN. Their roles are " . implode(", ", $veterinary->getRoles()));
        }
        
        $currentUser = $this->security->getUser();

        if ($veterinary && in_array('ROLE_VETERINARIAN', $currentUser->getRoles()) && $veterinary->getEmail() !== $currentUser->getEmail()) {
            throw new \ValueError("You cannot assign someone else as the veterinarian for an appointment.");
        }

        $appointment->setVeterinary($veterinary);
    }
}