<?php

/**
 * This file is part of OXID eSales eVAT module.
 *
 * OXID eSales eVAT module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
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
