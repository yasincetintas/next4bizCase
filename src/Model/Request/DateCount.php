<?php


namespace App\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

class DateCount
{
    const PERIODS = ['daily', 'weekly', 'monthly', 'yearly'];

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Choice(choices=DateCount::PERIODS, message="Choose a valid period.")
     */
    public $period;

    /**
     * @var DateRange
     * @Assert\NotBlank
     * @Assert\Type("App\Model\Request\DateRange")
     * @Assert\Valid()
     */
    public $dateRange;

    /**
     * @return string
     */
    public function getPeriod(): string
    {
        return $this->period;
    }

    /**
     * @param $period
     */
    public function setPeriod($period): void
    {
        $this->period = $period;
    }

    /**
     * @return DateRange
     */
    public function getDateRange(): DateRange
    {
        return $this->dateRange;
    }

    /**
     * @param DateRange $dateRange
     */
    public function setDateRange(DateRange $dateRange): void
    {
        $this->dateRange = $dateRange;
    }
}