<?php

namespace App\State;

use App\Enum\AppointmentStateEnum;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use App\Services\VeterinarianAssignmentService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class UpdateAppointmentProcessor implements ProcessorInterface
{
    private ProcessorInterface $persistProcessor;
    private Security $security;
    private EntityManagerInterface $entityManager;
    private VeterinarianAssignmentService $veterinaryService;

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        ProcessorInterface $persistProcessor,
        Security $security,
        EntityManagerInterface $entityManager,
        VeterinarianAssignmentService $veterinaryService,
    ) {
        $this->persistProcessor = $persistProcessor;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->veterinaryService = $veterinaryService ;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $currentUser = $this->security->getUser();

        $uow = $this->entityManager->getUnitOfWork();
        $originalAppointment = $uow->getOriginalEntityData( $data );

        if(!$originalAppointment){
            throw new \Exception('Appointment not found');
        }

        if($originalAppointment['state'] == AppointmentStateEnum::done->name && in_array("ROLE_ASSISTANT",$currentUser->getRoles())){
            throw new \Exception('Assistants cannot update finished appointments');
        }

        if(
            in_array('ROLE_VETERINARIAN', $currentUser->getRoles()) && $originalAppointment['appointmentDate'] != $data->getAppointmentDate() ||
            in_array('ROLE_VETERINARIAN', $currentUser->getRoles()) && $originalAppointment['animal'] != $data->getAnimal() ||
            in_array('ROLE_VETERINARIAN', $currentUser->getRoles()) && $originalAppointment['reason'] != $data->getReason() ||
            in_array('ROLE_VETERINARIAN', $currentUser->getRoles()) && $originalAppointment['assistant'] != $data->getAssistant() ||
            in_array('ROLE_VETERINARIAN', $currentUser->getRoles()) && $originalAppointment['paymentStatus'] != $data->isPaymentStatus()
        ) {
            throw new Exception("Veterinarians can only change an appointment's veterinarian or its status") ;
        }
        

        $veterinarian = $data->getVeterinary();
        $originalVeterinarian = $originalAppointment['veterinary'];
        if($veterinarian != $originalVeterinarian){
            $this->veterinaryService->setVeterinary($data, $veterinarian);
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}