<?php

namespace App\Event;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class AuthenticationSuccessListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user->isActive() && $user->getDeletedAt() != null){
            throw new UnsupportedUserException('Pasif Kullanıcı');
        }

        $data['user'] = [
            "user" => $user->getUsername(),
            "email" => $user->getEmail(),
            "name" => $user->getName(),
            "surname" => $user->getSurname(),
        ];

        $event->setData($data);
    }
}
