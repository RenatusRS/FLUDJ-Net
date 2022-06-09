<?php

namespace CodeIgniter;

use App\Models\OwnershipM;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\ProductM;
use App\Models\UserM;
use App\Models\BundleM;

class AdminTest extends CIUnitTestCase {
    //use ControllerTestTrait;
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected function test() {
        $_SESSION['user_id'] = 1;
        $routes = [
            ['get', 'http://localhost:8080/', '\App\Controllers\Admin::class'],
        ];

        return $this->withSession()->withRoutes($routes);
    }

    public function test_manageProductPage() {
        $result = $this->test()->get('admin/manageProduct/3');
        $this->assertTrue($result->see("Product Details"));
    }

    public function test_manageBundlePage() {
        $result = $this->test()->get('admin/manageBundle');
        $this->assertTrue($result->see("Create the bundle first then change its contents."));
    }

    public function test_addBundlePage() {
        $result = $this->test()->get('admin/addBundle');
        $this->assertTrue($result->see("Bundle Name"));
    }

    public function test_setDiscountPage() {
        $result = $this->test()->get('admin/setDiscount/4');
        $this->assertTrue($result->see("Set Discount"));
    }

    public function test_setDiscountSubmitSuccess() {
        $myDiscount = '45';
        $result = $this->test()->call('post', 'admin/setdiscountsubmit/12', ['discount' => $myDiscount, 'expDate'  => '2022-06-14',]);
        $product = (new ProductM())->find('12');
        $this->assertEquals($product->discount, $myDiscount);
    }

    public function test_setDiscountSubmitWrongDiscount() {
        $myDiscount = 'a';
        $result = $this->test()->call('post', 'admin/setdiscountsubmit/12', ['discount' => $myDiscount, 'expDate'  => '2022-06-14',]);
        $this->assertTrue($result->see("The discount field must contain a number greater than or equal to 5."));
    }

    public function test_setDiscountSubmitWrongDate() {
        $myDiscount = '45';
        $result = $this->test()->call('post', 'admin/setdiscountsubmit/12', ['discount' => $myDiscount, 'expDate'  => '2022-06-08',]);
        $this->assertTrue($result->see("Set Discount"));
    }

    public function test_deleteUser() {
        $result = $this->test()->get('admin/deleteUser/15');
        $user = (new UserM())->find('15');
        $this->assertNull($user);
    }

    public function test_deleteProduct() {
        $result = $this->test()->get('admin/deleteProduct/33');
        $prod = (new ProductM())->find('33');
        $this->assertNull($prod);
    }

    public function test_deleteBundle() {
        $result = $this->test()->get('admin/deleteBundle/2');
        $prod = (new BundleM())->find('2');
        $this->assertNull($prod);
    }

    // public function test_deleteReview() {
    //     $result=$this->test()->get('admin/DeleteReviewAdminSubmit/12/17');
    //     $res=(new OwnershipM())->asArray()->where('id_product', '12')->where('id_user','17')->findAll();
    //     if(!$res) $this->assertNull($res[0]['text']);
    //     else {
    //         $this->assertNull($res);
    //     }
    // }   

}
