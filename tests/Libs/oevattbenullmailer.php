<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Class used to stub email sending.
 */
class oeVATTBENullMailer extends oxEmail
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
