<?php

/*
 * This file is part of the ElaoConsent bundle.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Bundle\ConsentBundle\EventListener;

use Elao\Bundle\ConsentBundle\Consent\ConsentStorage;
use Elao\Bundle\ConsentBundle\Cookie\CookieFactory;
use Elao\Bundle\ConsentBundle\Renderer\ToastRenderer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ConsentEventSubscriber implements EventSubscriberInterface
{
    private ConsentStorage $storage;
    private ToastRenderer $renderer;
    private CookieFactory $cookieFactory;
    private array $consents;

    public function __construct(
        ConsentStorage $storage,
        ToastRenderer $renderer,
        CookieFactory $cookieFactory,
        array $consents,
    ) {
        $this->storage = $storage;
        $this->renderer = $renderer;
        $this->cookieFactory = $cookieFactory;
        $this->consents = $consents;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [
                ['onRequest', 1000],
            ],
            ResponseEvent::class => 'onResponse',
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->isMethod(Request::METHOD_POST) && $request->request->get('consents')) {
            $consents = array_map(fn ($v) => (bool) $v, $request->request->get('consents'));

            if (isset($consents['_all'])) {
                $consents = array_map(fn () => $consents['_all'], $this->consents);
            }

            $response = new RedirectResponse($request->getUri());
            $response->headers->setCookie($this->cookieFactory->create($consents));

            $event->setResponse($response);
            $event->stopPropagation();
            return;
        }

        $consents = $this->cookieFactory->read($request);
        $this->storage->setUserConsents($consents);
    }

    public function onResponse(ResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->storage->isUserConsentRequired()) {
            $this->injectConsentRequirement($event->getResponse());
        }
    }

    private function injectConsentRequirement(Response $response): void
    {
        $content = $response->getContent();
        $pos = strripos($content, '</body>');
        $toast = $this->renderer->render();
        $content = substr($content, 0, $pos).$toast.substr($content, $pos);
        $response->setContent($content);
    }
}
