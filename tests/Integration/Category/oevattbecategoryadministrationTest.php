<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Testing VAT TBE administration in category page.
 *
 * @covers oeVATTBECategoryAdministration
 */
class Integration_oeVATTBE_category_oeVATTBECategoryAdministrationTest extends OxidTestCase
{
    /**
     * Check if view data is correct.
     */
    public function testViewData()
    {
        $aData1 = array(
            'oevattbe_id'          => '2',
            'oevattbe_countryid'   => 'a7c40f631fc920687.20179984',
            'oevattbe_name'        => 'Group Name1',
            'oevattbe_description' => 'Some description1',
            'oevattbe_rate'        => '20.50',
            'oevattbe_timestamp'   => '2014-10-24 09:46:11'
        );
        $aData2 = array(
            'oevattbe_id'          => '3',
            'oevattbe_countryid'   => 'a7c40f6323c4bfb36.59919433',
            'oevattbe_name'        => 'Group Name2',
            'oevattbe_description' => 'Some description2',
            'oevattbe_rate'        => '11.11',
            'oevattbe_timestamp'   => '2014-10-24 09:46:11'
        );
        $this->_cleanData();
        $this->_addData($aData1);
        $this->_addData($aData2);

        /** @var oeVATTBECategoryAdministration $oCategoryAdministration */
        $oCategoryAdministration = oxNew('oeVATTBECategoryAdministration');

        $oCountryVATGroup1 = oeVATTBECountryVATGroup::createInstance();
        $oCountryVATGroup1->setId(2);
        $oCountryVATGroup1->setData($aData1);

        $oCountryVATGroup2 = oeVATTBECountryVATGroup::createInstance();
        $oCountryVATGroup2->setId(3);
        $oCountryVATGroup2->setData($aData2);

        $aExpectedViewData = array(
            'a7c40f631fc920687.20179984' => array(
                'countryTitle' => 'Deutschland',
                'countryGroups' => array (
                    $oCountryVATGroup1
                ),
            ),
            'a7c40f6323c4bfb36.59919433' => array(
                'countryTitle' => 'Italien',
                'countryGroups' => array (
                    $oCountryVATGroup2
                ),
            ),
        );

        $this->assertEquals($aExpectedViewData, $oCategoryAdministration->getCountryAndVATGroupsData(), 'Data which should go to template is not correct.');
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function providerViewDataIsTBEService()
    {
        return array(
            /** TBE Service */
            array(1),
            /** Not TBE Service */
            array(0),
        );
    }

    /**
     * Check view data for correct value which shows if category is TBE.
     *
     * @param int $iIsTBECategory is tbe or not
     *
     * @dataProvider providerViewDataIsTBEService
     */
    public function testViewDataIsTBEService($iIsTBECategory)
    {
        /** @var oxCategory $oCategory */
        $oCategory = oxNew('oxCategory');
        $oCategory->setId('_testCategory');
        $oCategory->oxcategories__oevattbe_istbe = new oxField($iIsTBECategory);
        $oCategory->oxcategories__oxparentid = new oxField('oxrootid');
        $oCategory->save();

        /** @var oeVATTBECategoryAdministration $oCategoriesAdministration */
        $oCategoriesAdministration = oxNew('oeVATTBECategoryAdministration');
        $oCategoriesAdministration->setEditObjectId('_testCategory');

        $this->assertSame($iIsTBECategory, $oCategoriesAdministration->isCategoryTBE());
    }

    /**
     * Checks if selected option is saved rate.
     *
     * @return oeVATTBECategoryAdministration
     */
    public function testSelectedRateForCountry()
    {
        /** @var oeVATTBECategoryAdministration $oCategoryAdministration */
        $oCategoryAdministration = oxNew('oeVATTBECategoryAdministration');
        $aSelectParams = array(
            'a7c40f632e04633c9.47194042' => 2,
            '8f241f110955d3260.55487539' => ''
        );

        $this->setRequestParameter('VATGroupsByCountry', $aSelectParams);
        $oCategoryAdministration->setEditObjectId('_testCategory');
        $oCategoryAdministration->save();

        $this->assertSame(true, $oCategoryAdministration->isSelected('a7c40f632e04633c9.47194042', '2'));

        return $oCategoryAdministration;
    }

    /**
     * Checks if rate was not selected.
     *
     * @param oeVATTBECategoryAdministration $oCategoryAdministration controller
     *
     * @depends testSelectedRateForCountry
     *
     * @return oeVATTBECategoryAdministration
     */
    public function testNotSelectedRateForCountry($oCategoryAdministration)
    {
        $this->assertSame(false, $oCategoryAdministration->isSelected('8f241f110955d3260.55487539', ''));

        return $oCategoryAdministration;
    }

    /**
     * Checks if method returns correct value for non existing country.
     *
     * @param oeVATTBECategoryAdministration $oCategoryAdministration controller
     *
     * @depends testNotSelectedRateForCountry
     */
    public function testSelectionForNonExistingCountry($oCategoryAdministration)
    {
        $this->assertSame(false, $oCategoryAdministration->isSelected('NoneExistingId', '2'));
    }

    /**
     * Prepares VAT TBE groups data.
     *
     * @param array $aData
     */
    private function _addData($aData)
    {
        /** @var oeVATTBECountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');

        $oGateway->save($aData);
    }

    /**
     * Cleans current data.
     */
    private function _cleanData()
    {
        /** @var oeVATTBECountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        foreach ($oGateway->getList() as $aGroupInformation) {
            $oGateway->delete($aGroupInformation['OEVATTBE_ID']);
        }
    }
}
