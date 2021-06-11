<?php

/*
 * This file is part of the ElaoConsent bundle.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Bundle\ConsentBundle\DependencyInjection;

use Elao\Bundle\ConsentBundle\Consent\ConsentStorage;
use Elao\Bundle\ConsentBundle\Cookie\CookieFactory;
use Elao\Bundle\ConsentBundle\EventListener\ConsentEventSubscriber;
use Elao\Bundle\ConsentBundle\Renderer\ToastRenderer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ElaoConsentExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        $container
            ->getDefinition(CookieFactory::class)
            ->replaceArgument('$name', $config['cookie']['name'])
            ->replaceArgument('$ttl', $config['cookie']['ttl'])
        ;

        $container->getDefinition(ConsentStorage::class)->replaceArgument('$consents', $config['consents']);
        $container->getDefinition(ToastRenderer::class)->replaceArgument('$consents', $config['consents']);
        $container->getDefinition(ConsentEventSubscriber::class)->replaceArgument('$consents', $config['consents']);
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->loadFromExtension('twig', [
            'paths' => [
                __DIR__.'/../Resources/templates' => 'ElaoConsentBundle',
            ],
        ]);
    }
}
