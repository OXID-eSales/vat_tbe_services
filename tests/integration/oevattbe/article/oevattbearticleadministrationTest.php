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
 * Testing VAT TBE administration in article page.
 */
class Integration_oeVATTBE_article_oeVATTBEArticleAdministrationTest extends OxidTestCase
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
        $this->_prepareData($aData1, $aData2);

        /** @var oeVATTBEArticleAdministration $oArticleAdministration */
        $oArticleAdministration = oxNew('oeVATTBEArticleAdministration');
        $oArticleAdministration->render();
        $aViewData = $oArticleAdministration->getViewData();

        $oCountryVATGroup1 = oeVATTBECountryVATGroup::createCountryVATGroup();
        $oCountryVATGroup1->setId(2);
        $oCountryVATGroup1->setData($aData1);

        $oCountryVATGroup2 = oeVATTBECountryVATGroup::createCountryVATGroup();
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

        $this->assertEquals($aExpectedViewData, $aViewData['aCountriesAndVATGroups'], 'Data which should go to template is not correct.');
    }

    public function providerIsTBEService()
    {
        return array(
            array(1),
            array(0),
        );
    }

    /**
     * @param $iIsTBEArticle
     *
     * @dataProvider providerIsTBEService
     */
    public function testIsTBEService($iIsTBEArticle)
    {
        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = oxNew('oeVATTBEOxArticle');
        $oArticle->setId('_testArticle');
        $oArticle->oxarticles__oevattbe_istbeservice = new oxField($iIsTBEArticle);
        $oArticle->save();

        /** @var oeVATTBEArticleAdministration $oArticleAdministration */
        $oArticleAdministration = oxNew('oeVATTBEArticleAdministration');
        $oArticleAdministration->setEditObjectId('_testArticle');
        $oArticleAdministration->render();
        $aViewData = $oArticleAdministration->getViewData();

        $this->assertSame("$iIsTBEArticle", $aViewData['iIsTbeService']);
    }

    /**
     * Prepares VAT TBE groups data.
     *
     * @param array $aData1
     * @param array $aData2
     */
    private function _prepareData($aData1, $aData2)
    {
        /** @var oeVATTBECountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        foreach ($oGateway->getList() as $aGroupInformation) {
            $oGateway->delete($aGroupInformation['OEVATTBE_ID']);
        }

        $oGateway->save($aData1);
        $oGateway->save($aData2);
    }
}
