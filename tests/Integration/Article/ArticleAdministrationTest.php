<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Article;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Controller\Admin\ArticleAdministration;
use OxidEsales\EVatModule\Model\CountryVATGroup;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;
use OxidEsales\EVatModule\Model\GroupArticleCacheInvalidator;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;
use OxidEsales\EVatModule\Traits\ServiceContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;

/**
 * Testing VAT TBE administration in article page.
 */
class ArticleAdministrationTest extends BaseTestCase
{
    use ContainerTrait;

    /**
     * Check if view data is correct.
     */
    public function testViewData()
    {
        $aData1 = [
            'OEVATTBE_ID'          => '2',
            'OEVATTBE_COUNTRYID'   => 'a7c40f631fc920687.20179984',
            'OEVATTBE_NAME'        => 'Group Name1',
            'OEVATTBE_DESCRIPTION' => 'Some description1',
            'OEVATTBE_RATE'        => '20.50',
            'OEVATTBE_TIMESTAMP'   => '2014-10-24 09:46:11'
        ];
        $aData2 = [
            'OEVATTBE_ID'          => '3',
            'OEVATTBE_COUNTRYID'   => 'a7c40f6323c4bfb36.59919433',
            'OEVATTBE_NAME'        => 'Group Name2',
            'OEVATTBE_DESCRIPTION' => 'Some description2',
            'OEVATTBE_RATE'        => '11.11',
            'OEVATTBE_TIMESTAMP'   => '2014-10-24 09:46:11'
        ];
        $this->_cleanData();
        $this->_addData($aData1);
        $this->_addData($aData2);

        /** @var ArticleAdministration $oArticleAdministration */
        $oArticleAdministration = oxNew(ArticleAdministration::class);

        /** @var CountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew(CountryVATGroupsDbGateway::class);

        /** @var GroupArticleCacheInvalidator $groupArticleCacheInvalidator */
        $groupArticleCacheInvalidator = $this->get(GroupArticleCacheInvalidator::class);

        $oCountryVATGroup1 = oxNew(CountryVATGroup::class, $oGateway, $groupArticleCacheInvalidator);
        $oCountryVATGroup1->setId(2);
        $oCountryVATGroup1->setData($aData1);

        $oCountryVATGroup2 = oxNew(CountryVATGroup::class, $oGateway, $groupArticleCacheInvalidator);
        $oCountryVATGroup2->setId(3);
        $oCountryVATGroup2->setData($aData2);

        $aExpectedViewData = [
            'a7c40f631fc920687.20179984' => [
                'countryTitle' => 'Deutschland',
                'countryGroups' => [
                    $oCountryVATGroup1
                ],
            ],
            'a7c40f6323c4bfb36.59919433' => [
                'countryTitle' => 'Italien',
                'countryGroups' => [
                    $oCountryVATGroup2
                ],
            ],
        ];

        // phpcs:ignore Generic.Files.LineLength.TooLong
        $this->assertEquals($aExpectedViewData, $oArticleAdministration->getCountryAndVATGroupsData(), 'Data which should go to template is not correct.');
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function providerViewDataIsTBEService()
    {
        return [
            /** TBE Service */
            [1],
            /** Not TBE Service */
            [0],
        ];
    }

    /**
     * Check view data for correct value which shows if article is TBE service.
     *
     * @param int $iIsTBEArticle
     */
    #[DataProvider('providerViewDataIsTBEService')]
    public function testViewDataIsTBEService($iIsTBEArticle)
    {
        /** @var Article $oArticle */
        $oArticle = oxNew(Article::class);
        $oArticle->load('_testArticle');
        $oArticle->assign([
            'oevattbe_istbeservice' => $iIsTBEArticle
        ]);
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
    public function testSelectedRateForCountry(): ArticleAdministration
    {
        /** @var ArticleAdministration $oArticleAdministration */
        $oArticleAdministration = oxNew(ArticleAdministration::class);
        $aSelectParams = [
            'a7c40f632e04633c9.47194042' => 2,
            '8f241f110955d3260.55487539' => ''
        ];
        $_POST['VATGroupsByCountry'] = $aSelectParams;
        $_POST['editval'] = ['oevattbe_istbeservice' => true];
        $oArticleAdministration->setEditObjectId('_testArticle');
        $oArticleAdministration->save();

        $this->assertTrue($oArticleAdministration->isSelected('a7c40f632e04633c9.47194042', 2));

        return $oArticleAdministration;
    }

    /**
     * Checks if rate was not selected.
     *
     * @param ArticleAdministration $oArticleAdministration
     *
     * @return ArticleAdministration
     */
    #[Depends('testSelectedRateForCountry')]
    public function testNotSelectedRateForCountry(ArticleAdministration $oArticleAdministration)
    {
        $this->assertFalse($oArticleAdministration->isSelected('8f241f110955d3260.55487539', ''));

        return $oArticleAdministration;
    }

    /**
     * Checks if method returns correct value for non existing country.
     *
     * @param ArticleAdministration $oArticleAdministration
     *
     */
    #[Depends('testNotSelectedRateForCountry')]
    public function testSelectionForNonExistingCountry($oArticleAdministration)
    {
        $this->assertSame(false, $oArticleAdministration->isSelected('NoneExistingId', 2));
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
