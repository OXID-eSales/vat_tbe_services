<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Libs;

use OxidEsales\Eshop\Core\Email;

/**
 * Class used to stub email sending.
 */
class NullMailer extends Email
{
    /**
     * Overrides send mail functionality.
     *
     * @return bool
     */
    public function send()
    {
        return true;
    }
}
