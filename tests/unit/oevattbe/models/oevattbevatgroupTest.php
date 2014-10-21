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
 * Testing oeVATTBEVATGroup class.
 * @covers oeVATTBEVATGroup
 */
class Unit_oeVatTbe_models_oeVATTBEVATGroupTest extends OxidTestCase
{
    /**
     * Information is set to Group entity;
     * Correct information array is passed to gateway for saving.
     */
    public function testSavingVATGroup()
    {
        $aExpectedData = array(
            'oevattbe_countryid' => '8f241f11095410f38.37165361',
            'oevattbe_name' => 'Group Name',
            'oevattbe_description' => 'Some description',
            'oevattbe_rate' => 20.50
        );

        $oGateway = $this->getMock('oeVATTBEVATGroupsDbGateway', array('save'));
        $oGateway->expects($this->once())->method('save')->with($aExpectedData);

        /** @var oeVATTBEVATGroup $oGroup */
        $oGroup = oxNew('oeVATTBEVATGroup', $oGateway);

        $oGroup->setCountryId('8f241f11095410f38.37165361');
        $oGroup->setName('Group Name');
        $oGroup->setDescription('Some description');
        $oGroup->setRate(20.50);

        $oGroup->save();
    }

    /**
     * No information is set to group;
     * Correct information array is passed to gateway for saving.
     */
    public function testSavingVATGroupWithNoData()
    {
        $oGateway = $this->getMock('oeVATTBEVATGroupsDbGateway', array('save'));
        $oGateway->expects($this->once())->method('save')->with(null);

        /** @var oeVATTBEVATGroup $oGroup */
        $oGroup = oxNew('oeVATTBEVATGroup', $oGateway);

        $oGroup->save();
    }

    /**
     * Group information is provided by gateway;
     * All information is correctly taken.
     */
    public function testLoadingVATGroup()
    {
        $aData = array(
            'OEVATTBE_ID' => 99,
            'OEVATTBE_COUNTRYID' => '8f241f11095410f38.37165361',
            'OEVATTBE_NAME' => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE' => '20.50',
            'OEVATTBE_TIMESTAMP' => '2014-05-05 18:00:00',
        );

        $oGateway = $this->_createStub('oeVATTBEVATGroupsDbGateway', array('load' => $aData));

        /** @var oeVATTBEVATGroup $oGroup */
        $oGroup = oxNew('oeVATTBEVATGroup', $oGateway);
        $oGroup->load(99);

        $this->assertSame('8f241f11095410f38.37165361', $oGroup->getCountryId());
        $this->assertSame('Group Name', $oGroup->getName());
        $this->assertSame('Some description', $oGroup->getDescription());
        $this->assertSame('20.50', $oGroup->getRate());
    }
}
