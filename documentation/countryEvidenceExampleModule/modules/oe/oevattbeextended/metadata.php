<?php
/**
 * This file is part of OXID eSales VAT TBE module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales VAT TBE module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.2';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'oevattbeextended',
    'title'        => 'OXID eShop eVAT location extender',
    'description'  => array(
        'de' => 'This module shows one of the possible ways to add new evidence collectors to OXID eShop eVAT module.',
        'en' => 'This module shows one of the possible ways to add new evidence collectors to OXID eShop eVAT module.',
    ),
    'version'      => '1.0.0',
    'author'       => 'OXID eSales AG',
    'url'          => 'http://www.oxid-esales.com',
    'email'        => 'info@oxid-esales.com',
    'files' => array(
        'oeVATTBEExtendedEvents'                    => 'oe/oevattbeextended/core/oevattbeextendedevents.php',
        'oeVATTBEExtendedCreditCardCountryEvidence' => 'oe/oevattbeextended/models/evidences/oevattbeextendedcreditcardcountryevidence.php',
    ),
    'events'       => array(
        'onActivate'   => 'oeVATTBEExtendedEvents::onActivate',
        'onDeactivate'   => 'oeVATTBEExtendedEvents::onDeactivate',
    ),
);
