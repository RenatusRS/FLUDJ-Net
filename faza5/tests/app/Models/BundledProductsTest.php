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

        $this->assertProductsInBundle();
        $this->assertAddToBundle(1, 38);
        $this->assertAddToBundle(1, 2);
        $this->assertClearBundle(1);
        $this->assertClearBundle(2);
        $this->assertProductsNotInBundle();
    }

    private function assertClearBundle($id) {
        $model = $this->model;

        $products = iterator_to_array($model->productsInBundle($id));
        $cnt1 = count($products);
        $model->clearBundle($id);
        $products2 = iterator_to_array($model->productsInBundle($id));
        $this->assertCount(0, $products2);
        foreach ($products as $product)
            $model->addToBundle($id, $product->id);
        $products = iterator_to_array($model->productsInBundle($id));
        $this->assertCount($cnt1, $products);
    }
    private function assertAddToBundle($idBundle, $idProduct) {
        $model = $this->model;

        $products1 = iterator_to_array($model->productsInBundle($idBundle));
        if ($model->addToBundle($idBundle, $idProduct)) {
            $products2 = iterator_to_array($model->productsInBundle($idBundle));
            $this->assertCount(count($products1) + 1, $products2);
            $model->where('id_bundle', $idBundle)
                ->where('id_product', $idProduct)
                ->delete();
            $products3 = iterator_to_array($model->productsInBundle($idBundle));
            $this->assertCount(count($products1), $products3);
        }
    }
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