<?php

namespace AgilelabFr\CaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('captcha_bundle');

        $rootNode = method_exists(TreeBuilder::class, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('captcha_bundle');

        $rootNode
            ->children()
                ->integerNode('width')->defaultValue(120)->end()
                ->integerNode('height')->defaultValue(40)->end()
                ->integerNode('length')->defaultValue(6)->end()
                ->integerNode('lines')->defaultValue(8)->end()
                ->scalarNode('characters')->defaultValue("ABCDEFGHJKLMNPQRSTUVWXYZ23456789")->end()
            ->end();

        return $treeBuilder;
    }
}
