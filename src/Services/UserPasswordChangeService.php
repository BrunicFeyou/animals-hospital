<?php

namespace App\Services;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\State\UserPasswordHasherProcessor;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class UserPasswordChangeService implements ProcessorInterface
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

        if ($data->getPlainPassword() && $currentUser !== $data) {
            throw new \Exception('You can only change your own password.');
        }

        if ($data->getPlainPassword() !== null && !!$data->getPlainPassword() == "") {
            throw new \Exception("The new password shouldn't be empty");
        }

        $data = $this->passwordHasherProcessor->process($data, $operation, $uriVariables, $context);

        return $data;
    }
}