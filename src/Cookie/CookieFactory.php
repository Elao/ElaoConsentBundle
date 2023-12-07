<?php

/*
 * This file is part of the ElaoConsent bundle.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Bundle\ConsentBundle\Cookie;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class CookieFactory
{
    private string $name;
    private int $ttl;

    public function __construct(string $name, int $ttl)
    {
        $this->name = $name;
        $this->ttl = $ttl;
    }

    public function create(array $consents): Cookie
    {
        return Cookie::create($this->name)
            ->withValue(json_encode($consents))
            ->withExpires((new \DateTime())->modify("+{$this->ttl} seconds"))
            ->withSameSite(Cookie::SAMESITE_NONE)
        ;
    }

    public function read(Request $request): array
    {
        return json_decode($request->cookies->get($this->name, '[]'), true);
    }
}
