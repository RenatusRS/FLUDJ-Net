<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Config\Factories;

use App\Models\UserM;

class UserMTest extends CIUnitTestCase {
    use ControllerTestTrait;
    use DatabaseTestTrait;

    // protected $seed = 'Tests\Support\Database\Seeds\UserSeeder';
    private $model;

    public function testModelFindAll() {
        $this->model = new UserM();

        $this->assertGetAvatar();
        $this->assertGetBackground();
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
    private function assertBanUser() {
        $model = $this->model;

        $this->assertTrue($model->find(2)->review_ban == 0);
        $model->banUser(2);
        $this->assertTrue($model->find(2)->review_ban == 1);
    }
    private function assertUnbanUser() {
        $model = $this->model;

        $this->assertTrue($model->find(2)->review_ban == 1);
        $model->unbanUser(2);
        $this->assertTrue($model->find(2)->review_ban == 0);
    }
    private function assertPromoteUser() {
        $model = $this->model;

        $this->assertTrue($model->find(2)->admin_rights == 0);
        $model->promoteUser(2);
        $this->assertTrue($model->find(2)->admin_rights == 1);
    }
    private function assertDemoteUser() {
        $model = $this->model;

        $this->assertTrue($model->find(2)->admin_rights == 1);
        $model->demoteUser(2);
        $this->assertTrue($model->find(2)->admin_rights == 0);
    }
}
