<?php


namespace App\Service\Test;


use App\Entity\DateLog;
use App\Model\Request\DateCount;
use App\Service\AbstractService;

class TestService extends AbstractService
{
    public function getDateCount(DateCount $dateModel)
    {
        $functionName = $dateModel->getPeriod().'Count';

        return $this->em->getRepository(DateLog::class)->$functionName($dateModel->getDateRange());
    }
}