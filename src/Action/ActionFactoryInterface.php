<?php

/*
 * This file is part of the Sacrpkg CrudBundle package.
 *
 * (c) Oleg Bruyako <jonsm2184@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace sacrpkg\CrudBundle\Action;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Interface for action factory.
 */
interface ActionFactoryInterface
{
    /**
     * Get action object.
     *
     * @param string $action Action name 
     *
     * @return ActionAbstract
     */
    public function get(string $action): ActionAbstract;
}
