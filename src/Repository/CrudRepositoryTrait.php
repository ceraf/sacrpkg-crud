<?php

namespace sacrpkg\CrudBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use sacrpkg\CrudBundle\Model\Paginator;
use sacrpkg\CrudBundle\Model\Filter;

trait CrudRepositoryTrait
{
    public function getGridCollection(Paginator $paginator, Filter $filter)
    {
        $this->beforeCrudCreateQueryBuilder($paginator, $filter);
        
        $qb = $this->getQBGrid($filter, $paginator);

        $qb = $this->afterCrudCreateQueryBuilder($qb, $paginator, $filter);

        if ($paginator->getSortBy()) {
            $sortby = 'p.'.$paginator->getSortBy();
            $qb->orderBy($sortby, $paginator->getSortType());
        }

        $offset = $paginator->getCurrPage()*$paginator->getItemsOnPage();

        $qb->setMaxResults($paginator->getItemsOnPage())
            ->setFirstResult($offset);

        $qb = $this->afterCrudApplyPaginator($qb, $paginator, $filter);

        $qb = $this->applyFilter($qb, $filter->getData());

        $qb = $this->afterCrudApplyFilter($qb, $paginator, $filter);

        $query = $qb->getQuery();
        $res = $query->getResult();
    
        return $res;
    }
    
    protected function getQBGrid(Filter $filter, Paginator $paginator): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');
 
        return $qb;
    }
    
    protected function applyFilter($qb, $filterdata)
    {
        if ($filterdata['is_visible'] ?? null) {
            $qb->andWhere('p.status <> :val')
                    ->setParameter('val', 'removed');
        }

        $qb = $this->loadFilter($qb, $filterdata);

        return $qb; 
    }
    
    protected function loadFilter($qb, $filterdata)
    {
        return $qb;
    }
    
    protected function beforeCrudCreateQueryBuilder(Paginator $paginator, Filter $filter): ServiceEntityRepository
    {
        return $this;
    }
    
    protected function afterCrudCreateQueryBuilder(QueryBuilder $qb, Paginator $paginator, Filter $filter): QueryBuilder
    {
        return $qb;
    }
    
    protected function afterCrudApplyPaginator(QueryBuilder $qb, Paginator $paginator, Filter $filter): QueryBuilder
    {
        return $qb;
    }
    
    protected function afterCrudApplyFilter(QueryBuilder $qb, Paginator $paginator, Filter $filter): QueryBuilder
    {
        return $qb;
    }  
}
