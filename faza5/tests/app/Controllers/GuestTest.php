<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

use App\Models\UserM;
use App\Models\ProductM;
use App\Models\BundleM;

class GuestTest extends CIUnitTestCase {
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected function test() {
        $routes = [
            ['get', 'http://localhost:8080/', '\App\Controllers\Guest::class'],
        ];

        return $this->withRoutes($routes);
    }

    public function testIndex() {
        $result = $this->test()->get('guest/index');
        $this->assertTrue($result->see("Popular Products"));
    }

    public function testLogin() {
        $result = $this->test()->get('guest/login');
        $this->assertTrue($result->see("Password"));
    }

    public function testLoginSubmit() {
        $result = $this->test()->call('post', 'guest/loginSubmit', ['username' => 'hose', 'password' => 'a']);
        $this->assertFalse($result->see("SIGN-IN"));
    }

    public function testRegistration() {
        $result = $this->test()->get('guest/registration');
        $this->assertTrue($result->see("Password"));
    }

    public function testRegistrationSubmit() {
        $result = $this->test()->call('post', 'guest/registrationSubmit', ['username' => 'hosey1234', 'password' => 'a']);
        $this->assertFalse($result->see("SIGN-IN"));
    }

    public function testProfile() {
        $users = (new UserM())->find();

        foreach ($users as $user) {
            $result = $this->test()->get("guest/profile/{$user->id}");
            $this->assertTrue($result->see("{$user->nickname}"));
        }
    }

    public function testProduct() {
        $products = (new ProductM())->find();

        foreach ($products as $product) {
            $result = $this->test()->get("guest/product/{$product->id}");
            $this->assertTrue($result->see("{$product->name}"));
        }
    }

    public function testBundle() {
        $bundles = (new BundleM())->find();

        foreach ($bundles as $bundle) {
            $result = $this->test()->get("guest/bundle/{$bundle->id}");
            $this->assertTrue($result->see("{$bundle->name}"));
        }
    }

    public function testAjaxProductLoad() {
        $name = 'DOOM';
        $result = $this->test()->call('post', 'guest/ajaxProductLoad/guest', ['ime' => $name]);
        $json = $result->getJSON();

        $result->assertEquals('http://localhost:8080/guest/product/15', json_decode($json));
    }

    public function testAjaxProductSearch() {
        $name = 'cs';
        $result = $this->test()->call('post', 'guest/ajaxProductSearch', ['q' => $name]);
        $json = $result->getJSON();

        $result->assertEquals('CS: Global Offensive', json_decode(json_decode($json))[0]->text);
    }
}
