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
 * @copyright (C) OXID eSales AG 2003-2014T
 */

/**
 * Test class for oeVATTBEOrderEvidenceList.
 *
 * @covers oeVATTBEOrderEvidenceList
 */
class Unit_oeVATTBE_Models_oeVATTBEOrderEvidenceListTest extends OxidTestCase
{
    public function testSavingEvidenceList()
    {
        $aData = array('evidence' => 'evidenceData');
        $oGateway = $this->getMock('TestGateway', array('save'));
        $oGateway->expects($this->once())->method('save')->with(array('orderId' => 'order_id', 'evidenceList' => $aData));

        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);

        $oList->setId('order_id');
        $oList->setData($aData);

        $oList->save();
    }

    public function testLoadingEvidenceList()
    {
        $aData = array('evidence' => 'evidenceData');
        $oGateway = $this->_createStub('TestGateway', array('load' => $aData));

        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);
        $oList->load('order_id');

        $this->assertEquals($aData, $oList->getData());
    }

    public function testDeletingEvidenceList()
    {
        $oGateway = $this->getMock('TestGateway', array('load', 'delete'));
        $oGateway->expects($this->any())->method('load')->will($this->returnValue(array('someData')));
        $oGateway->expects($this->once())->method('delete')->with('order_id');

        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);
        $oList->load('order_id');

        $oList->delete();
    }

    /**
     * Creates stub object from given class
     *
     * @param string $sClass       Class name
     * @param array  $aMethods     Assoc array with method => value
     * @param array  $aTestMethods Array with test methods for mocking
     *
     * @return mixed
     */
    protected function _createStub($sClass, $aMethods, $aTestMethods = array())
    {
        $aMockedMethods = array_unique(array_merge(array_keys($aMethods), $aTestMethods));

        $oObject = $this->getMock($sClass, $aMockedMethods, array(), '', false);

        foreach ($aMethods as $sMethod => $sValue) {
            if (!in_array($sMethod, $aTestMethods)) {
                $oObject->expects($this->any())
                    ->method($sMethod)
                    ->will($this->returnValue($sValue));
            }
        }

        return $oObject;
    }
}
