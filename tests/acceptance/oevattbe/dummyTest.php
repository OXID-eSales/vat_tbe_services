<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Acceptance test cases
 */
class oeVATTBE_dummyTest extends oxTestCase
{

    const TEST_USER_NAME = 'test@oxid-esales.com';
    const TEST_USER_PASSWORD = 'testtest';

    /**
     * test for activating Module.
     *
     * @group mobile
     */
    public function testActivateExtension()
    {
        $this->open(shopURL . "admin");
        $this->loginAdminForModule("Extensions", "Modules", null, null, null, "admin", "admin");
        $this->openListItem("link=VAT TBE services");
        $this->clickAndWait("module_activate");
        // dumping database
        try {
            $this->dumpDB();
        } catch (Exception $e) {
            $this->stopTesting("Failed dumping original db");
        }
    }
}
