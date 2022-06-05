<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class AdminTest extends CIUnitTestCase {
    use ControllerTestTrait;
    use DatabaseTestTrait;

    protected function test($func) {
        return $this->withURI('http://localhost:8080/')
            ->controller(\App\Controllers\Admin::class)
            ->execute($func);
    }
}
