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

/**
 * Test class for oeVATTBEEvidenceCalculator.
 *
 * @covers oeVATTBEEvidenceCollector
 */
class Unit_oeVATTBE_Models_Evidences_oeVATTBEEvidenceCollectorTest extends OxidTestCase
{

    /**
     * Evidence is registered;
     * Evidence is set to be active;
     * Evidence should appear in evidence list.
     */
    public function testGetEvidencesWhenEvidencesExistAndIsActive()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', array('billing_country' => 1));
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEBillingCountryEvidence'));

        /** @var oxUser|PHPUnit_Framework_MockObject_MockObject $oUser */
        $oUser = $this->getMock('oeVATTBEOxUser', array(), array(), '', false);

        $oExpectedEvidence = oxNew('oeVATTBEBillingCountryEvidence', $oUser);
        $oEvidenceList = oxNew('oeVATTBEEvidenceList');
        $oEvidenceList->add($oExpectedEvidence);

        $oCollector = oxNew('oeVATTBEEvidenceCollector', $oUser, $oConfig);
        $this->assertEquals($oEvidenceList, $oCollector->getEvidenceList());
    }

    /**
     * Evidence is registered;
     * Evidence is set to be not active;
     * Empty evidence list should be returned.
     */
    public function testGetEvidencesWhenEvidenceExistsButIsNotActive()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', array('billing_country' => 0));
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEBillingCountryEvidence'));

        /** @var oxUser|PHPUnit_Framework_MockObject_MockObject $oUser */
        $oUser = $this->getMock('oeVATTBEOxUser', array(), array(), '', false);

        $oEvidenceList = oxNew('oeVATTBEEvidenceList');

        $oCollector = oxNew('oeVATTBEEvidenceCollector', $oUser, $oConfig);
        $this->assertEquals($oEvidenceList, $oCollector->getEvidenceList());
    }

    /**
     * Evidence is registered;
     * Active evidence list is empty;
     * Registered evidence should be put to active evidences list and set to be not active.
     */
    public function testGetEvidencesWhenEvidenceIsRegisteredButActiveEvidencesListIsEmpty()
    {
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', array());
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEBillingCountryEvidence'));

        /** @var oxUser|PHPUnit_Framework_MockObject_MockObject $oUser */
        $oUser = $this->getMock('oeVATTBEOxUser', array(), array(), '', false);
        $oCollector = oxNew('oeVATTBEEvidenceCollector', $oUser, $oConfig);
        $oCollector->getEvidenceList();

        $this->assertEquals(array('billing_country' => 0), $oConfig->getConfigParam('aOeVATTBECountryEvidences'));
    }

    /**
     * No evidences are registered;
     * No evidences are set to be active;
     * Empty evidence list should be returned.
     */
    public function testGetEvidencesWhenNoEvidencesAreSet()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', array());
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array());

        /** @var oxUser|PHPUnit_Framework_MockObject_MockObject $oUser */
        $oUser = $this->getMock('oeVATTBEOxUser', array(), array(), '', false);

        $oEvidenceList = oxNew('oeVATTBEEvidenceList');

        $oCollector = oxNew('oeVATTBEEvidenceCollector', $oUser, $oConfig);
        $this->assertEquals($oEvidenceList, $oCollector->getEvidenceList());
    }

    /**
     * No evidences are registered;
     * Active evidence list contains non registered evidence;
     * Evidence should be removed from active evidences list.
     */
    public function testGetEvidencesWhenNoEvidenceIsRegisteredButActiveEvidenceListIsNotEmpty()
    {
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', array('non_existing_id' => 1));
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array());

        /** @var oxUser|PHPUnit_Framework_MockObject_MockObject $oUser */
        $oUser = $this->getMock('oeVATTBEOxUser', array(), array(), '', false);
        $oCollector = oxNew('oeVATTBEEvidenceCollector', $oUser, $oConfig);
        $oCollector->getEvidenceList();

        $this->assertEquals(array(), $oConfig->getConfigParam('aOeVATTBECountryEvidences'));
    }

    /**
     * Non existing evidence is registered;
     * Non existing evidence is set to be active;
     * Empty evidence list should be returned.
     */
    public function testGetEvidencesWhenEvidencesDoesNotExists()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', array('non_existing_id' => 1));
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('NonExistingEvidenceClass'));

        /** @var oxUser|PHPUnit_Framework_MockObject_MockObject $oUser */
        $oUser = $this->getMock('oeVATTBEOxUser', array(), array(), '', false);

        $oEvidenceList = oxNew('oeVATTBEEvidenceList');

        $oCollector = oxNew('oeVATTBEEvidenceCollector', $oUser, $oConfig);
        $this->assertEquals($oEvidenceList, $oCollector->getEvidenceList());
    }
}
