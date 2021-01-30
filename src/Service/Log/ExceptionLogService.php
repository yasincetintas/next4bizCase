<?php

namespace App\Service\Log;

use Psr\Log\LoggerInterface;

class ExceptionLogService implements CustomLogInterface
{
    /**
     * @var LoggerInterface 
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $level
     * @param $message
     *
     * @return mixed
     */
    public function add($level, $message): void
    {
        $this->logger->$level($message);
    }
}