<?php

namespace sacrpkg\CrudBundle\DependencyInjection;

use sacrpkg\CrudBundle\Command\TestCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

class SacrpkgCrudExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('crud_actions.yaml');
        
        // создание определения команды
        $commandDefinition = new Definition(TestCommand::class);
        // добавление ссылок на отправителей в конструктор комманды
        
      //  foreach ($config['senders'] as $serviceId) {
      //      $commandDefinition->addArgument(new Reference($serviceId));
      //  }
        // регистрация сервиса команды как консольной команды
        $commandDefinition->addTag('console.command', ['command' => TestCommand::getCommanName()]);
        // установка определения в контейнер
        $container->setDefinition(TestCommand::class, $commandDefinition);
    }
}
