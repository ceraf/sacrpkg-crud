<?php

/*
 * This file is part of the Sacrpkg CrudBundle package.
 *
 * (c) Oleg Bruyako <jonsm2184@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace sacrpkg\CrudBundle\Model\Reader;

use Doctrine\Persistence\ManagerRegistry;
use sacrpkg\CrudBundle\Model\Paginator;
use sacrpkg\CrudBundle\Model\Filter;
use sacrpkg\CrudBundle\Model\Exceptions\ExceptionReaderFetch;

/**
 * Reader from doctrine database.
 */
class DoctrineReader implements ReaderInterface
{
    /**
     * @var ObjectManager
     */
    private $em;
    
    /**
     * Rows from database. 
     *
     * @var array
     */ 
    private $collection;
    
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager();
    }
    
    /**
     * {@inheritdoc}
     */
    public function fetch(Paginator $paginator, Filter $filter, string $entity_name): ReaderInterface
    {
        try {
            $repository = $this->em->getRepository($entity_name);
                            
            if (method_exists($repository, 'getGridCollection')) {
                $items = $repository->getGridCollection((clone $paginator)->incPage(), $filter);
                $total = ($paginator->getCurrPage()+1)*$paginator->getItemsOnPage() + count($items ?? []);
                $paginator->setTotal($total);
                $this->collection = $repository->getGridCollection($paginator, $filter);
            } else {
                $this->collection = $repository->findBy([]);
            }
        } catch (\Exception $e) {
            $this->collection = null;
            throw new ExceptionReaderFetch($e->getMessage());
        }
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCollection(): ?array
    {
        if (is_null($this->collection))
            $this->fetch();
            
        return $this->collection;
    }
}
