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
use Symfony\Component\Asset\Packages;
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
    private Packages $packages;
    private array $consents;

    public function __construct(
        ConsentStorage $storage,
        ToastRenderer $renderer,
        CookieFactory $cookieFactory,
        Packages $packages,
        array $consents
    ) {
        $this->storage = $storage;
        $this->renderer = $renderer;
        $this->cookieFactory = $cookieFactory;
        $this->packages = $packages;
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
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->isMethod(Request::METHOD_POST) && $request->request->has('consents')) {
            $consents = array_map(function ($v) { return (bool) $v; }, $request->request->all('consents'));

            if (isset($consents['_all'])) {
                $consents = array_map(function () use ($consents) { return $consents['_all']; }, $this->consents);
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
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->storage->isUserConsentRequired()) {
            $this->injectConsentRequirement($event->getResponse());
            $this->injectConsentStyle($event->getResponse());
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

    private function injectConsentStyle(Response $response): void
    {
        $content = $response->getContent();
        $pos = strripos($content, '</head>');
        $style = "<link rel=\"stylesheet\" href=\"{$this->packages->getUrl('bundles/elaoconsent/css/style.css')}\">";
        $content = substr($content, 0, $pos).$style.substr($content, $pos);
        $response->setContent($content);
    }
}
