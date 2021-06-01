<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Elao\Bundle\ConsentBundle\Consent\ConsentStorage;
use Elao\Bundle\ConsentBundle\EventListener\ConsentEventSubscriber;
use Elao\Bundle\ConsentBundle\Renderer\ToastRenderer;

return function(ContainerConfigurator $configurator) {
    $services = $configurator
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->load('Elao\\Bundle\\ConsentBundle\\', '../../*')
        ->exclude('../../Resources')
    ;

    $services->set(ConsentStorage::class)->bind('$consents', '%elao_consent.consents%');
    $services->set(ToastRenderer::class)->bind('$consents', '%elao_consent.consents%');
    $services->set(ConsentEventSubscriber::class)->bind('$consents', '%elao_consent.consents%');
};
