<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Config\Factories;

use App\Models\RelationshipM;
use App\Models\UserM;

class RelationshipT extends CIUnitTestCase {
    use ControllerTestTrait;
    use DatabaseTestTrait;

    // protected $seed = 'Tests\Support\Database\Seeds\RelationshipSeeder';
    private $model;

    public function testModelFindAll() {
        $this->model = new RelationshipM();

        $this->assertGetFriends();
        $this->assertGetFriendsWhoOwn();
        $this->assertGetIncoming();
        $this->assertGetSent();
    }

    private function assertGetFriends() {
        $model = $this->model;

        $noFriends = $model->getFriends(-1);
        $u1 = $model->getFriends(1);
        $u2 = $model->getFriends(2);

        $this->assertIsArray($noFriends);
        $this->assertCount(0, $noFriends);

        $this->assertIsArray($u1);
        $this->assertCount(11, $u1);

        $this->assertIsArray($u2);
        $this->assertCount(15, $u2);
    }
    private function assertGetFriendsWhoOwn() {
        $model = $this->model;

        $noFriends = $model->getFriendsWhoOwn(-1, 1);
        $u1 = $model->getFriendsWhoOwn(1, 5);
        $u2 = $model->getFriendsWhoOwn(15, 2);

        $this->assertIsArray($noFriends);
        $this->assertCount(0, $noFriends);

        $this->assertIsArray($u1);
        $this->assertCount(2, $u1);

        $this->assertIsArray($u2);
        $this->assertCount(4, $u2);
    }
    private function assertGetIncoming() {
        $model = $this->model;

        $user1 = (new UserM())->find(-1);
        $user2 = (new UserM())->find(1);
        $user3 = (new UserM())->find(2);

        $noFriends = $model->getIncoming($user1);
        $u1 = $model->getIncoming($user2);
        $u2 = $model->getIncoming($user3);

        $this->assertIsArray($noFriends);
        $this->assertCount(0, $noFriends);

        $this->assertIsArray($u1);
        $this->assertCount(3, $u1);

        $this->assertIsArray($u2);
        $this->assertCount(1, $u2);
    }
    private function assertGetSent() {
        $model = $this->model;

        $user1 = (new UserM())->find(-1);
        $user2 = (new UserM())->find(5);
        $user3 = (new UserM())->find(2);

        $noFriends = $model->getSent($user1);
        $u1 = $model->getSent($user2);
        $u2 = $model->getSent($user3);

        $this->assertIsArray($noFriends);
        $this->assertCount(0, $noFriends);

        $this->assertIsArray($u1);
        $this->assertCount(4, $u1);

        $this->assertIsArray($u2);
        $this->assertCount(6, $u2);
    }
}

