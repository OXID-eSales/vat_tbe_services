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

// -------------------------------
// RESOURCE IDENTIFIER = STRING
// -------------------------------
$aLang = array(
    'charset'                                        => 'ISO-8859-15',
    'SHOP_MODULE_GROUP_oevattbe'                     => '[TR] VAT TBE services options',

    'SHOP_MODULE_aOeVATTBECountryEvidences'          => '[TR] Registered country evidence collectors.',
    'HELP_SHOP_MODULE_aOeVATTBECountryEvidences'     => '[TR] Country evidences can be marked as active (1) or inactive (0). Only active evidences are used in user country calculation. Evidences should not be removed from the list, only activation state should be changed when needed.',
    'SHOP_MODULE_sOeVATTBEDefaultEvidence'           => '[TR] Default evidence id.',
    'HELP_SHOP_MODULE_sOeVATTBEDefaultEvidence'      => '[TR] In case of contradicting evidences this evidence will be used when deciding user country. If user country can not be determined by default evidence, first registered evidence with country will be used.',
);
