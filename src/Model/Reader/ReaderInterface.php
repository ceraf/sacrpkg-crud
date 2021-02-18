<?php 

namespace sacrpkg\CrudBundle\Model\Reader;

use sacrpkg\CrudBundle\Model\Paginator;
use sacrpkg\CrudBundle\Model\Filter;

interface ReaderInterface
{
    public function fetch(Paginator $paginator, Filter $filter, string $entity_name): ReaderInterface;
    
    public function getCollection(): array;
}
