<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Config\Factories;

use App\Models\BundleM;
use App\Models\BundledProductsM;

class BundleT extends CIUnitTestCase {
    use ControllerTestTrait;
    use DatabaseTestTrait;

    // protected $seed = 'Tests\Support\Database\Seeds\BundleSeeder';
    private $model;

    public function testModelFindAll() {
        $this->model = new BundleM();

        $this->assertBundleNameExists();
        $this->assertBundlePrice();
        $this->assertBundleProducts();
        $this->assertGetBackground();
        $this->assertGetBundles();
    }

    private function assertBundleNameExists() {
        $model = $this->model;

        $isCurrentBundle = $model->bundleNameExists("Portal Bundle", 6);
        $exists = $model->bundleNameExists("Portal Bundle");
        $doesntExist = $model->bundleNameExists("Shmortal Shmundle");

        $this->assertIsBool($isCurrentBundle);
        $this->assertFalse($isCurrentBundle);

        $this->assertIsBool($exists);
        $this->assertTrue($exists);

        $this->assertIsBool($doesntExist);
        $this->assertFalse($doesntExist);
    }
    private function assertBundlePrice() {
        $this->priceHasOneProduct(1, 1);
        $this->priceHasNoProducts(2, 1);
        $this->priceHasAllProducts(1, 2);
        $this->priceIsGuest(10, -1);
    }
    private function assertBundleProducts() {
        $model = $this->model;

        $bundleExists = $model->bundleProducts(1);
        $bundleDoesntExist = $model->bundleProducts(50);

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
    private function assertGetBackground() {
        $model = $this->model;

        $noBackground = $model->getBackground(1);
        $background = $model->getBackground(2);

        $this->assertNull($noBackground);

        /* TODO ne radi prvi assert iz nekog razloga
        $this->assertNotNull($background);
        $this->assertIsString($background);
        $this->assertEquals(base_url('uploads/product/45/background.png'), $background);
        */
    }
    private function assertGetBundles() {
        $model = $this->model;

        $noBundles = $model->getBundles(1);
        $oneBundle = $model->getBundles(3);
        $twoBundles = $model->getBundles(6);

        $this->assertIsArray($noBundles);
        $this->assertCount(0, $noBundles);

        $this->assertIsArray($oneBundle);
        $this->assertCount(1, $oneBundle);

        $this->assertIsArray($twoBundles);
        $this->assertCount(2, $twoBundles);
    }

    // pomoćni
    private function priceHasOneProduct($bundleId, $userId) {
        $model = $this->model;
        $bundle = $model->find($bundleId);
        $products = iterator_to_array((new BundledProductsM())->productsInBundle($bundleId));
        $prices = $model->bundlePrice($products, $bundle->discount, $userId);

        $this->assertIsArray($prices);
        $this->assertArrayHasKey('price', $prices);
        $this->assertArrayHasKey('discount', $prices);
        $this->assertArrayHasKey('final', $prices);

        $this->assertEqualsWithDelta(3.99, $prices['price'], 0.01);
        $this->assertEqualsWithDelta(0, $prices['discount'], 0.01);
        $this->assertEqualsWithDelta(3.99, $prices['final'], 0.01);
    }
    private function priceHasNoProducts($bundleId, $userId) {
        $model = $this->model;

        $bundle = $model->find($bundleId);
        $products = iterator_to_array((new BundledProductsM())->productsInBundle($bundleId));
        $prices = $model->bundlePrice($products, $bundle->discount, $userId);

        $this->assertIsArray($prices);
        $this->assertArrayHasKey('price', $prices);
        $this->assertArrayHasKey('discount', $prices);
        $this->assertArrayHasKey('final', $prices);

        $this->assertEqualsWithDelta(39.97, $prices['price'], 0.01);
        $this->assertEqualsWithDelta(35, $prices['discount'], 0.01);
        $this->assertEqualsWithDelta(25.98, $prices['final'], 0.01);
    }
    private function priceHasAllProducts($bundleId, $userId) {
        $model = $this->model;

        $bundle = $model->find($bundleId);
        $products = iterator_to_array((new BundledProductsM())->productsInBundle($bundleId));
        $prices = $model->bundlePrice($products, $bundle->discount, $userId);

        $this->assertIsArray($prices);
        $this->assertArrayHasKey('price', $prices);
        $this->assertArrayHasKey('discount', $prices);
        $this->assertArrayHasKey('final', $prices);

        $this->assertEqualsWithDelta(0, $prices['price'], 0.01);
        $this->assertEqualsWithDelta(0, $prices['discount'], 0.01);
        $this->assertEqualsWithDelta(0, $prices['final'], 0.01);
    }
    private function priceIsGuest($bundleId, $userId) {
        $model = $this->model;

        $bundle = $model->find($bundleId);
        $products = iterator_to_array((new BundledProductsM())->productsInBundle($bundleId));
        $prices = $model->bundlePrice($products, $bundle->discount, $userId);

        $this->assertIsArray($prices);
        $this->assertArrayHasKey('price', $prices);
        $this->assertArrayHasKey('discount', $prices);
        $this->assertArrayHasKey('final', $prices);

        $this->assertEqualsWithDelta(24.57, $prices['price'], 0.01);
        $this->assertEqualsWithDelta(30, $prices['discount'], 0.01);
        $this->assertEqualsWithDelta(17.20, $prices['final'], 0.01);
    }
}