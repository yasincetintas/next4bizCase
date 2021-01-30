<?php
/**
 * Class Token
 * @package App\Utils
 * @author Yasin CETINTAS <ysnctnts@gmail.com>
 */

namespace App\Util\Lexik;

use Doctrine\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Token
{
    public $token;

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    /**
     * @var Request|null
     */
    private $request;
    /**
     * @var ObjectManager
     */
    private $em;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(ContainerInterface $container, JWTEncoderInterface $jwtEncoder, UserPasswordEncoderInterface $encoder)
    {
        $this->request = $container->get('request_stack')->getCurrentRequest();
        $this->jwtEncoder = $jwtEncoder;
        $this->em = $container->get('doctrine')->getManager();
        $this->encoder = $encoder;
    }

    public function extractor()
    {
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );
        $this->token = $extractor->extract($this->request);
        return $this;
    }

    public function generateToken($username, $roles)
    {
        return $this->jwtEncoder->encode(['roles' => $roles, 'username' => $username]);
    }
}
