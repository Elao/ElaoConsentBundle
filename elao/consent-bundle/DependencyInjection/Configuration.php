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
                ->arrayNode('consents')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('description')->end()
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
