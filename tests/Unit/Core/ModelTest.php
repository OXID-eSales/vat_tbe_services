<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Core;

use OxidEsales\EVatModule\Core\Model;
use OxidEsales\EVatModule\Model\DbGateway\ArticleVATGroupsDbGateway;
use OxidEsales\EVatModule\Model\DbGateway\CategoryVATGroupsDbGateway;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;
use OxidEsales\EVatModule\Model\DbGateway\OrderEvidenceListDbGateway;
use PHPUnit\Framework\TestCase;

/**
 * Testing Model class.
 */
class ModelTest extends TestCase
{
    public function gatewayProvider()
    {
        return [
            [ArticleVATGroupsDbGateway::class],
            [CategoryVATGroupsDbGateway::class],
            [CountryVATGroupsDbGateway::class],
            [OrderEvidenceListDbGateway::class],
        ];
    }

    /**
     * Loading of data by id, returned by getId method
     *
     * @dataProvider gatewayProvider
     */
    public function testLoadWhenIdIsSetToModel(string $gateway): void
    {
        $data = ['testkey' => 'testValue'];
        $gatewayMock = $this->createPartialMock($gateway, ['load']);
        $gatewayMock->expects($this->any())->method('load')->will($this->returnValue($data));
        $actualModel = $this->_getModel($gatewayMock, 'id-to-load');

        $this->assertTrue($actualModel->load());
        $this->assertEquals($data, $actualModel->getData());
    }

    /**
     * Loading of data by passed id
     *
     * @dataProvider gatewayProvider
     */
    public function testLoadWhenIdPassedIdViaParameter(string $gateway): void
    {
        $data = ['testkey' => 'testValue'];
        $gatewayMock = $this->createPartialMock($gateway, ['load']);
        $gatewayMock->expects($this->any())->method('load')->will($this->returnValue($data));
        $actualModel = $this->_getModel($gatewayMock);

        $this->assertTrue($actualModel->load('id-to-load'));
        $this->assertEquals($data, $actualModel->getData());
    }

    /**
     * Is loaded method returns false when record does not exists in database
     *
     * @dataProvider gatewayProvider
     */
    public function testIsLoadedWhenDatabaseRecordNotFound(string $gateway): void
    {
        $gatewayMock = $this->createPartialMock($gateway, ['load']);
        $gatewayMock->expects($this->any())->method('load')->will($this->returnValue(null));
        $actualModel = $this->_getModel($gatewayMock);

        $this->assertFalse($actualModel->load());
    }

    /**
     * @dataProvider gatewayProvider
     */
    public function testIsLoadedWhenDatabaseRecordFound(string $gateway): void
    {
        $data = ['oeTBEVATId' => 'testId'];
        $gatewayMock = $this->createPartialMock($gateway, ['load']);
        $gatewayMock->expects($this->any())->method('load')->will($this->returnValue($data));
        $actualModel = $this->_getModel($gatewayMock);

        $this->assertTrue($actualModel->load());
    }

    /**
     * @dataProvider gatewayProvider
     */
    public function testClearingDataAfterDeletion(string $gateway)
    {
        $gatewayMock = $this->createPartialMock($gateway, ['delete']);
        $gatewayMock->expects($this->any())->method('delete')->will($this->returnValue(true));
        $actualModel = $this->_getModel($gatewayMock);
        $actualModel->setData(['some_field' => 'some_entry']);
        $actualModel->delete();

        $this->assertEquals([], $actualModel->getData());
    }

    /**
     * Creates Model with mocked abstract methods
     *
     * @param $gateway
     * @param ?string $id
     * @return Model
     */
    protected function _getModel($gateway, string $id = null): Model
    {
        $model = oxNew(Model::class, $gateway);
        if ($id) {
            $model->setId($id);
        }

        return $model;
    }
}
