<?php

namespace AgilelabFr\CaptchaBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class CaptchaBundleExtension extends Extension
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('captcha_bundle.width', $config['width']);
        $container->setParameter('captcha_bundle.height', $config['height']);
        $container->setParameter('captcha_bundle.length', $config['length']);
        $container->setParameter('captcha_bundle.lines', $config['lines']);
    }
}
