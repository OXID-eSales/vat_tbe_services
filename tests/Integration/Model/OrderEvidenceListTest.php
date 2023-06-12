<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Model;

use OxidEsales\EVatModule\Model\DbGateway\OrderEvidenceListDbGateway;
use OxidEsales\EVatModule\Model\OrderEvidenceList;
use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEOrderEvidenceList.
 */
class OrderEvidenceListTest extends TestCase
{
    /**
     * Saves evidence list.
     */
    public function testSavingEvidenceList()
    {
        $aData = ['evidence' => 'evidenceData'];
        $oGateway = $this->createPartialMock(OrderEvidenceListDbGateway::class, ['save']);
        $oGateway->expects($this->once())->method('save')->with(['orderId' => 'order_id', 'evidenceList' => $aData]);

        /** @var OrderEvidenceList $oList */
        $oList = oxNew(OrderEvidenceList::class, $oGateway);

        $oList->setId('order_id');
        $oList->setData($aData);

        $oList->save();
    }

    /**
     * Checks if evidence list data.
     */
    public function testLoadingEvidenceList()
    {
        $aData = ['evidence' => 'evidenceData'];
        $oGateway = $this->_createStub(['load' => $aData]);

        /** @var OrderEvidenceList $oList */
        $oList = oxNew(OrderEvidenceList::class, $oGateway);
        $oList->load('order_id');

        $this->assertEquals($aData, $oList->getData());
    }

    /**
     * Deletes evidence list.
     */
    public function testDeletingEvidenceList()
    {
        $oGateway = $this->createPartialMock(OrderEvidenceListDbGateway::class, ['load', 'delete']);
        $oGateway->expects($this->any())->method('load')->will($this->returnValue(['someData']));
        $oGateway->expects($this->once())->method('delete')->with('order_id');

        /** @var OrderEvidenceList $oList */
        $oList = oxNew(OrderEvidenceList::class, $oGateway);
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
    protected function _createStub($aMethods, $aTestMethods = [])
    {
        $aMockedMethods = array_unique(array_merge(array_keys($aMethods), $aTestMethods));

        $oObject = $this->createPartialMock(OrderEvidenceListDbGateway::class, $aMockedMethods);

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
