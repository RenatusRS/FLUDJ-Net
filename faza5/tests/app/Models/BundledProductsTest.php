<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Config\Factories;

use App\Models\BundledProductsM;

class BundledProductsTest extends CIUnitTestCase {
    use ControllerTestTrait;
    use DatabaseTestTrait;

    // protected $seed = 'Tests\Support\Database\Seeds\BundledProductsSeeder';
    private $model;

    public function testModelFindAll() {
        $this->model = new BundledProductsM();

        $this->assertClearBundle();
        $this->assertAddToBundle();
        $this->assertProductsInBundle();
        $this->assertProductsNotInBundle();
    }

    private function assertClearBundle() {} // TODO
    private function assertAddToBundle() {} // TODO
    private function assertProductsInBundle() {
        $model = $this->model;

        $bundleExists = $model->productsInBundle(1);
        $bundleDoesntExist = $model->productsInBundle(50);

        $this->assertIsIterable($bundleExists);
        $bundleExists = iterator_to_array($bundleExists);
        $this->assertNotCount(0, $bundleExists);
        foreach ($bundleExists as $product) {
            $this->assertIsObject($product);
        }

        $this->assertIsIterable($bundleDoesntExist);
        $bundleDoesntExist = iterator_to_array($bundleDoesntExist);
        $this->assertCount(0, $bundleDoesntExist);

    }
    private function assertProductsNotInBundle() {
        $model = $this->model;

        $bundleExists = $model->productsNotInBundle(1);
        $bundleDoesntExist = $model->productsNotInBundle(50);

        $this->assertIsIterable($bundleExists);
        $bundleExists = iterator_to_array($bundleExists);
        $this->assertNotCount(0, $bundleExists);
        foreach ($bundleExists as $bundle) {
            $this->assertIsObject($bundle);
        }

        $this->assertIsIterable($bundleDoesntExist);
        $bundleDoesntExist = iterator_to_array($bundleDoesntExist);
        $this->assertNotCount(0, $bundleDoesntExist);
        foreach ($bundleDoesntExist as $bundle) {
            $this->assertIsObject($bundle);
        }
    }
}