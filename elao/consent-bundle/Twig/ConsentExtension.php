<?php

/*
 * This file is part of the ElaoConsent bundle.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Bundle\ConsentBundle\Twig;

use Elao\Bundle\ConsentBundle\Consent\ConsentStorage;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConsentExtension extends AbstractExtension
{
    private ConsentStorage $storage;

    public function __construct(ConsentStorage $storage)
    {
        $this->storage = $storage;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('has_user_consent', [$this->storage, 'hasUserConsent']),
        ];
    }
}
