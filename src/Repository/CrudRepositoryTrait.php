<?php

namespace sacrpkg\CrudBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use sacrpkg\CrudBundle\Model\Paginator;

trait CrudRepositoryTrait
{
    public function getGridCollection(Paginator $paginator, $filter = null)
    {
        $qb = $this->getQBGrid(null, $filter);

        if ($paginator->getSortBy()) {
            $sortby = 'p.'.$paginator->getSortBy();
            $qb->orderBy($sortby, $paginator->getSortType());
        }

        $offset = $paginator->getCurrPage()*$paginator->getItemsOnPage();

        $query = $qb->setMaxResults($paginator->getItemsOnPage())
            ->setFirstResult($offset)
            ->getQuery();
        $res = $query->getResult();
    
        return $res;
    }
    
    protected function getQBGrid($search, $filter = null)
    {
        $qb = $this->createQueryBuilder('p');
			
        $qb = $this->applyFilter($qb, $filter);
            
        return $qb;
    }
    
    public function applyFilter($qb, $filter)
    {
        if ($filter['is_visible'] ?? null) {
            $qb->andWhere('p.status <> :val')
                    ->setParameter('val', 'removed');
        }

        $qb = $this->loadFilter($qb, $filter);

        return $qb; 
    }
    
    public function loadFilter($qb, $filter)
    {
        return $qb;
    }
}
