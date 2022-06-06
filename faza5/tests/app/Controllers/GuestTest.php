<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

use App\Models\UserM;
use App\Models\ProductM;
use App\Models\BundleM;

class GuestTest extends CIUnitTestCase {
    use ControllerTestTrait;
    use DatabaseTestTrait;

    protected function test() {
        return $this->withURI('http://localhost:8080/')
            ->controller(\App\Controllers\Guest::class);
    }

    public function testIndex() {
        $this->assertTrue($this->test()->execute('index')->isOK());
    }

    public function testLogin() {
        $this->assertTrue($this->test()->execute('login')->isOK());
    }

    public function testLoginSubmit() {
        $this->assertTrue($this->test()->execute('loginSubmit')->isOK());
    }

    public function testRegistration() {
        $this->assertTrue($this->test()->execute('registration')->isOK());
    }

    public function testRegistrationSubmit() {
        $this->assertTrue($this->test()->execute('registrationSubmit')->isOK());
    }

    public function testProfile() {
        $users = (new UserM())->find();

        foreach ($users as $user) {
            $this->assertTrue($this->test()->execute('profile', $user->id)->isOK());
        }
    }

    public function testProduct() {
        $products = (new ProductM())->find();

        foreach ($products as $product) {
            $this->assertTrue($this->test()->execute('product', $product->id)->isOK());
        }
    }

    public function testBundle() {
        $bundles = (new BundleM())->find();

        foreach ($bundles as $bundle) {
            $this->assertTrue($this->test()->execute('bundle', $bundle->id)->isOK());
        }
    }
}
