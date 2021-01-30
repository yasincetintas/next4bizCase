<?php

namespace App\Repository;

use App\Model\Request\DateRange;
use Doctrine\DBAL\Exception;

class DateLogRepository extends AbstractRepository
{
    /**
     * @param DateRange $date
     *
     * @return array|array[]
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     */
    public function dailyCount(DateRange $date): array
    {
        $dql = "select date(d) as date, count(dl.email) as email 
                FROM generate_series
                    ( :startDate::timestamp 
                    , :endDate::timestamp
                    , '1 day'::interval) d
                left join renta.renta.date_log dl on date(dl.send_date) = d 
                group by date order by date";

        $statement =  $this->getEntityManager()->getConnection()->prepare($dql);
        $statement->bindValue('startDate' , $date->getStart());
        $statement->bindValue('endDate' , $date->getEnd());

        $statement->execute();

        return $statement->fetchAllAssociative();
    }

    /**
     * @param DateRange $date
     *
     * @return array|array[]
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function weeklyCount(DateRange $date): array
    {
        $dql = "select date(d) || ' - ' || date((date_trunc('week',:endDate::timestamp) + '1 week'::interval)) as date, count(dl.email) as email
                FROM generate_series
                    ( date_trunc('week', :startDate::timestamp)::timestamp
                    , (date_trunc('week',:endDate::timestamp) + '1 week'::interval)::timestamp
                    , '7 day'::interval) d
                left join renta.renta.date_log dl on 
                    date(dl.send_date) >= date_trunc('week', d::timestamp) AND 
                    date(dl.send_date) < (date_trunc('week', d::timestamp) + '1 week'::interval)
                    AND 
                    date(dl.send_date) >= date(:startDate) AND 
                    date(dl.send_date) <= date(:endDate)
                group by date order by date";

        $statement =  $this->getEntityManager()->getConnection()->prepare($dql);
        $statement->bindValue('startDate' , $date->getStart());
        $statement->bindValue('endDate' , $date->getEnd());

        $statement->execute();

        return $statement->fetchAllAssociative();
    }

    /**
     * @param DateRange $date
     *
     * @return array
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function monthlyCount(DateRange $date): array
    {
        $dql = "select date(d) || ' - '|| date((date_trunc('month',d::timestamp)+ interval '1 month')) as date, count(dl.email) as email
                FROM generate_series
                    ( date_trunc('month', :startDate::timestamp)::timestamp
                    , (date_trunc('month',:endDate::timestamp)+ interval '1 month' - interval '1 day')::timestamp
                    , '1 month'::interval) d
                left join renta.renta.date_log dl on 
                    date(dl.send_date) > date_trunc('month', d::timestamp) AND 
                    date(dl.send_date) < (date_trunc('month', d::timestamp)+ interval '1 month' - interval '1 day')
                    AND 
                    date(dl.send_date) >= date(:startDate) AND 
                    date(dl.send_date) <= date(:endDate)
                group by date order by date";

        $statement =  $this->getEntityManager()->getConnection()->prepare($dql);
        $statement->bindValue('startDate' , $date->getStart());
        $statement->bindValue('endDate' , $date->getEnd());

        $statement->execute();

        return $statement->fetchAllAssociative();
    }


    /**
     * @param DateRange $date
     *
     * @return array
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function yearlyCount(DateRange $date): array
    {

        $dql = "select date(d) || ' - '|| date((date_trunc('year',d::timestamp)+ interval '1 year' - interval '1 day')) as date, count(dl.email)  as email
                FROM generate_series
                    ( date_trunc('year', :startDate::timestamp)::timestamp
                    , (date_trunc('year',:endDate::timestamp)+ interval '1 year' - interval '1 day')::timestamp
                    , '1 year'::interval) d
                left join renta.renta.date_log dl on 
                    date(dl.send_date) > date_trunc('year', d::timestamp) AND 
                    date(dl.send_date) < (date_trunc('year', d::timestamp)+ interval '1 year' - interval '1 day')
                    AND 
                    date(dl.send_date) >= date(:startDate) AND 
                    date(dl.send_date) <= date(:endDate)
                group by date order by date";

        $statement =  $this->getEntityManager()->getConnection()->prepare($dql);
        $statement->bindValue('startDate' , $date->getStart());
        $statement->bindValue('endDate' , $date->getEnd());

        $statement->execute();

        return $statement->fetchAllAssociative();
    }
}