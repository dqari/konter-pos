<?php

use CodeIgniter\Test\CIUnitTestCase;
use Config\Filters;

/**
 * @internal
 */
final class PosSecurityTest extends CIUnitTestCase
{
    public function testCsrfIsEnabledGlobally(): void
    {
        $filters = new Filters();

        $this->assertContains('csrf', $filters->globals['before']);
    }

    public function testOwnerRoutesUseRoleFilter(): void
    {
        $routes = file_get_contents(APPPATH . 'Config/Routes.php');

        $this->assertNotFalse($routes);
        $this->assertStringContainsString("['filter' => 'role:Owner']", $routes);
    }

    public function testLoginPageDoesNotResetOwnerPassword(): void
    {
        $authController = file_get_contents(APPPATH . 'Controllers/Auth.php');

        $this->assertNotFalse($authController);
        $this->assertStringNotContainsString("owner_utama')->set", $authController);
    }
}
