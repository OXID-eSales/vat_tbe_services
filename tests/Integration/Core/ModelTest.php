<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Core;

use OxidEsales\EVatModule\Core\Model;
use OxidEsales\EVatModule\Model\ArticleVATGroupsList;
use OxidEsales\EVatModule\Model\CategoryVATGroupsList;
use OxidEsales\EVatModule\Model\CountryVATGroup;
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

        $mockBuilder = $this->getMockBuilder($gateway);

        //CountryVATGroupsList set data differently
        if ($model == CountryVATGroupsList::class) {
            $gatewayMock = $mockBuilder
                ->onlyMethods(['load', 'getList'])
                ->getMock();

            $gatewayMock->expects($this->any())->method('load')->will($this->returnValue($data));
            $gatewayMock->expects($this->any())->method('getList')->will($this->returnValue($data));
        } else {
            $gatewayMock = $mockBuilder
                ->onlyMethods(['load'])
                ->getMock();

            $gatewayMock->expects($this->any())->method('load')->will($this->returnValue($data));
        }

        $actualModel = $this->getModel($model, $gatewayMock, 'id-to-load');

        if ($model == CountryVATGroupsList::class) {
            $this->assertIsArray($actualModel->load());

            $modelData = $actualModel->getData();
            $this->assertInstanceOf(CountryVATGroup::class, $modelData[0]);
            $this->assertSame(array_change_key_case($data[0], CASE_LOWER), $modelData[0]->getData());
        } else {
            $this->assertTrue($actualModel->load());
            $this->assertEquals($expected, $actualModel->getData());
        }
    }

    /**
     * Loading of data by passed id
     *
     * @dataProvider gatewayProvider
     */
    public function testLoadWhenIdPassedIdViaParameter(string $model, string $gateway, array $data): void
    {
        $expected = ['testkey' => 'testValue'];

        $mockBuilder = $this->getMockBuilder($gateway);

        //CountryVATGroupsList set data differently
        if ($model == CountryVATGroupsList::class) {
            $gatewayMock = $mockBuilder
                ->onlyMethods(['load', 'getList'])
                ->getMock();

            $gatewayMock->expects($this->any())->method('load')->will($this->returnValue($data));
            $gatewayMock->expects($this->any())->method('getList')->will($this->returnValue($data));
        } else {
            $gatewayMock = $mockBuilder
                ->onlyMethods(['load'])
                ->getMock();

            $gatewayMock->expects($this->any())->method('load')->will($this->returnValue($data));
        }

        $actualModel = $this->getModel($model, $gatewayMock);

        if ($model == CountryVATGroupsList::class) {
            $this->assertIsArray($actualModel->load('id-to-load'));

            $modelData = $actualModel->getData();
            $this->assertInstanceOf(CountryVATGroup::class, $modelData[0]);
            $this->assertSame(array_change_key_case($data[0], CASE_LOWER), $modelData[0]->getData());
        } else {
            $this->assertTrue($actualModel->load('id-to-load'));

            $this->assertEquals($expected, $actualModel->getData());
        }
    }

    /**
     * Is loaded method returns false when record does not exists in database
     *
     * @dataProvider gatewayProvider
     */
    public function testIsLoadedWhenDatabaseRecordNotFound(string $model, string $gateway): void
    {
        $mockedMethods = ['load'];
        if ($model == CountryVATGroupsList::class) {
            $mockedMethods[] = 'getList';
        }
        $gatewayMock = $this->createPartialMock($gateway, $mockedMethods);
        $gatewayMock
            ->expects($this->any())
            ->method('load')
            ->will($this->returnValue(null));
        $actualModel = $this->getModel($model, $gatewayMock);

        if ($model == CountryVATGroupsList::class) {
            $gatewayMock
                ->expects($this->any())
                ->method('getList')
                ->will($this->returnValue([]));
            $this->assertIsArray($actualModel->load());
            $this->assertEquals([], $actualModel->getData());
        } else {
            $this->assertFalse($actualModel->load());
        }
    }

    /**
     * @dataProvider gatewayProvider
     */
    public function testIsLoadedWhenDatabaseRecordFound(string $model, string $gateway, $data): void
    {
        $mockedMethods = ['load'];
        if ($model == CountryVATGroupsList::class) {
            $mockedMethods[] = 'getList';
        }
        $gatewayMock = $this->createPartialMock($gateway, $mockedMethods);
        $gatewayMock
            ->expects($this->any())
            ->method('load')
            ->will($this->returnValue($data));
        $actualModel = $this->getModel($model, $gatewayMock);

        if ($model == CountryVATGroupsList::class) {
            $gatewayMock
                ->expects($this->any())
                ->method('getList')
                ->will($this->returnValue($data));
            $this->assertIsArray($actualModel->load());
            $data = $actualModel->getData();
            $this->assertCount(1, $data);
            $this->assertInstanceOf(CountryVATGroup::class, $data[0]);
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
        $actualModel = $this->getModel($model, $gatewayMock);
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
    protected function getModel($modelClass, $gateway, string $id = null): Model
    {
        $model = oxNew($modelClass, $gateway);

        if ($id) {
            $model->setId($id);
        }

        return $model;
    }
}
