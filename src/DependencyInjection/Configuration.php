<?php

/*
 * This file is part of the ElaoConsent bundle.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Bundle\ConsentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('elao_consent');
        $rootNode = method_exists($treeBuilder, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('elao_consent');

        $rootNode
            ->canBeDisabled()
            ->children()
                ->arrayNode('cookie')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('name')
                            ->info('Cookie name')
                            ->defaultValue('consent')
                        ->end()
                        ->integerNode('ttl')
                            ->info('Cookie duration in seconds')
                            ->defaultValue(15_552_000) // 6 months
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('consents')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('label')->isRequired()->end()
                            ->scalarNode('description')->defaultNull()->end()
                        ->end()
                    ->end()
                    ->defaultValue([
                        'default' => [],
                    ])
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
