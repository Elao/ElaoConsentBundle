<?php

/*
 * This file is part of the ElaoConsent bundle.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Bundle\ConsentBundle\Consent;

class ConsentStorage
{
    private array $consents = [];
    private array $userConsents = [];
    private bool $userConsentRequired = false;

    public function __construct(array $consents)
    {
        $this->consents = $consents;
    }

    public function isUserConsentRequired(): bool
    {
        return $this->userConsentRequired;
    }

    public function requireUserConsent(): self
    {
        $this->userConsentRequired = true;

        return $this;
    }

    public function hasUserConsent(string $name = 'default'): bool
    {
        if (!isset($this->consents[$name])) {
            throw new \Exception("dsf");
        }

        $consent = $this->userConsents[$name] ?? null;

        if (null === $consent) {
            $this->requireUserConsent();
        }

        return (bool) $consent;
    }

    public function setUserConsents(array $userConsents): self
    {
        foreach ($userConsents as $key => $value) {
            $this->userConsents[$key] = $value;
        }

        return $this;
    }
}
