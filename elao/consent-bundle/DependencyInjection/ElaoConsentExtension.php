<?php

/*
 * This file is part of the ElaoConsent bundle.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Bundle\ConsentBundle\DependencyInjection;

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

        $container->setParameter('elao_consent.cookie.name', $config['cookie']['name']);
        $container->setParameter('elao_consent.cookie.ttl', $config['cookie']['ttl']);
        $container->setParameter('elao_consent.consents', $config['consents']);

        $loader = new Loader\PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');
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
