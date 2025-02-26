<?php

namespace AgilelabFr\CaptchaBundle;

use AgilelabFr\CaptchaBundle\DependencyInjection\CaptchaBundleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AgilelabFrCaptchaBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new CaptchaBundleExtension();
    }

}
