<?php

namespace sacrpkg\CrudBundle\Model\Reader;

use Doctrine\Persistence\ManagerRegistry;
use sacrpkg\CrudBundle\Model\Paginator;
use sacrpkg\CrudBundle\Model\Filter;
use sacrpkg\CrudBundle\Model\Exceptions\ExceptionReaderFetch;

class DoctrineReader implements ReaderInterface
{
    private $em;
    private $collection;
    
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager();
    }
    
    public function fetch(Paginator $paginator, Filter $filter, string $entity_name): ReaderInterface
    {
       // $this->initSearch();

        try {
            $repository = $this->em->getRepository($entity_name);
                            
            if (method_exists($repository, 'getGridCollection')) {
                $items = $repository->getGridCollection((clone $paginator)->incPage(), $filter);
                $total = ($paginator->getCurrPage()+1)*$paginator->getItemsOnPage() + count($items ?? []);
                $paginator->setTotal($total);
                $this->collection = $repository->getGridCollection($paginator, $filter);
            } else {
                $this->collection = $repository->findBy([]);
                $paginator = null;
            }
        } catch (\Exception $e) {
            $this->collection = null;
            throw new ExceptionReaderFetch($e->getMessage());
        }
        return $this;
    }
    
    public function getCollection(): array
    {
        if (is_null($this->collection))
            $this->fetch();
            
        return $this->collection;
    }
}
