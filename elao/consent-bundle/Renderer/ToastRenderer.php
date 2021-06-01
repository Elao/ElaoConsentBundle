<?php

/*
 * This file is part of the ElaoConsent bundle.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Bundle\ConsentBundle\Renderer;

use Twig\Environment;

class ToastRenderer
{
    private Environment $twig;
    private array $consents;

    public function __construct(Environment $twig, array $consents)
    {
        $this->twig = $twig;
        $this->consents = $consents;
    }

    public function render(): string
    {
        return $this->twig->render('@ElaoConsentBundle/toast.html.twig', [
            'consents' => $this->consents,
        ]);
    }
}
