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
 * Interface for the filter.
 */
interface FilterInterface
{
    /**
     * Init filter.
     *
     * @param array $settings Filter fields 
     * @param array $data Filter default data 
     *
     * @return FilterInterface
     */
    public function init(array $settings, array $data): FilterInterface;
    
    /**
     * Get filter data.
     *
     * @return array
     */
    public function getData(): ?array;
    
    /**
     * Set route to grid.
     *
     * @param string $route 
     *
     * @return $this
     */
    public function setGridRoute(string $route): FilterInterface;
    
    /**
     * Generate url to grid.
     *
     * @return string
     */
    public function getFilterUrl(): string;
    
    /**
     * Flag is use filter.
     *
     * @return bool
     */
    public function isUseFilter(): bool;

    /**
     * Flag is use field in filter.
     *
     * @param string $field_name 
     *
     * @return bool
     */
    public function hasField(string $field_name): bool;
    
    /**
     * Get filter fields settings.
     *
     * @return array
     */
    public function getSettings(): ?Array;
}
