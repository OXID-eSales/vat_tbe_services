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
    'id'           => 'oevattbe',
    'title'        => 'VAT TBE services',
    'description'  => array(
        'de' => 'Module for VAT TBE Services.',
        'en' => 'Module for VAT TBE Services.',
    ),
    'thumbnail'    => 'logo.jpg',
    'version'      => '1.0.0',
    'author'       => 'OXID eSales AG',
    'url'          => 'http://www.oxid-esales.com',
    'email'        => 'info@oxid-esales.com',
    'extend'       => array(
        'oxarticle'         => 'oe/oevattbe/models/oevattbeoxarticle',
        'oxarticlelist'     => 'oe/oevattbe/models/oevattbeoxarticlelist',
    ),
    'files' => array(
        'oeVATTBEEvidenceSelector'         => 'oe/oevattbe/models/oevattbeevidenceselector.php',
        'oeVATTBEEvidenceCollector'        => 'oe/oevattbe/models/oevattbeevidencecollector.php',
        'oeVATTBEEvidence'                 => 'oe/oevattbe/models/evidences/oevattbeevidence.php',
        'oeVATTBEBillingCountryEvidence'   => 'oe/oevattbe/models/evidences/oevattbebillingcountryevidence.php',
        'oeVATTBEGeoLocationEvidence'      => 'oe/oevattbe/models/evidences/oevattbegeolocationevidence.php',
        'oeVATTBEEvidenceList'             => 'oe/oevattbe/models/evidences/oevattbeevidencelist.php',
        'oeVATTBEList'                     => 'oe/oevattbe/models/oevattbelist.php',
        'oeVatTbeEvents'                   => 'oe/oevattbe/core/oevattbeevents.php',
    ),
    'events'       => array(
        'onActivate'   => 'oeVatTbeEvents::onActivate',
        'onDeactivate' => 'oeVatTbeEvents::onDeactivate'
    ),
    'templates' => array(
    ),
    'blocks' => array(
    ),
    'settings' => array(
    )
);
