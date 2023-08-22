<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model\Evidence\Item;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Session;

abstract class Evidence
{
    private ?User $user;

    public function __construct(
        Session $session
    ) {
        $this->user = $session->getUser() ?: null;
    }

    /**
     * Returns evidence id.
     * Evidence id is shown in module configuration screen for admin to be able to active or deactivate this evidence.
     * It is also shown in order page if order has TBE articles and this evidence was used for country selection.
     */
    abstract public function getId(): string;

    /**
     * Calculates user country id and returns it.
     * For performance reasons country id should be cached locally,
     * so that country would not be checked on every call.
     */
    abstract public function getCountryId(): string;

    protected function getUser(): ?User
    {
        return $this->user;
    }
}
