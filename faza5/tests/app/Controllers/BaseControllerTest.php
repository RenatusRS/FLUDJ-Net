<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class BaseTest extends CIUnitTestCase {
    use ControllerTestTrait;
    use DatabaseTestTrait;

    protected function test($func) {
        return $this->withURI('http://localhost:8080/')
            ->controller(\App\Controllers\BaseController::class)
            ->execute($func);
    }
}
