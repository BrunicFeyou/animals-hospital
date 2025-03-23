<?php
 
namespace App\EventListener;
 
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
 
class JWTCreatedListener
{
    public function __construct(private UserRepository $userRepository)
    {
    }
 
    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();
        $user = $this->userRepository->findOneByEmail($payload['username']);
 
        $payload['surname'] = $user->getSurname();
 
        $event->setData($payload);
    }
}