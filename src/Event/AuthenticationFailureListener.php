<?php


namespace App\Event;

use App\Util\Lexik\Token;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationFailureListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Request|null
     */
    private $request;

    /**
     * @var Token
     */
    private $token;

    public function __construct(ContainerInterface $container, Token $token)
    {
        $this->container = $container;
        $this->request = $this->container->get('request_stack')->getCurrentRequest();
        $this->token = $token;
    }

    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        /**
         * @todo Logger yapılacak
         */
    }
}
