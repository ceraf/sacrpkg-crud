<?php

/*
 * This file is part of the Sacrpkg CrudBundle package.
 *
 * (c) Oleg Bruyako <jonsm2184@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace sacrpkg\CrudBundle\Model;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

/**
 * Interface for the paginator.
 */
interface PaginatorInterface
{
    /**
     * Set grid.
     *
     * @param GridInterface $grid
     *
     * @return $this
     */
    public function setGrid(GridInterface $grid): PaginatorInterface; 
    
    /**
     * Set fields list to sort.
     *
     * @param array $sort_fields
     *
     * @return $this
     */
    public function setSortFields(Array $sort_fields): PaginatorInterface;
    
    /**
     * Get param by name.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getParam(string $name);
    
    /**
     * Set parameter.
     *
     * @param string $name Parameter name 
     * @param array $value Parameter value 
     *
     * @return $this
     */
    public function setParam(string $name, string  $value): PaginatorInterface;
    
    /**
     * Initialization filter.
     *
     * @param int $items_on_page Number items on page 
     * @param string $sortby Field name for default sort.
     * @param string $sorttype Default sort type.
     * @param string $grid_route Route to grid
     *
     * @return $this
     */
    public function init(int $items_on_page, string $sortby,
            string $sorttype, string $grid_route): PaginatorInterface;

    /**
     * Get number lines.
     * @return int
     */
    public function getTotal(): ?int;
    
    /**
     * Set number lines.
     * @return $this
     */
    public function setTotal($total): PaginatorInterface;

    /**
     * Get number pages.
     *
     * @return int
     */
    public function getNumPages(): ?int;
    
    /**
     * Increase page by 1 .
     * @return $this
     */
    public function incPage(): PaginatorInterface;
    
    /**
     * Get route grid.
     *
     * @return string
     */
    public function getRoute(): string;
    
    /**
     * Get filed name for sort.
     *
     * @return string
     */
    public function getSortBy(): string;
    
    /**
     * Get type of sort.
     *
     * @return string
     */
    public function getSortType(): string;
    
    /**
     * Get number current page.
     *
     * @return int
     */
    public function getCurrPage(): int;
    
    /**
     * Get number of itemson page.
     *
     * @return int
     */
    public function getItemsOnPage(): int;
    
    /**
     * Get flag use paginator.
     *
     * @return bool
     */
    public function isUse(): bool;
    
    
    /**
     * Get url previos page.
     *
     * @return string
     */
    public function getPrevUrl(): ?string;
    
    /**
     * Get url next page.
     *
     * @return string
     */
    public function getNextUrl(): ?string;
}
