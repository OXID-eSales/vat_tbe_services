<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Core;

use OxidEsales\EVatModule\Core\Model;
use OxidEsales\EVatModule\Model\ArticleVATGroupsList;
use OxidEsales\EVatModule\Model\CategoryVATGroupsList;
use OxidEsales\EVatModule\Model\CountryVATGroupsList;
use OxidEsales\EVatModule\Model\OrderEvidenceList;
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
            [
                'model' => ArticleVATGroupsList::class,
                'gateway' => ArticleVATGroupsDbGateway::class,
                'data' => [
                    [
                        'OEVATTBE_COUNTRYID' => 'testkey',
                        'OEVATTBE_VATGROUPID' => 'testValue',
                    ]
                ],
            ],
            [
                'model' => CategoryVATGroupsList::class,
                'gateway' => CategoryVATGroupsDbGateway::class,
                'data' => [
                    [
                        'OEVATTBE_COUNTRYID' => 'testkey',
                        'OEVATTBE_VATGROUPID' => 'testValue',
                    ]
                ],
            ],
            [
                'model' => CountryVATGroupsList::class,
                'gateway' => CountryVATGroupsDbGateway::class,
                'data' => [
                    [
                        'OEVATTBE_COUNTRYID' => 'testkey',
                        'OEVATTBE_VATGROUPID' => 'testValue',
                    ]
                ],
            ],
            [
                'model' => OrderEvidenceList::class,
                'gateway' => OrderEvidenceListDbGateway::class,
                'data' => [
                    'testkey' => 'testValue'
                ],
            ],
        ];
    }

    /**
     * Loading of data by id, returned by getId method
     *
     * @dataProvider gatewayProvider
     */
    public function testLoadWhenIdIsSetToModel(string $model, string $gateway, array $data): void
    {
        $expected = ['testkey' => 'testValue'];

        $gatewayMock = $this->createPartialMock($gateway, ['load']);
        $gatewayMock
            ->expects($this->any())
            ->method('load')
            ->will($this->returnValue($data));

        if ($model == CountryVATGroupsList::class) {
            $gatewayMock->method('getList')->will($this->returnValue('data'));
        }

        $actualModel = $this->_getModel($model, $gatewayMock, 'id-to-load');

        if ($model == CountryVATGroupsList::class) {
            $this->assertIsArray($actualModel->load());
        } else {
            $this->assertTrue($actualModel->load());
        }

        $this->assertEquals($expected, $actualModel->getData());
    }

    /**
     * Loading of data by passed id
     *
     * @dataProvider gatewayProvider
     */
    public function testLoadWhenIdPassedIdViaParameter(string $model, string $gateway, array $data): void
    {
        $expected = ['testkey' => 'testValue'];

        $gatewayMock = $this->createPartialMock($gateway, ['load']);
        $gatewayMock
            ->expects($this->any())
            ->method('load')
            ->will($this->returnValue($data));
        $actualModel = $this->_getModel($model, $gatewayMock);

        if ($model == CountryVATGroupsList::class) {
            $this->assertIsArray($actualModel->load('id-to-load'));
        } else {
            $this->assertTrue($actualModel->load('id-to-load'));
        }

        $this->assertEquals($expected, $actualModel->getData());
    }

    /**
     * Is loaded method returns false when record does not exists in database
     *
     * @dataProvider gatewayProvider
     */
    public function testIsLoadedWhenDatabaseRecordNotFound(string $model, string $gateway): void
    {
        $gatewayMock = $this->createPartialMock($gateway, ['load']);
        $gatewayMock
            ->expects($this->any())
            ->method('load')
            ->will($this->returnValue(null));
        $actualModel = $this->_getModel($model, $gatewayMock);

        if ($model == CountryVATGroupsList::class) {
            $this->assertIsArray($actualModel->load());
        } else {
            $this->assertFalse($actualModel->load());
        }
    }

    /**
     * @dataProvider gatewayProvider
     */
    public function testIsLoadedWhenDatabaseRecordFound(string $model, string $gateway, $data): void
    {
        $gatewayMock = $this->createPartialMock($gateway, ['load']);
        $gatewayMock
            ->expects($this->any())
            ->method('load')
            ->will($this->returnValue($data));
        $actualModel = $this->_getModel($model, $gatewayMock);

        if ($model == CountryVATGroupsList::class) {
            $this->assertIsArray($actualModel->load());
        } else {
            $this->assertTrue($actualModel->load());
        }
    }

    /**
     * @dataProvider gatewayProvider
     */
    public function testClearingDataAfterDeletion(string $model, string $gateway)
    {
        $gatewayMock = $this->createPartialMock($gateway, ['delete']);
        $gatewayMock
            ->expects($this->any())
            ->method('delete')
            ->will($this->returnValue(true));
        $actualModel = $this->_getModel($model, $gatewayMock);
        $actualModel->setData(['some_field' => 'some_entry']);
        $actualModel->delete();

        $this->assertEquals([], $actualModel->getData());
    }

    /**
     * Creates Model with mocked abstract methods
     *
     * @param $modelClass
     * @param $gateway
     * @param ?string $id
     * @return Model
     */
    protected function _getModel($modelClass, $gateway, string $id = null): Model
    {
        $model = oxNew($modelClass, $gateway);

        if ($id) {
            $model->setId($id);
        }

        return $model;
    }
}
