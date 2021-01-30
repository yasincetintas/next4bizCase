<?php


namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractService
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $em;

    /**
     * AbstractService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine');
    }

    protected function persist($entity)
    {
        $this->em->getManager()->persist($entity);
        $this->em->getManager()->flush();
    }

    /**
     * @param $json
     * @param $key
     * @param $search
     *
     * @return bool
     */
    protected function jsonSearch($json, $key, $search)
    {
        foreach ($json as $item) {
            if ($item->{$key} === $search) {
                return true;
            }
        }
        return false;
    }

    protected function saveJson($path, $data)
    {
        $fp = fopen($path, 'w');
        fwrite($fp, $this->container->get('serializer')->serialize($data, 'json'));
        fclose($fp);
    }
}
