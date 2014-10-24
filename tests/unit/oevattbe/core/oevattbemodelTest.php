<?php
/**
 * This file is part of OXID eSales PayPal module.
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
 * along with OXID eSales PayPal module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

/**
 * Testing oeVATTBEModel class.
 *
 * @covers oeVATTBEModel
 */
class Unit_oeVATTBE_Core_oeVATTBEModelTest extends OxidTestCase
{
    /**
     * Loading of data by id, returned by getId method
     */
    public function testLoadWhenIdIsSetToModel()
    {
        $sId = 'RecordIdToLoad';
        $aData = array('testkey' => 'testValue');
        $oGateway = $this->_createStub('TestGateway', array('load' => $aData));

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
        $aData = array('testkey' => 'testValue');
        $oGateway = $this->_createStub('TestGateway', array('load' => $aData));

        $oModel = $this->_getModel($oGateway);

        $this->assertTrue($oModel->load($sId));
        $this->assertEquals($aData, $oModel->getData());
    }

    /**
     * Is loaded method returns false when record does not exists in database
     */
    public function testIsLoadedWhenDatabaseRecordNotFound()
    {
        $oGateway = $this->_createStub('TestGateway', array('load' => null));

        $oModel = $this->_getModel($oGateway);
        $oModel->load();

        $this->assertFalse($oModel->isLoaded());
    }

    /**
     * Is loaded method returns false when record does not exists in database
     */
    public function testIsLoadedWhenDatabaseRecordFound()
    {
        $oGateway = $this->_createStub('TestGateway', array('load' => array('oeTBEVATId' => 'testId')));

        $oModel = $this->_getModel($oGateway);
        $oModel->load();

        $this->assertTrue($oModel->isLoaded());
    }

    /**
     * Is loaded method returns false when record does not exists in database
     */
    public function testClearingDataAfterDeletion()
    {
        $oGateway = $this->_createStub('TestGateway', array('delete' => true));

        $oModel = $this->_getModel($oGateway);
        $oModel->setData(array('some_field' => 'some_entry'));
        $oModel->delete();

        $this->assertEquals(array(), $oModel->getData());
    }

    /**
     * Creates oeVATTBEModel with mocked abstract methods
     *
     * @param object $oGateway
     * @param string $sId
     *
     * @return oeVATTBEModel
     */
    protected function _getModel($oGateway, $sId = null)
    {
        $oModel = oxNew('oeVATTBEModel', $oGateway);
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
