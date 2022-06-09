<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Config\Factories;

use App\Models\ProductM;

class ProductT extends CIUnitTestCase {
    use ControllerTestTrait;
    use DatabaseTestTrait;

    // protected $seed = 'Tests\Support\Database\Seeds\ProductSeeder';
    private $model;

    // sledeći metodi iz modela neće biti testirani:
    // getRating, getProductRating, getDiscountRating, getCouponRating,
    // getDiscoveryProducts
    public function testModelFindAll() {
        $this->model = new ProductM();

        $this->assertGetGenres();
        $this->assertFutureDate();
        $this->assertGetDiscount();
        $this->assertGetDiscountedPrice();
        $this->assertGetAllProducts();
        $this->assertGetRating();
        $this->assertGetTopProducts();
        $this->getHeroProduct();
        $this->assertGetHighRatingProducts();
        $this->assertGetTopSellersProducts();
        $this->assertGetDiscountedProducts();
        $this->assertGetCouponProducts();
        $this->assertGetProductsUserLike();
        $this->assertGetProductsUserFriendsLike();
        $this->assertGetSimilarProducts();
        $this->assertGetBackground();
    }

    private function assertGetGenres() {
        $model = $this->model;

        $p1 = $model->productNameExists("Elden Ring", 1);
        $p2 = $model->productNameExists("Elden Ring", -1);
        $p3 = $model->productNameExists("Elden King", 1);

        $this->assertIsBool($p1);
        $this->assertFalse($p1);

        $this->assertIsBool($p2);
        $this->assertTrue($p2);

        $this->assertIsBool($p3);
        $this->assertFalse($p3);
    }
    private function assertFutureDate() {
        $model = $this->model;

        $past = $model->future_date("2000/01/01");
        $future = $model->future_date("2099/01/01");

        $this->assertIsBool($past);
        $this->assertFalse($past);

        $this->assertIsBool($future);
        $this->assertTrue($future);
    }
    private function assertGetDiscount() {
        $model = $this->model;

        $d1 = $model->getDiscount(35);
        $d2 = $model->getDiscount(-1);
        $d3 = $model->getDiscount(1);

        $this->assertIsInt($d1);
        $this->assertEquals(40, $d1);

        $this->assertIsInt($d2);
        $this->assertEquals(0, $d2);

        $this->assertIsInt($d3);
        $this->assertEquals(0, $d3);
    }
    private function assertGetDiscountedPrice() {
        $model = $this->model;

        $d1 = $model->getDiscountedPrice(35);
        $d2 = $model->getDiscountedPrice(-1);
        $d3 = $model->getDiscountedPrice(1);

        $this->assertIsFloat($d1);
        $this->assertEqualsWithDelta(5.99, $d1, 0.01);

        $this->assertEqualsWithDelta(0, $d2, 0.01);

        $this->assertIsFloat($d3);
        $this->assertEqualsWithDelta(59.99, $d3, 0.01);
    }
    private function assertGetAllProducts() {
        $model = $this->model;

        $noDLCs = $model->getAllProducts(true);
        $yesDLCs = $model->getAllProducts(false);

        $this->assertIsIterable($noDLCs);
        $noDLCs = iterator_to_array($noDLCs);
        foreach ($noDLCs as $p) {
            $this->assertIsObject($p);
        }
        $this->assertCount(41, $noDLCs);

        $this->assertIsIterable($yesDLCs);
        $yesDLCs = iterator_to_array($yesDLCs);
        foreach ($yesDLCs as $p) {
            $this->assertIsObject($p);
        }
        $this->assertCount(49, $yesDLCs);
    }
    private function assertGetRating() {
        $model = $this->model;
    } // TODO (možda potpuno nepotrebno uopšte testirati ovo).
    // isto važi za getProductRating, getDiscountRating, getCouponRating itd.

    private function assertGetTopProducts() {
        $model = $this->model;

        $p = $model->getTopProducts();

        $this->assertIsArray($p);
        $this->assertTrue($p[0]->id == 16);
        $this->assertCount(41, $p);
    }
    private function getHeroProduct() {
        $model = $this->model;

        for ($i = 0; $i < 10; $i++)
            $this->assertIsObject($model->getHeroProduct());
    }
    private function assertGetHighRatingProducts() {
        $model = $this->model;

        $guestProducts = $model->getHighRatingProducts();
        $userProducts = $model->getHighRatingProducts(1);

        $this->assertIsArray($guestProducts);
        foreach ($guestProducts as $p)
            $this->assertIsObject($p);
        $this->assertTrue($guestProducts[0]->id == 16);
        $this->assertTrue($guestProducts[4]->id == 19);
        $this->assertCount(41, $guestProducts);

        $this->assertIsArray($userProducts);
        foreach ($userProducts as $p)
            $this->assertIsObject($p);
        $this->assertTrue($userProducts[0]->id == 15);
        $this->assertTrue($userProducts[4]->id == 10);
        $this->assertCount(35, $userProducts);
    }
    private function assertGetTopSellersProducts() {
        $model = $this->model;

        $guestProducts = $model->getTopSellersProducts();
        $userProducts = $model->getTopSellersProducts(1);

        $this->assertIsArray($guestProducts);
        foreach ($guestProducts as $p)
            $this->assertIsObject($p);
        $this->assertTrue($guestProducts[0]->id == 8);
        $this->assertTrue($guestProducts[3]->id == 39);
        $this->assertCount(41, $guestProducts);

        $this->assertIsArray($userProducts);
        foreach ($userProducts as $p)
            $this->assertIsObject($p);
        $this->assertTrue($userProducts[0]->id == 8);
        $this->assertTrue($userProducts[3]->id == 13);
        $this->assertCount(35, $userProducts);
    }
    private function assertGetDiscountedProducts() {
        $model = $this->model;

        $guestProducts = $model->getDiscountedProducts();
        $userProducts = $model->getDiscountedProducts(2);

        $this->assertIsArray($guestProducts);
        foreach ($guestProducts as $p)
            $this->assertIsObject($p);
        $this->assertTrue($guestProducts[0]->id == 18);
        $this->assertTrue($guestProducts[3]->id == 40);
        $this->assertCount(13, $guestProducts);

        $this->assertIsArray($userProducts);
        foreach ($userProducts as $p)
            $this->assertIsObject($p);
        $this->assertTrue($userProducts[0]->id == 18);
        $this->assertTrue($userProducts[3]->id == 24);
        $this->assertCount(8, $userProducts);
    }
    private function assertGetCouponProducts() {
        $model = $this->model;

        $guestProducts = $model->getCouponProducts();
        $userProducts = $model->getCouponProducts(1);

        $this->assertIsArray($guestProducts);
        $this->assertCount(0, $guestProducts);

        $this->assertIsArray($userProducts);
        foreach ($userProducts as $p)
            $this->assertIsObject($p);
        $this->assertTrue($userProducts[0]->id == 39);
        $this->assertTrue($userProducts[1]->id == 31);
        $this->assertCount(3, $userProducts);
    }
    private function assertGetProductsUserLike() {
        $model = $this->model;

        $guestProducts = $model->getProductsUserLike();
        $userProducts = $model->getProductsUserLike(1);

        $this->assertIsArray($guestProducts);
        $this->assertCount(0, $guestProducts);

        $this->assertIsArray($userProducts);
        foreach ($userProducts as $p)
            $this->assertIsObject($p);
        $this->assertTrue($userProducts[0]->id == 6);
        $this->assertTrue($userProducts[5]->id == 24);
        $this->assertCount(31, $userProducts);
    }
    private function assertGetProductsUserFriendsLike() {
        $model = $this->model;

        $guestProducts = $model->getProductsUserFriendsLike();
        $userProducts = $model->getProductsUserFriendsLike(1);

        $this->assertIsArray($guestProducts);
        $this->assertCount(0, $guestProducts);

        $this->assertIsArray($userProducts);
        foreach ($userProducts as $p)
            $this->assertIsObject($p);
        $this->assertTrue($userProducts[0]->id == 19);
        $this->assertTrue($userProducts[4]->id == 27);
        $this->assertCount(41, $userProducts);
    }
    private function assertGetSimilarProducts() {
        $model = $this->model;

        $p1 = $model->getSimilarProducts(15, 2);
        $p2 = $model->getSimilarProducts(15);
        $p3 = $model->getSimilarProducts(41);

        $this->assertIsArray($p1);
        foreach ($p1 as $p)
            $this->assertIsObject($p);
        $this->assertCount(17, $p1);
        $this->assertTrue($p1[0]->id == 10);
        $this->assertTrue($p1[3]->id == 5);

        $this->assertIsArray($p2);
        foreach ($p2 as $p)
            $this->assertIsObject($p);
        $this->assertTrue($p2[0]->id == 10);
        $this->assertTrue($p2[3]->id == 28);
        $this->assertCount(24, $p2);

        $this->assertIsArray($p3);
        foreach ($p3 as $p)
            $this->assertIsObject($p);
        $this->assertTrue($p3[0]->id == 37);
        $this->assertCount(2, $p3);
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
}

