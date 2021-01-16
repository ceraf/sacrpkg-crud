<?php

namespace sacrpkg\CrudBundle\Action;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

interface ActionFactoryInterface
{
    public function get(string $action): ?ActionAbstract;
}
