<?php

/**
 * This file is part of OXID eSales eVAT module.
 *
 * OXID eSales eVAT module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

use OxidEsales\EVatModule\Core\Module;
use OxidEsales\EVatModule\Service\ModuleSettings;

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => Module::MODULE_ID,
    'title'        => 'OXID eShop eVAT',
    'description'  => array(
        'de' => 'Das Modul eVAT erm&ouml;glicht es,
einem Land verschiedene Mehrwertsteuers&auml;tze zuzuweisen und zus&auml;tzlich
Artikel als Telekommunikations-, Rundfunk-, Fernseh- und auf elektronischem Weg erbrachte Dienstleistungen gem&auml;&szlig; der
<a href="https://ec.europa.eu/taxation_customs/news/vat-updated-version-moss-report-has-just-been-published-2016-05-12_de" target="_blank">Europ&auml;ischen Steuervorschrift ab 2015</a> zu definieren.',
        'en' => 'The OXID eVAT module allows you to configure a range of tax rates for a
country, and additionally enables you to define a product as a
Telecommunication, Broadcasting or Electronic (TBE) service according to
<a href="https://ec.europa.eu/taxation_customs/news/vat-updated-version-moss-report-has-just-been-published-2016-05-12_en" target="_blank">European tax directive as of 2015</a>',
    ),
    'thumbnail'    => 'logo.png',
    'version'      => '2.1.0',
    'author'       => 'OXID eSales AG',
    'url'          => 'http://www.oxid-esales.com',
    'email'        => 'info@oxid-esales.com',
    'controllers' => [
        'oevattbecountryvatgroups'                     => \OxidEsales\EVatModule\Controller\Admin\CountryVatGroups::class,
        'oevattbearticleadministration'                => \OxidEsales\EVatModule\Controller\Admin\ArticleAdministration::class,
        'oevattbecategoryadministration'               => \OxidEsales\EVatModule\Controller\Admin\CategoryAdministration::class,
    ],
    'extend'       => [
        //Components
        \OxidEsales\Eshop\Application\Component\BasketComponent::class => \OxidEsales\EVatModule\Component\BasketComponent::class,

        //Controllers
        \OxidEsales\Eshop\Application\Controller\BasketController::class  => \OxidEsales\EVatModule\Controller\BasketController::class,
        \OxidEsales\Eshop\Application\Controller\OrderController::class  => \OxidEsales\EVatModule\Controller\OrderController::class,

        \OxidEsales\Eshop\Application\Controller\Admin\OrderMain::class => \OxidEsales\EVatModule\Controller\Admin\OrderMain::class,
        \OxidEsales\Eshop\Application\Controller\Admin\CategoryMainAjax::class => \OxidEsales\EVatModule\Controller\Admin\CategoryMainAjax::class,
        \OxidEsales\Eshop\Application\Controller\Admin\ArticleExtendAjax::class => \OxidEsales\EVatModule\Controller\Admin\ArticleExtendAjax::class,
        \OxidEsales\Eshop\Application\Controller\Admin\ArticleMain::class => \OxidEsales\EVatModule\Controller\Admin\ArticleMain::class,

        //Models
        \OxidEsales\Eshop\Application\Model\Article::class => \OxidEsales\EVatModule\Shop\Article::class,
        \OxidEsales\Eshop\Application\Model\ArticleList::class => \OxidEsales\EVatModule\Shop\ArticleList::class,
        \OxidEsales\Eshop\Application\Model\User::class => \OxidEsales\EVatModule\Shop\User::class,
        \OxidEsales\Eshop\Application\Model\Search::class => \OxidEsales\EVatModule\Shop\Search::class,
        \OxidEsales\Eshop\Application\Model\VatSelector::class => \OxidEsales\EVatModule\Shop\VatSelector::class,
        \OxidEsales\Eshop\Application\Model\Basket::class => \OxidEsales\EVatModule\Shop\Basket::class,
        \OxidEsales\Eshop\Application\Model\Order::class => \OxidEsales\EVatModule\Shop\Order::class,
        \OxidEsales\Eshop\Application\Model\BasketContentMarkGenerator::class => \OxidEsales\EVatModule\Shop\BasketContentMarkGenerator::class,
        \OxidEsales\Eshop\Application\Model\Country::class => \OxidEsales\EVatModule\Shop\Country::class,
        \OxidEsales\Eshop\Application\Model\Category::class => \OxidEsales\EVatModule\Shop\Category::class,
        \OxidEsales\Eshop\Application\Model\Shop::class => \OxidEsales\EVatModule\Shop\Shop::class,

        //Core
        \OxidEsales\Eshop\Core\ViewConfig::class => \OxidEsales\EVatModule\Shop\ViewConfig::class,
    ],
    'events'    => array(
        'onActivate'   => '\OxidEsales\EVatModule\Core\Events::onActivate',
        'onDeactivate' => '\OxidEsales\EVatModule\Core\Events::onDeactivate'
    ),
    'templates' => array(
        '@oevattbe/admin/oevattbecountryvatgroups.tpl'       => 'views/smarty/admin/oevattbecountryvatgroups.tpl',
        '@oevattbe/admin/oevattbearticleadministration.tpl'  => 'views/smarty/admin/oevattbearticleadministration.tpl',
        '@oevattbe/admin/oevattbecategoryadministration.tpl' => 'views/smarty/admin/oevattbecategoryadministration.tpl',
    ),
    'blocks'    => array(
        array('template' => 'layout/base.tpl', 'block' => 'base_style', 'file' => 'views/blocks/layout/base.tpl'),
        array('template' => 'page/details/inc/productmain.tpl', 'block' => 'details_productmain_price_value', 'file' => 'views/blocks/page/details/inc/productmain.tpl'),
        array('template' => 'widget/product/listitem_grid.tpl', 'block' => 'widget_product_listitem_grid_price_value', 'file' => 'views/blocks/widget/product/listitem_grid.tpl'),
        array('template' => 'widget/product/listitem_infogrid.tpl', 'block' => 'widget_product_listitem_infogrid_price_value', 'file' => 'views/blocks/widget/product/listitem_infogrid.tpl'),
        array('template' => 'widget/product/listitem_line.tpl', 'block' => 'widget_product_listitem_line_price_value', 'file' => 'views/blocks/widget/product/listitem_line.tpl'),
        array('template' => 'widget/product/boxproduct.tpl', 'block' => 'widget_product_boxproduct_price_value', 'file' => 'views/blocks/widget/product/boxproduct.tpl'),
        array('template' => 'widget/product/bargainitem.tpl', 'block' => 'widget_product_bargainitem_price_value', 'file' => 'views/blocks/widget/product/bargainitem.tpl'),
        array('template' => 'widget/product/compareitem.tpl', 'block' => 'widget_product_compareitem_price_value', 'file' => 'views/blocks/widget/product/compareitem.tpl'),
        array('template' => 'user_main.tpl', 'block' => 'admin_user_main_form', 'file' => 'views/blocks/admin/user_main.tpl'),
        array('template' => 'page/checkout/inc/basketcontents.tpl', 'block' => 'checkout_basketcontents_basketitem_vat', 'file' => 'views/blocks/page/checkout/inc/basketitem_vat.tpl'),
        array('template' => 'page/checkout/inc/basketcontents_table.tpl', 'block' => 'checkout_basketcontents_basketitem_vat', 'file' => 'views/blocks/page/checkout/inc/basketitem_vat_in_table.tpl'),
        array('template' => 'page/checkout/basket.tpl', 'block' => 'checkout_basket_next_step_bottom', 'file' => 'views/blocks/page/checkout/basket_nextstep.tpl'),
        array('template' => 'page/checkout/order.tpl', 'block' => 'checkout_order_next_step_bottom', 'file' => 'views/blocks/page/checkout/order_nextstep.tpl'),
        ['template' => 'order_main.tpl', 'block' => 'admin_order_main_form', 'file' => 'views/admin/tpl/oevattbeorder_main.tpl'],
        array('template' => 'layout/page.tpl', 'block' => 'content_main', 'file' => 'views/blocks/layout/page.tpl'),
        array('template' => 'country_list.tpl', 'block' => 'admin_country_list_colgroup', 'file' => 'views/blocks/admin/country_list_colgroup.tpl'),
        array('template' => 'country_list.tpl', 'block' => 'admin_country_list_filter', 'file' => 'views/blocks/admin/country_list_filter.tpl'),
        array('template' => 'country_list.tpl', 'block' => 'admin_country_list_sorting', 'file' => 'views/blocks/admin/country_list_sorting.tpl'),
        array('template' => 'country_list.tpl', 'block' => 'admin_country_list_item', 'file' => 'views/blocks/admin/country_list_item.tpl'),
        array('template' => 'country_main.tpl', 'block' => 'admin_country_main_form', 'file' => 'views/blocks/admin/country_main.tpl'),
    ),
    'settings'  => array(
        ['group' => 'oevattbe', 'name' => ModuleSettings::COUNTRY_EVIDENCES, 'type' => 'aarr', 'value' => ['billing_country' => 1, 'geo_location' => 1]],
        ['group' => 'oevattbe', 'name' => ModuleSettings::DEFAULT_EVIDENCE, 'type' => 'str', 'value' => 'billing_country'],
        ['group' => 'oevattbe', 'name' => ModuleSettings::DOMESTIC_COUNTRY, 'type' => 'str', 'value' => 'DE'],
    )
);
