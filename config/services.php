<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Elao\Bundle\ConsentBundle\Consent\ConsentStorage;
use Elao\Bundle\ConsentBundle\Cookie\CookieFactory;
use Elao\Bundle\ConsentBundle\EventListener\ConsentEventSubscriber;
use Elao\Bundle\ConsentBundle\Renderer\ToastRenderer;
use Elao\Bundle\ConsentBundle\Twig\ConsentExtension;
use Symfony\Component\Asset\Packages;

return function(ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set(CookieFactory::class)
        ->args([
            '$name' => abstract_arg('cookie.name'),
            '$ttl' => abstract_arg('cookie.ttl'),
        ])
    ;

    $services
        ->set(ConsentStorage::class)
        ->args([
            '$consents' => abstract_arg('consents'),
        ])
    ;

    $services
        ->set(ToastRenderer::class)
        ->args([
            '$twig' => service('twig'),
            '$consents' => abstract_arg('consents'),
        ])
    ;

    $services
        ->set(ConsentEventSubscriber::class)
        ->tag('kernel.event_subscriber')
        ->args([
            '$storage' => service(ConsentStorage::class),
            '$renderer' => service(ToastRenderer::class),
            '$cookieFactory' => service(CookieFactory::class),
            '$packages' => service(Packages::class),
            '$consents' => abstract_arg('consents')
        ]);

    $services
        ->set(ConsentExtension::class)
        ->tag('twig.extension')
        ->args([
            '$storage' => service(ConsentStorage::class),
        ])
    ;
};
