<?php

/*
 * This file is part of the ElaoConsent bundle.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Bundle\ConsentBundle\Cookie;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class CookieFactory
{
    private string $name;
    private int $ttl;
    private LoggerInterface $logger;

    public function __construct(string $name, int $ttl, LoggerInterface $logger)
    {
        $this->name = $name;
        $this->ttl = $ttl;
        $this->logger = $logger;
    }

    public function create(array $consents): Cookie
    {
        return Cookie::create($this->name)
            ->withValue(json_encode($consents))
            ->withExpires((new \DateTime())->modify("+{$this->ttl} seconds"))
        ;
    }

    public function read(Request $request): array
    {
        $cookieValue = json_decode($request->cookies->get($this->name, '[]'), true);

        if (null === $cookieValue) {
            $this->logger->warning('Consent Bundle - Decoded cookie value return null', [
                'Cookie value' => $request->cookies->get($this->name),
            ]);

            return [];
        }

        return $cookieValue;
    }
}
