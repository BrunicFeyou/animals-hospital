<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PatchUserProcessor implements ProcessorInterface
{
    private ProcessorInterface $userRoleChangeCheckerProcessor;
    private ProcessorInterface $userPasswordChangeCheckerProcessor;
    private ProcessorInterface $persistProcessor;

    public function __construct(
        #[Autowire(service: UserRoleChangeCheckerProcessor::class)]
        ProcessorInterface $userRoleChangeCheckerProcessor,
        #[Autowire(service: UserPasswordChangeCheckerProcessor::class)]
        ProcessorInterface $userPasswordChangeCheckerProcessor,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        ProcessorInterface $persistProcessor
    ) {
        $this->userRoleChangeCheckerProcessor = $userRoleChangeCheckerProcessor;
        $this->userPasswordChangeCheckerProcessor = $userPasswordChangeCheckerProcessor;
        $this->persistProcessor = $persistProcessor;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Process role change check
        $data = $this->userRoleChangeCheckerProcessor->process($data, $operation, $uriVariables, $context);

        // Process password change check
        $data = $this->userPasswordChangeCheckerProcessor->process($data, $operation, $uriVariables, $context);

        // Persist the data
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}