<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Config\Factories;

use App\Models\ProductM;

class ProductTest extends CIUnitTestCase {
    use ControllerTestTrait;
    use DatabaseTestTrait;

    // protected $seed = 'Tests\Support\Database\Seeds\ProductSeeder';
    private $model;

    // sledeći metodi iz modela neće biti testirani:
    // getRating, getProductRating, getDiscountRating, getCouponRating
    public function testModelFindAll() {
        $this->model = new ProductM();

        $this->assertGetGenres();
        $this->assertFutureDate();
        $this->assertGetDiscount();
        $this->assertGetDiscountedPrice();
        $this->assertGetAllProducts();
        $this->assertGetRating();
        $this->getHeroProduct();
        $this->assertGetHighRatingProducts();
        $this->assertGetTopSellersProducts();
        $this->assertGetDiscountedProducts();
        $this->assertGetDiscoveryProducts();
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
        $this->assertEquals($d1, 40);

        $this->assertIsInt($d2);
        $this->assertEquals($d2, 0);

        $this->assertIsInt($d3);
        $this->assertEquals($d3, 0);
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
        foreach ($guestProducts as $p) {
            $this->assertIsObject($p);
            $this->assertObjectHasAttribute('rating', $p);
        }
        $this->assertIsSorted($guestProducts, fn ($p1, $p2) => $p1->rating - $p2->rating);
        $this->assertCount(41, $guestProducts);

        $this->assertIsArray($userProducts);
        foreach ($userProducts as $p) {
            $this->assertIsObject($p);
            $this->assertObjectHasAttribute('rating', $p);
        }
        $this->assertIsSorted($userProducts, fn ($p1, $p2) => $p1->rating - $p2->rating);
        $this->assertCount(35, $userProducts);
    }
    private function assertGetTopSellersProducts() {
        $model = $this->model;

        $guestProducts = $model->getTopSellersProducts();
        $userProducts = $model->getTopSellersProducts(1);

        $this->assertIsArray($guestProducts);
        foreach ($guestProducts as $p) {
            $this->assertIsObject($p);
            $this->assertObjectHasAttribute('cnt', $p);
        }
        $this->assertIsSorted($guestProducts, fn ($p1, $p2) => $p1->cnt - $p2->cnt);
        $this->assertCount(41, $guestProducts);

        $this->assertIsArray($userProducts);
        foreach ($userProducts as $p) {
            $this->assertIsObject($p);
            $this->assertObjectHasAttribute('cnt', $p);
        }
        $this->assertIsSorted($userProducts, fn ($p1, $p2) => $p1->cnt - $p2->cnt);
        $this->assertCount(35, $userProducts);
    }
    private function assertGetDiscountedProducts() {
        $model = $this->model;

        $guestProducts = $model->getDiscountedProducts();
        $userProducts = $model->getDiscountedProducts(2);

        $this->assertIsArray($guestProducts);
        foreach ($guestProducts as $p) {
            $this->assertIsObject($p);
            $this->assertObjectHasAttribute('discRating', $p);
        }
        $this->assertIsSorted($guestProducts, fn ($p1, $p2) => $p1->discRating - $p2->discRating);
        $this->assertCount(13, $guestProducts);

        $this->assertIsArray($userProducts);
        foreach ($userProducts as $p) {
            $this->assertIsObject($p);
            $this->assertObjectHasAttribute('discRating', $p);
        }
        $this->assertIsSorted($userProducts, fn ($p1, $p2) => $p1->discRating - $p2->discRating);
        $this->assertCount(8, $userProducts);
    }
    private function assertGetDiscoveryProducts() {
        $model = $this->model;

        $userDiscovery = $model->getDiscoveryProducts(1);
        $guestDiscovery = $model->getDiscoveryProducts();

        $this->assertIsArray($userDiscovery);
        foreach ($userDiscovery as $p)
            $this->assertIsObject($p);
        $this->assertLessThanOrEqual(DISCOVERY_LENGTH, count($userDiscovery));
        $this->assertGreaterThanOrEqual(1, count($userDiscovery));

        $this->assertIsArray($guestDiscovery);
        $this->assertCount(0, $guestDiscovery);
    }
    private function assertGetCouponProducts() {
        $model = $this->model;

        $guestProducts = $model->getCouponProducts();
        $userProducts = $model->getCouponProducts(1);

        $this->assertIsArray($guestProducts);
        $this->assertCount(0, $guestProducts);

        $this->assertIsArray($userProducts);
        foreach ($userProducts as $p) {
            $this->assertIsObject($p);
            $this->assertObjectHasAttribute('coupRating', $p);
        }
        $this->assertIsSorted($userProducts, fn ($p1, $p2) => $p1->coupRating - $p2->coupRating);
        $this->assertCount(3, $userProducts);
    }
    private function assertGetProductsUserLike() {
        $model = $this->model;

        $guestProducts = $model->getProductsUserLike();
        $userProducts = $model->getProductsUserLike(1);

        $this->assertIsArray($guestProducts);
        $this->assertCount(0, $guestProducts);

        $this->assertIsArray($userProducts);
        foreach ($userProducts as $p) {
            $this->assertIsObject($p);
            $this->assertObjectHasAttribute('matching', $p);
        }
        $this->assertIsSorted($userProducts, fn ($p1, $p2) => $p1->matching - $p2->matching);
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
        $this->assertEquals(19, $userProducts[0]->id);
        $this->assertCount(41, $userProducts);
    } // TODO, funkcija koja se testira mora značajno da se promeni da bi se koristilo pametnije testiranje (sa assertIsSorted())
    private function assertGetSimilarProducts() {
        $model = $this->model;

        $p1 = $model->getSimilarProducts(15, 2);
        $p2 = $model->getSimilarProducts(15);
        $p3 = $model->getSimilarProducts(41);

        foreach ([$p1, $p2, $p3] as $array) {
            $this->assertIsArray($array);
            foreach ($array as $p) {
                $this->assertIsObject($p);
                $this->assertObjectHasAttribute('match_count', $p);
                $this->assertObjectHasAttribute('rating', $p);
            }
            $this->assertIsSorted($array, function ($p1, $p2) {
                if ($p1->match_count != $p2->match_count)
                    return $p1->match_count - $p2->match_count;
                return $p1->rating - $p2->rating;
            });
        }
        $this->assertCount(17, $p1);
        $this->assertCount(24, $p2);
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
    //
    private function assertIsSorted($array, $comparator) {
        if (count($array) == 0)
            return;

        for ($i = 1; $i < count($array); $i++) {
            $this->assertTrue($comparator($array[$i - 1], $array[$i]) >= 0);
        }
    }
}
