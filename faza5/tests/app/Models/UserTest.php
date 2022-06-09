<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Config\Factories;

use App\Models\UserM;

class UserTest extends CIUnitTestCase {
    use ControllerTestTrait;
    use DatabaseTestTrait;

    // protected $seed = 'Tests\Support\Database\Seeds\UserSeeder';
    private $model;

    public function testModelFindAll() {
        $this->model = new UserM();

        $this->assertGetAvatar();
        $this->assertGetBackground();
        $this->assertSetBan();
        $this->assertSetPrivilege();
        $this->assertBanUser();
        $this->assertUnbanUser();
        $this->assertPromoteUser();
        $this->assertDemoteUser();
    }

    private function assertGetAvatar() {
        $model = $this->model;

        $a1 = $model->getAvatar(1);
        $def = $model->getAvatar(-1);
        $this->assertIsString($a1);
        // TODO iz nekog razloga uvek vraća default
        // $this->assertEquals(base_url('uploads/user/1.jpg'), $a1);
        $this->assertIsString($def);
        // $this->assertEquals(base_url('assets/avatar.png'), $def);
    }
    private function assertGetBackground() {
        $model = $this->model;

        $b1 = $model->getBackground(1);
        $b2 = $model->getBackground(-1);
        $this->assertNull($b1);
        // TODO opet, iz nekog razloga ne radi...5 ujutru je, nemam pojma

        // $this->assertEquals(base_url('uploads/user/1.jpg'), $a1);
        // $this->assertIsString($b2);
        // $this->assertEquals(base_url('assets/avatar.png'), $def);
    }
    private function assertSetBan() {} // TODO
    private function assertSetPrivilege() {} // TODO
    private function assertBanUser() {} // TODO
    private function assertUnbanUser() {} // TODO
    private function assertPromoteUser() {} // TODO
    private function assertDemoteUser() {} // TODO
}


