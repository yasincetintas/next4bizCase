<?php

namespace App\Repository;

use Doctrine\ORM\Mapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use App\Response\RepositoryResponse;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

abstract class AbstractRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
    }

    /**
     * @var array
     */
    private $orderBy=[];

    /**
     * @var int
     */
    private $resultCount = 0;

    /**
     * @param QueryBuilder $queryBuilder
     * @param array|null $limit
     *
     * @return QueryBuilder
     */
    public function addLimitToQueryBuilder(QueryBuilder $queryBuilder, array $limit = null)
    {
        $limit = $limit ?? ['offset' => 0, 'limit' => 20];
        if ($limit != null) {
            if (isset($limit['offset']) && isset($limit['limit'])) {
                $queryBuilder->setFirstResult($limit['offset']);
                $queryBuilder->setMaxResults($limit['limit']);
            }
        }
        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array|null $orderBy
     * @param string|null $alias
     *
     * @return QueryBuilder
     */
    public function addOrderByToQueryBuilder(QueryBuilder $queryBuilder, array $orderBy = null, string $alias = null)
    {
        $orderBy = $orderBy ?? [];
        if (count($orderBy) < 1) {
            return $queryBuilder;
        }

        foreach ($orderBy as $index => $orderStr) {
            if (intval($index)!=$index && in_array($index, ['lt','-','<'])) {
                $direction='DESC';
                $orderColumn=$orderStr;
            } else {
                $directionIndicator = substr($orderStr, 0, 1);
                $orderColumn = str_replace(['+', '-'], '', $orderStr);
                $direction = $directionIndicator == '-' ? 'DESC' : 'ASC';
            }

            $indexArr = explode('.', $orderColumn);
            if (is_array($indexArr) && count($indexArr)>1) {
                $tableColumn = $indexArr[0];
                unset($indexArr[0]);
                $jsonColumn =implode('.', $indexArr);
                $queryBuilder->addOrderBy("JSON_EXTRACT(".$alias.".".$tableColumn.", '$.$jsonColumn') ", $direction);
            } else {
                $queryBuilder->addOrderBy($alias.".".$orderColumn, $direction);
            }
        }

        return $queryBuilder;
    }

    public function filterToBuilderParser(QueryBuilder &$qb, $alias, $filters)
    {
        foreach ($filters as $index => $filter) {

            if ($index == 'limit' && !is_null($filter)) {
                $this->addLimitToQueryBuilder($qb, $filters) ;
                continue;
            }

            if ($index == 'sort' && !is_null($filter)) {
                $this->addOrderByToQueryBuilder($qb, $filters['sort'], $alias);
                continue;
            }

            if ($index == 'offset' && !is_null($filter)) {
                continue;
            }

            /**
             * ?like[policyNumber]=ASD
             * ?like[policyNumber.plate]=ASD ---> JSON
             */
            if ($index == 'like' && !is_null($filter)) {
                $this->addLikeByToQueryBuilder($qb, $filters['like'], $alias);
                continue;
            }

            if (is_array($filter)) {
                $whereIn = [];

                foreach ($filter as $i => $v) {
                    switch ((string)$i) {
                        case 'gt':
                        case 'lt':
                        case 'gte':
                        case 'lte':
                        case 'eq':
                            $function = $i;
                            break;
                        default:
                            $function = 'in';
                            if ($v!=='') {
                                $whereIn[] = $v;
                            }
                    }

                    if ($function!='in') {
                        if (strripos($index, 'date') !== false) {
                            $time = ($function == 'gte') ? ' 00:00:00' : ' 23:59:59';
                            $time = ($function == 'eq' || $function=='gt' || $function=='lt') ? '' : $time;

                            $qb->andWhere($qb->expr()->{$function}($alias.'.'.$index, ':'.$index.$i))
                                ->setParameter($index.$i, date('Y-m-d'.$time, strtotime($v)));
                        } else {
                            $qb->andWhere($qb->expr()->{$function}($alias.'.'.$index, ':'.$index.$i))
                                ->setParameter($index.$i, $v);
                        }
                    }
                }
                if (count($whereIn)>0) {
                    $qb->andWhere($qb->expr()->in($alias.'.'.$index, ':'.$index))
                        ->setParameter($index, $whereIn[0]);
                }
            } else {
                if ($filter!="") {
                    if (strpos($filter, "~")>-1) {
                        $filter = "%".str_replace('~', '', $filter)."%";
                        $qb->andWhere($qb->expr()->like($alias.'.'.$index, ':'.$index))->setParameter($index, $filter);
                    } else {
                        $qb->andWhere($qb->expr()->eq($alias.'.'.$index, ':'.$index))->setParameter($index, $filter);
                    }
                }
            }
        }
    }

    /**
     * @param QueryBuilder $qb
     * @param $filter
     * @param $alias
     *
     * @return QueryBuilder
     */
    private function addLikeByToQueryBuilder(QueryBuilder &$qb, $filter, $alias)
    {
        $arg=[];
        foreach ($filter as $index => $value) {
            if (strpos($index, '.')) {
                $indexArr = explode('.', $index);
                $tableColumn = $indexArr[0];
                unset($indexArr[0]);
                $jsonColumn = implode('.', $indexArr);
                $qb->
                andWhere($qb->expr()->isNotNull("JSON_SEARCH(".$alias.".".$tableColumn.", '$.$jsonColumn','$value') "));
                continue;
            }

            if (strpos($index, '|')) {
                $expr = $qb->expr()->orX();
                $filterIndex = explode('|', $index);
                foreach ($filterIndex as $findex) {
                    $arg[] = $qb->expr()->like($alias.'.'.$findex, ':'.str_replace('|', '', $index));
                }

                $qb->andWhere($expr->addMultiple($arg))->setParameter(str_replace('|', '', $index), '%'.$value.'%');
                continue;
            }

            if (strpos($index, '&')) {
                $expr = $qb->expr()->andX();
                $filterIndex = explode('&', $index);

                foreach ($filterIndex as $findex) {
                    $arg[] = $qb->expr()->like($alias.'.'.$findex, ':'.str_replace('&', '', $index));
                }

                $qb->andWhere($expr->addMultiple($arg))->setParameter(str_replace('&', '', $index), '%'.$value.'%');
                continue;
            }

            $qb->andWhere($qb->expr()->like($alias.'.'.$index, ':'.$index))->setParameter($index, '%'.$value.'%');
        }

        return $qb;
    }

    /**
     * @param $column
     *
     * @return $this
     */
    public function addOrderBy($column)
    {
        if (is_array($column)) {
            $this->orderBy = $column;
        } else {
            $this->orderBy[] = $column;
        }

        return $this;
    }

    /**
     * @param array $criteria
     * @param bool $returnQB
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     *
     * @return RepositoryResponse|QueryBuilder
     */
    public function findWithFilter(array $criteria, $returnQB = false)
    {
        $qb = $this->createQueryBuilder('s');
        $this->filterToBuilderParser($qb, 's', $criteria);

        if (count($this->orderBy)>0) {
            foreach ($this->orderBy as $orderBy) {
                list($alias, $column) = strpos($orderBy, '.')>-1 ? explode('.', str_replace(['+','-'], ['',''], $orderBy)) : ['s',str_replace(['+','-'], ['',''], $orderBy)];
                $qb->addOrderBy($alias.'.'.$column, strpos($orderBy, '-')>-1 ? 'DESC' : 'ASC');
            }
        }

        $data = $qb->getQuery()->getResult();

        if (!$returnQB) {
            unset($criteria['limit']);
            unset($criteria['offset']);
            unset($criteria['sort']);

            $qb2 = $this->createQueryBuilder('s');

            $this->filterToBuilderParser($qb2, 's', $criteria);

            $qb2->select("count(s) as total");

            $count = intval($qb2->getQuery()->getSingleResult()["total"]);

            $this->resultCount = $count;
        }

        return $returnQB ? $qb : new RepositoryResponse($data, $count);
    }

    /**
     * @param QueryBuilder $qb
     * @param $alias
     *
     * @return RepositoryResponse
     */
    public function response(QueryBuilder $qb, $alias)
    {
        $qbCount = clone $qb;

        $result = $qb->getQuery()->getResult();
        $qbCount->setMaxResults(null);
        $qbCount->setFirstResult(null);
        $qbCount->select("count(".$alias.") as total");
        $count = $qbCount->getQuery()->getScalarResult()[0]['total'];
        $this->resultCount = $count;

        return new RepositoryResponse($result, $count);
    }

    /**
     * @param QueryBuilder $qb
     * @param string $alias
     *
     * @return int|mixed
     */
    public function getResultCount(QueryBuilder $qb, $alias = 's')
    {
        $qbCount = clone $qb;

        $result = $qb->getQuery()->getResult();
        $qbCount->setMaxResults(null);
        $qbCount->setFirstResult(null);
        $qbCount->select("count(".$alias.") as total");
        $count = $qbCount->getQuery()->getScalarResult()[0]['total'];
        $this->resultCount = $count;
        return $this->resultCount;
    }

    /**
     * @param $entity
     */
    public function remove($entity)
    {
        $this->getDoctrine()->getManager()->remove($entity);
        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * @param $entity
     * @throws \Exception
     */
    public function softDelete($entity)
    {
        $entity->setDeletedAt(new \DateTime('now'));
        self::persist($entity);
    }

    /**
     * @param $entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function persist($entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

}