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

use sacrpkg\CrudBundle\Model\Paginator;
use sacrpkg\CrudBundle\Model\Filter;

/**
 * Interface for the paginator.
 */
interface ReaderInterface
{
    /**
     * Fetch data from database.
     *
     * @return $this
     */
    public function fetch(Paginator $paginator, Filter $filter, string $entity_name): ReaderInterface;
    
    /**
     * Get rows from database.
     *
     * @return array
     */
    public function getCollection(): ?array;
}
