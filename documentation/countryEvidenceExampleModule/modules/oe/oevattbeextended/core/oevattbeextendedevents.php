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
 * Class defines what module does on Shop events.
 */
class oeVATTBEExtendedEvents
{
    /**
     * Register new evidence to shop. Any number of evidences might be registered here.
     * oeVATTBEEvidenceRegister::registerEvidence() takes two parameters -
     * class name and whether to activate new evidence after registration.
     * As of default new evidences are not active.
     */
    public static function onActivate()
    {
        if (class_exists('oeVATTBEEvidenceRegister')) {
            $oConfig = oxRegistry::getConfig();
            /** @var oeVATTBEEvidenceRegister $oEvidenceRegister */
            $oEvidenceRegister = oxNew('oeVATTBEEvidenceRegister', $oConfig);
            $oEvidenceRegister->registerEvidence('oeVATTBEExtendedCreditCardCountryEvidence');
        }
    }

    /**
     * Unregister module evidences, which was registered on activation.
     * oeVATTBEEvidenceRegister::unregisterEvidence() takes two parameters -
     * evidence class name and the second is evidence id. If no evidence id is passed,
     * it is taken from evidence class, but evidence class must still be reachable at this point.
     */
    public static function onDeactivate()
    {
        if (class_exists('oeVATTBEEvidenceRegister')) {
            $oConfig = oxRegistry::getConfig();
            /** @var oeVATTBEEvidenceRegister $oEvidenceRegister */
            $oEvidenceRegister = oxNew('oeVATTBEEvidenceRegister', $oConfig);
            $oEvidenceRegister->unregisterEvidence('oeVATTBEExtendedCreditCardCountryEvidence');
        }
    }
}
