<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Article;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EVatModule\Controller\Admin\ArticleAdministration;
use OxidEsales\EVatModule\Model\CountryVATGroup;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;

/**
 * Testing VAT TBE administration in article page.
 *
 * @covers oeVATTBEArticleAdministration
 */
class ArticleAdministrationTest extends TestCase
{
    use ServiceContainer;

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

        /** @var ArticleAdministration $oArticleAdministration */
        $oArticleAdministration = oxNew(ArticleAdministration::class);

        $oCountryVATGroup1 = $this->getServiceFromContainer(CountryVATGroup::class);
        $oCountryVATGroup1->setId(2);
        $oCountryVATGroup1->setData($aData1);

        $oCountryVATGroup2 = $this->getServiceFromContainer(CountryVATGroup::class);
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

        $this->assertEquals($aExpectedViewData, $oArticleAdministration->getCountryAndVATGroupsData(), 'Data which should go to template is not correct.');
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
     * Check view data for correct value which shows if article is TBE service.
     *
     * @param int $iIsTBEArticle
     *
     * @dataProvider providerViewDataIsTBEService
     */
    public function testViewDataIsTBEService($iIsTBEArticle)
    {
        /** @var Article $oArticle */
        $oArticle = oxNew(Article::class);
        $oArticle->setId('_testArticle');
        $oArticle->oxarticles__oevattbe_istbeservice = new Field($iIsTBEArticle);
        $oArticle->save();

        /** @var ArticleAdministration $oArticleAdministration */
        $oArticleAdministration = oxNew(ArticleAdministration::class);
        $oArticleAdministration->setEditObjectId('_testArticle');

        $this->assertSame($iIsTBEArticle, $oArticleAdministration->isArticleTBE());
    }

    /**
     * Checks if selected option is saved rate.
     *
     * @return ArticleAdministration
     */
    public function testSelectedRateForCountry()
    {
        /** @var ArticleAdministration $oArticleAdministration */
        $oArticleAdministration = oxNew(ArticleAdministration::class);
        $aSelectParams = array(
            'a7c40f632e04633c9.47194042' => 2,
            '8f241f110955d3260.55487539' => ''
        );
        $_POST['VATGroupsByCountry'] = $aSelectParams;
        $oArticleAdministration->setEditObjectId('_testArticle');
        $oArticleAdministration->save();

        $this->assertSame(true, $oArticleAdministration->isSelected('a7c40f632e04633c9.47194042', '2'));

        return $oArticleAdministration;
    }

    /**
     * Checks if rate was not selected.
     *
     * @param ArticleAdministration $oArticleAdministration
     *
     * @depends testSelectedRateForCountry
     *
     * @return ArticleAdministration
     */
    public function testNotSelectedRateForCountry($oArticleAdministration)
    {
        $this->assertSame(false, $oArticleAdministration->isSelected('8f241f110955d3260.55487539', ''));

        return $oArticleAdministration;
    }

    /**
     * Checks if method returns correct value for non existing country.
     *
     * @param ArticleAdministration $oArticleAdministration
     *
     * @depends testNotSelectedRateForCountry
     */
    public function testSelectionForNonExistingCountry($oArticleAdministration)
    {
        $this->assertSame(false, $oArticleAdministration->isSelected('NoneExistingId', '2'));
    }

    /**
     * Prepares VAT TBE groups data.
     *
     * @param array $aData
     */
    private function _addData($aData)
    {
        /** @var CountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew(CountryVATGroupsDbGateway::class);

        $oGateway->save($aData);
    }

    /**
     * Cleans current data.
     */
    private function _cleanData()
    {
        /** @var CountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew(CountryVATGroupsDbGateway::class);
        foreach ($oGateway->getList() as $aGroupInformation) {
            $oGateway->delete($aGroupInformation['OEVATTBE_ID']);
        }
    }
}
