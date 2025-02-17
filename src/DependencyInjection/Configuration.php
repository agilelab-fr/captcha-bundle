<?php

namespace AgilelabFr\CaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('captcha_bundle');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->integerNode('width')->end()
                ->integerNode('height')->end()
                ->integerNode('length')->end()
                ->integerNode('lines')->end()
                ->scalarNode('characters')->end()
            ->end();

        return $treeBuilder;
    }
}
