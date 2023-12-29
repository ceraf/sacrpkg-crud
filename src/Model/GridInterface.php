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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\RouterInterface;
use sacrpkg\CrudBundle\Model\Reader\ReaderInterface;
use sacrpkg\CrudBundle\Model\Render\RenderIntarface;

/**
 * Interface for the grid.
 */
interface GridInterface
{
    /**
     * Gets field setings.
     *
     * @return array
     */
    public function getFields(): array;
    
    /**
     * Set entity class name.
     *
     * @param string $entity
     *
     * @return $this
     */
    public function setEntity(string $entity): GridInterface;
    
    /**
     * Set filter data.
     *
     * @param array $data
     *
     * @return $this
     */
    public function setFilter(Array $data): GridInterface;
    
    /**
     * Set params for template.
     *
     * @param string $name Parameter name 
     * @param array $value Parameter value 
     *
     * @return $this
     */
    public function setParam(string $name, $value): GridInterface;
    
    /**
     * Set breadcrumb for template.
     *
     * @param array $breadcrumb 
     *
     * @return $this
     */
    public function setBreadcrumb(array $breadcrumb): GridInterface;    
    
    /**
     * Set template path.
     *
     * @param string $formview Template name 
     *
     * @return $this
     */
    public function setFormView(string $formview): GridInterface;
    
    /**
     * Generate response.
     *
     * @return Response
     */
    public function getResponse(): Response;
}
