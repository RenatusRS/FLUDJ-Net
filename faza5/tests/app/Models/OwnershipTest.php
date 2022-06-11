<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Config\Factories;

use App\Models\OwnershipM;

class OwnershipTest extends CIUnitTestCase {
    use ControllerTestTrait;
    use DatabaseTestTrait;

    // protected $seed = 'Tests\Support\Database\Seeds\OwnershipSeeder';

    private $model;

    public function testModelFindAll() {
        $this->model = new OwnershipM();

        $this->assertOwns();
        $this->assertGetOwned();
        $this->assertAcquire();
        $this->assertGetRating();
        $this->assertOwnedSum();
        $this->assertMatchingGenres();
        $this->assertFriendsLikes();
    }

    private function assertOwns() {
        $model = $this->model;

        $exists = $model->owns(1, 1);
        $doesntExist = $model->owns(1, 2);

        $this->assertisBool($exists); // exists in db
        $this->assertTrue($exists);

        $this->assertisBool($doesntExist); // doesn't exist in db
        $this->assertFalse($doesntExist);
    }
    private function assertGetOwned() {
        $model = $this->model;

        $owned = $model->getOwned(1);
        $doesntExist = $model->getOwned(14);

        $this->assertIsArray($owned);
        foreach ($owned as $product) {
            $this->assertArrayHasKey('product', $product);
            $this->assertArrayHasKey('rating', $product);
            $this->assertArrayHasKey('review', $product);
        }

        $this->assertIsArray($doesntExist);
        $this->assertCount(0, $doesntExist);
    }
    private function assertAcquire() {} // TODO
    private function assertGetRating() {
        $model = $this->model;

        $hasRating = $model->getRating(1, 1);
        $doesntHaveRating = $model->getRating(34, 3);
        $doesntHaveProduct = $model->getRating(1, 2);
        $userDoesntExist = $model->getRating(14, 1);
        $productDoesntExist = $model->getRating(1, 100);
        $userAndProductNotExisting = $model->getRating(14, 1000);

        $this->assertIsInt($hasRating);
        $this->assertEquals(3, $hasRating);

        $this->assertIsInt($doesntHaveRating);
        $this->assertEquals(0, $doesntHaveRating);

        $this->assertIsInt($doesntHaveProduct);
        $this->assertEquals(0, $doesntHaveProduct);

        $this->assertIsInt($userDoesntExist);
        $this->assertEquals(0, $userDoesntExist);

        $this->assertIsInt($productDoesntExist);
        $this->assertEquals(0, $productDoesntExist);

        $this->assertIsInt($userAndProductNotExisting);
        $this->assertEquals(0, $userAndProductNotExisting);
    }
    private function assertOwnedSum() { } // TODO
    private function assertMatchingGenres() { } // TODO
    private function assertFriendsLikes() { } // TODO
}