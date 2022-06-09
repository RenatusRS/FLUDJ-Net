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

    public function testModelFindAll() {
        $model = new OwnershipM();

        // ===== OwnershipM::owns ========
        $exists = $model->owns(1, 1);
        $doesntExist = $model->owns(1, 2);
        $this->assertisBool($exists); // exists in db
        $this->assertTrue($exists);

        $this->assertisBool($doesntExist); // doesn't exist in db
        $this->assertFalse($doesntExist);

        // ==== OwnershipM::getOwned =====
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


        // ==== OwnershipM::acquire ======
        // TODO

        // ==== OwnershipM::getRating =====
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

        // ==== OwnershipM::ownedSum =====
        // TODO

        // ==== OwnershipM::matchingGenres =====
        // TODO

        // ==== OwnershipM::friendsLikes =====
        // TODO
    }
}