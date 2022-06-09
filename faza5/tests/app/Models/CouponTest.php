<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Config\Factories;

use App\Models\CouponM;

class CouponTest extends CIUnitTestCase {
    use ControllerTestTrait;
    use DatabaseTestTrait;

    // protected $seed = 'Tests\Support\Database\Seeds\CouponSeeder';
    private $model;

    public function testModelFindAll() {
        $this->model = new CouponM();

        $this->assertOwnsCoupon();
        $this->assertGetAllCoupons();
        $this->assertCouponWorth();
        $this->assertUpgradeCoupon();
        $this->assertRemoveCoupon();
        $this->assertAwardCoupon();
    }

    private function assertOwnsCoupon() {
        $model = $this->model;

        $owns = $model->ownsCoupon(1, 13);
        $doesntOwn = $model->ownsCoupon(1, 12);

        $this->assertIsBool($owns);
        $this->assertTrue($owns);

        $this->assertIsBool($doesntOwn);
        $this->assertFalse($doesntOwn);
    }
    private function assertGetAllCoupons() {
        $model = $this->model;

        $coupons = $model->getAllCoupons(1);
        $yesDLCoupons = $model->getAllCoupons(3, false);
        $noDLCoupons = $model->getAllCoupons(3, true);
        $noCoupons = $model->getAllCoupons(-1);


        $this->assertCoupons($coupons, 3);
        $this->assertCoupons($noDLCoupons, 4);
        $this->assertCoupons($yesDLCoupons, 5);
        $this->assertCoupons($noCoupons, 0);
    }
    private function assertCouponWorth() {
        $model = $this->model;

        $coupon1 = $model->couponWorth(1, 13);
        $coupon2 = $model->couponWorth(1, 21);
        $noCoupon1 = $model->couponWorth(1, -1);
        $noCoupon2 = $model->couponWorth(-1, 1);

        $this->assertIsInt($coupon1);
        $this->assertEquals(10, $coupon1);
        $this->assertIsInt($coupon2);
        $this->assertEquals(30, $coupon2);
        $this->assertIsInt($noCoupon1);
        $this->assertEquals(0, $noCoupon1);
        $this->assertIsInt($noCoupon2);
        $this->assertEquals(0, $noCoupon2);
    }
    private function assertUpgradeCoupon() {
        $model = $this->model;
    } // TODO
    private function assertRemoveCoupon() {
        $model = $this->model;
    } // TODO
    private function assertAwardCoupon() {
        $model = $this->model;
    } // TODO
    private function assertAwardPoints() {
        $model = $this->model;
    } // TODO

    // pomoćne
    private function assertCoupons($coupons, $supposedCount) {
        $this->assertIsIterable($coupons);
        $coupons = iterator_to_array($coupons);
        foreach ($coupons as $coupon) {
            $this->assertIsObject($coupon);
            $this->assertObjectHasAttribute('discount', $coupon);
        }
        $this->assertCount($supposedCount, $coupons);
    }
}
