<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Core;

use OxidEsales\EVatModule\Core\Model;
use PHPUnit\Framework\TestCase;

/**
 * Testing Model class.
 *
 * @covers Model
 */
class ModelTest extends TestCase
{
    /**
     * Loading of data by id, returned by getId method
     */
    public function testLoadWhenIdIsSetToModel()
    {
        $sId = 'RecordIdToLoad';
        $aData = ['testkey' => 'testValue'];
        $oGateway = $this->_createStub('TestGateway', ['load' => $aData]);

        $oModel = $this->_getModel($oGateway, $sId);

        $this->assertTrue($oModel->load());
        $this->assertEquals($aData, $oModel->getData());
    }

    /**
     * Loading of data by passed id
     */
    public function testLoadWhenIdPassedIdViaParameter()
    {
        $sId = 'RecordIdToLoad';
        $aData = ['testkey' => 'testValue'];
        $oGateway = $this->_createStub('TestGateway', ['load' => $aData]);

        $oModel = $this->_getModel($oGateway);

        $this->assertTrue($oModel->load($sId));
        $this->assertEquals($aData, $oModel->getData());
    }

    /**
     * Is loaded method returns false when record does not exists in database
     */
    public function testIsLoadedWhenDatabaseRecordNotFound()
    {
        $oGateway = $this->_createStub('TestGateway', ['load' => null]);

        $oModel = $this->_getModel($oGateway);
        $oModel->load();

        $this->assertFalse($oModel->isLoaded());
    }

    /**
     * Is loaded method returns false when record does not exists in database
     */
    public function testIsLoadedWhenDatabaseRecordFound()
    {
        $oGateway = $this->_createStub('TestGateway', ['load' => ['oeTBEVATId' => 'testId']]);

        $oModel = $this->_getModel($oGateway);
        $oModel->load();

        $this->assertTrue($oModel->isLoaded());
    }

    /**
     * Is loaded method returns false when record does not exists in database
     */
    public function testClearingDataAfterDeletion()
    {
        $oGateway = $this->_createStub('TestGateway', ['delete' => true]);

        $oModel = $this->_getModel($oGateway);
        $oModel->setData(['some_field' => 'some_entry']);
        $oModel->delete();

        $this->assertEquals([], $oModel->getData());
    }

    /**
     * Creates Model with mocked abstract methods
     *
     * @param object $oGateway
     * @param string $sId
     *
     * @return Model
     */
    protected function _getModel($oGateway, $sId = null)
    {
        $oModel = oxNew(Model::class, $oGateway);
        if ($sId) {
            $oModel->setId($sId);
        }

        return $oModel;
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
    protected function _createStub($sClass, $aMethods, $aTestMethods = [])
    {
        $aMockedMethods = array_unique(array_merge(array_keys($aMethods), $aTestMethods));

        $oObject = $this->createPartialMock($sClass, $aMockedMethods);

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
