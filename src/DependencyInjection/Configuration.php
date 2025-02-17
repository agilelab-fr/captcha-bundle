<?php

namespace AgilelabFr\CaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('captcha_bundle');

        $treeBuilder->getRootNode()
            ->children()
            ->arrayNode('agilelab_fr_captcha')
            ->children()
            ->integerNode('width')->defaultValue(120)->end()
            ->integerNode('height')->defaultValue(40)->end()
            ->integerNode('length')->defaultValue(6)->end()
            ->integerNode('lines')->defaultValue(8)->end()
            ->scalarNode('characters')->defaultValue('ABCDEFGHJKLMNPQRSTUVWXYZ23456789')->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}