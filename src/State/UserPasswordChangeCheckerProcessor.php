<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class UserPasswordChangeCheckerProcessor implements ProcessorInterface
{
    private Security $security;
    private ProcessorInterface $passwordHasherProcessor;

    public function __construct(
        Security $security,
        #[Autowire(service: UserPasswordHasherProcessor::class)] ProcessorInterface $passwordHasherProcessor)
    {
        $this->security = $security;
        $this->passwordHasherProcessor = $passwordHasherProcessor;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $currentUser = $this->security->getUser();

        // Check if the current user is trying to change their own password
        if ($data->getPlainPassword() && $currentUser !== $data) {
            throw new \Exception('You can only change your own password.');
        }

        // dd($data->getPlainPassword(), $data->getPlainPassword() !== null);
        if ($data->getPlainPassword() !== null && !!$data->getPlainPassword() == "") {
            throw new \Exception("The new password shouldn't be empty");
        }

        // Delegate password hashing to UserPasswordHasherProcessor
        $data = $this->passwordHasherProcessor->process($data, $operation, $uriVariables, $context);

        // Pass the data to the next processor
        return $data;
    }
}