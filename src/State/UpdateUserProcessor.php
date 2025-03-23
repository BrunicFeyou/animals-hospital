<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Services\UserPasswordChangeService;
use App\Services\UserRoleChangeService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class UpdateUserProcessor implements ProcessorInterface
{
    private UserPasswordChangeService $userPasswordChangeService;
    private UserRoleChangeService $userRoleChangeService;
    private ProcessorInterface $persistProcessor;

    public function __construct(
        UserRoleChangeService $userRoleChangeService,
        UserPasswordChangeService $userPasswordChangeService,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        ProcessorInterface $persistProcessor
    ) {
        $this->userRoleChangeService = $userRoleChangeService;
        $this->userPasswordChangeService = $userPasswordChangeService;
        $this->persistProcessor = $persistProcessor;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $data = $this->userRoleChangeService->process($data, $operation, $uriVariables, $context);

        $data = $this->userPasswordChangeService->process($data, $operation, $uriVariables, $context);

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}