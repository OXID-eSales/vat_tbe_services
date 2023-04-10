<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\VisualCmsModule\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEOrderEvidenceList.
 *
 * @covers OrderEvidenceList
 */
class OrderEvidenceListTest extends TestCase
{
    /**
     * Saves evidence list.
     */
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

    /**
     * Checks if evidence list data.
     */
    public function testLoadingEvidenceList()
    {
        $aData = array('evidence' => 'evidenceData');
        $oGateway = $this->_createStub('TestGateway', array('load' => $aData));

        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);
        $oList->load('order_id');

        $this->assertEquals($aData, $oList->getData());
    }

    /**
     * Deletes evidence list.
     */
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
