<?php

namespace CodeIgniter;

use App\Models\OwnershipM;
use App\Models\UserM;
use App\Models\BundleM;
use App\Models\ReviewVoteM;
use App\Models\RelationshipM;
use App\Models\CouponM;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

class UserTest extends CIUnitTestCase {
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;
    protected function test() {
        $_SESSION['user_id'] = 21;
        $routes = [
            ['get', 'http://localhost:8080/', '\App\Controllers\User::class'],
        ];

        return $this->withSession()->withRoutes($routes);
    }

    protected function test2() {
        $_SESSION['user_id'] = 22;
        $routes = [
            ['get', 'http://localhost:8080/', '\App\Controllers\User::class'],
        ];

        return $this->withSession()->withRoutes($routes);
    }

    protected function test3() {
        $_SESSION['user_id'] = 17;
        $routes = [
            ['get', 'http://localhost:8080/', '\App\Controllers\User::class'],
        ];

        return $this->withSession()->withRoutes($routes);
    }

    public function test_index() {
        $result = $this->test()->get('user/index');
        $this->assertTrue($result->see("Popular Products"));
    }

    public function test_addFunds() {
        $result = $this->test()->get('user/addfunds');
        $this->assertTrue($result->see("Add Funds"));
    }

    //  public function test_addFundsSubmitSuccess() {
    //     $user = (new UserM())->find(21);
    //     $userBalanceOld = $user->balance;
    //     $funds = 10;
    //      $result = $this->test()->call('post', 'user/addFundsSubmit', ['funds' => $funds]);
    //      $user = (new UserM())->find(21);
    //     $userBalanceNew = $user->balance;
    //      $this->assertEquals($userBalanceOld + $funds, $userBalanceNew);
    //  }

    public function test_addFundsSubmitFailNegativeFund() {
        $funds = -10;
        $result = $this->test()->call('post', 'user/addFundsSubmit', ['funds' => $funds]);
        $this->assertTrue($result->see("The funds field must contain a number greater than 0."));
    }

    public function test_addFundsSubmitFailNotNumber() {
        $funds = 'g';
        $result = $this->test()->call('post', 'user/addFundsSubmit', ['funds' => $funds]);
        $this->assertTrue($result->see("The funds field must contain a number greater than 0."));
    }

    public function test_addFundsSubmitFailEmptyField() {
        $funds = null;
        $result = $this->test()->call('post', 'user/addFundsSubmit', ['funds' => $funds]);
        $this->assertTrue($result->see("Add Funds"));
    }

    public function test_buyProduct() {
        $result = $this->test()->get('user/buyProduct/1');
        $this->assertTrue($result->see("Confirm Purchase"));
    }

    public function test_buyProductSubmitSuccessForMyself() {
        $kupljen = false;
        $result = $this->test()->get('user/buyProductSubmit/2', ['buyOptions' => 21]);

        $ownershipM = new OwnershipM();
        $userProducts = $ownershipM->where('id_user', 21)->findAll();

        foreach ($userProducts as $userProduct) {
            if ($userProduct->id_product == 2) {
                $kupljen = true;
            }
        }
        $this->assertEquals(true, $kupljen);
    }

    public function test_buyProductSubmitSuccessForAFriend() {
        $kupljen = false;
        $result = $this->test()->get('user/buyProductSubmit/2', ['buyOptions' => 25]);

        $ownershipM = new OwnershipM();
        $userProducts = $ownershipM->where('id_user', 25)->findAll();

        foreach ($userProducts as $userProduct) {
            if ($userProduct->id_product == 2) {
                $kupljen = true;
            }
        }
        $this->assertEquals(true, $kupljen);
    }

    public function test_buyProductSubmitFailedAlreadyBought() {
        $result = $this->test()->get('user/buyProductSubmit/6', ['buyOptions' => 21]);
        $this->assertTrue($result->see("User already owns this product."));
    }

    public function test_buyProductSubmitFailedAlreadyBoughtForAFriend() {
        $result = $this->test()->get('user/buyProductSubmit/19', ['buyOptions' => 25]);
        $this->assertTrue($result->see("User already owns this product."));
    }

    public function test_buyProductSubmitFailedNoMoney() {
        $result = $this->test2()->get('user/buyProductSubmit/19', ['buyOptions' => 22]);
        $this->assertTrue($result->see("You have insufficient funds."));
    }

    public function test_buyProductSubmitFailedNoMoneyForAFriend() {
        $result = $this->test2()->get('user/buyProductSubmit/18', ['buyOptions' => 25]);
        $this->assertTrue($result->see("You have insufficient funds."));
    }

    public function test_buyProductSubmitFailedNoBaseGame() {
        $result = $this->test()->get('user/buyProductSubmit/29', ['buyOptions' => 21]);
        $this->assertTrue($result->see("User doesn't own the base product."));
    }

    public function test_buyProductSubmitFailedNoBaseGameForAFriend() {
        $result = $this->test()->get('user/buyProductSubmit/29', ['buyOptions' => 25]);
        $this->assertTrue($result->see("User doesn't own the base product."));
    }

    public function test_editProfile() {
        $result = $this->test()->get('user/editprofile');
        $this->assertTrue($result->see("Edit"));
    }

    public function test_friendrequests() {
        $result = $this->test()->get('user/friendrequests');
        $this->assertTrue($result->see("INCOMING REQUESTS"));
    }

    public function test_makeReviewSubmit() {
        $text = "Test Review";
        $rating = 4;
        $result = $this->test()->call('post', 'user/makeReviewSubmit/2', ['rating' => $rating, 'text' => $text]);
        $ownershipM = new OwnershipM();
        $review = $ownershipM->where('id_user', 21)->where('id_product', 2)->first();
        $flag = false;
        if ($review->rating == $rating && $review->text == $text)
            $flag = true;
        $this->assertEquals(true, $flag);
    }

    public function test_awardUser() {
        $result = $this->test()->get('user/awardUser/25');
        $this->assertTrue($result->see("Please slide the bar and press Award User."));
    }

    public function test_deleteReviewSubmit() {
        $result = $this->test()->get('user/deleteReviewSubmit/2');
        $ownershipM = new OwnershipM();
        $review = $ownershipM->where('id_user', 21)->where('id_product', 2)->first();
        $this->assertNull($review->text);
    }

    public function test_buyBundle() {
        $result = $this->test()->get('user/buyBundle/5');
        $this->assertTrue($result->see("Confirm Purchase"));
    }

    public function test_buyBundleSubmitSuccess() {
        $kupljen = true;

        $bundle = (new BundleM())->find(5);
        $products = iterator_to_array((new BundleM())->bundleProducts(5));
        $price = (new BundleM())->bundlePrice($products, $bundle->discount, 21);
        $result = $this->test()->call('post', 'user/buyBundleSubmit/5', ["final" => $price['final']]);

        $products = (new BundleM())->bundleProducts(5);

        $ownershipM = new OwnershipM();
        $userProducts = $ownershipM->where('id_user', 21)->findAll();

        foreach ($products as $product) {
            $userProduct = $ownershipM->where('id_user', 21)->where('id_product', $product->id)->first();
            if (!$userProduct) {
                $kupljen = false;
            }
        }

        $this->assertEquals(true, $kupljen);
    }

    public function test_buyBundleSubmitFailedAlreadyBoughtAll() {
        $ownershipM = new OwnershipM();
        $userProductOldCount = $ownershipM->where('id_user', 17)->countAllResults();

        $bundle = (new BundleM())->find(5);
        $products = iterator_to_array((new BundleM())->bundleProducts(5));
        $price = (new BundleM())->bundlePrice($products, $bundle->discount, 21);
        $result = $this->test()->call('post', 'user/buyBundleSubmit/5', ["final" => $price['final']]);

        $userProductNewCount = $ownershipM->where('id_user', 17)->countAllResults();

        $ownershipM->where('id_product', 26)->where('id_user', 21)->delete();
        $ownershipM->where('id_product', 27)->where('id_user', 21)->delete();
        $ownershipM->where('id_product', 28)->where('id_user', 21)->delete();
        $ownershipM->where('id_product', 29)->where('id_user', 21)->delete();

        $this->assertEquals($userProductOldCount, $userProductNewCount);
    }

    public function test_buyBundleSubmitFailedNoMoney() {

        $ownershipM = new OwnershipM();
        $userProductOldCount = $ownershipM->where('id_user', 17)->countAllResults();

        $bundle = (new BundleM())->find(5);
        $products = iterator_to_array((new BundleM())->bundleProducts(5));
        $price = (new BundleM())->bundlePrice($products, $bundle->discount, 17);
        $result = $this->test3()->call('post', 'user/buyBundleSubmit/5', ["final" => $price['final']]);

        $userProductNewCount = $ownershipM->where('id_user', 17)->countAllResults();

        $this->assertEquals($userProductOldCount, $userProductNewCount);
    }

    public function test_coupons() {
        $result = $this->test()->get('user/coupons');
        $this->assertTrue($result->see("My Coupons"));
    }

    //  public function test_editProfileSubmit() {
    //     $nicknameNew = "bosko123";
    //    $descNew = "Profesor engleskog jezika";
    //   $result = $this->test2()->call('post', 'user/editProfileSubmit', ["description" => $descNew, "nickname" => $nicknameNew,//"profile_pic" => NULL]);
    // }

    public function test_likeAjaxLike() {
        $reviewVoteM = new ReviewVoteM();
        $result = $this->test()->call('post', 'user/likeAjax', ["user" => 22, "product" => 24, "like" => 1]);
        $row = $reviewVoteM->where('id_user', 21)->where('id_poster', 22)->where('id_product', 24)->where('like', 1)->countAllResults();
        assertEquals(1, $row);
    }

    public function test_likeAjaxLikeToLike() {
        $reviewVoteM = new ReviewVoteM();
        $result = $this->test()->call('post', 'user/likeAjax', ["user" => 22, "product" => 24, "like" => 1]);
        $row = $reviewVoteM->where('id_user', 21)->where('id_poster', 22)->where('id_product', 24)->where('like', 1)->countAllResults();
        assertEquals(0, $row);
    }

    public function test_likeAjaxDislike() {
        $reviewVoteM = new ReviewVoteM();
        $result = $this->test()->call('post', 'user/likeAjax', ["user" => 22, "product" => 24, "like" => 0]);
        $row = $reviewVoteM->where('id_user', 21)->where('id_poster', 22)->where('id_product', 24)->where('like', 0)->countAllResults();
        assertEquals(1, $row);
    }

    public function test_likeAjaxDislikeToLike() {
        $reviewVoteM = new ReviewVoteM();
        $result = $this->test()->call('post', 'user/likeAjax', ["user" => 22, "product" => 24, "like" => 1]);
        $row = $reviewVoteM->where('id_user', 21)->where('id_poster', 22)->where('id_product', 24)->where('like', 1)->countAllResults();
        assertEquals(1, $row);
    }

    public function test_likeAjaxLikeToDislike() {
        $reviewVoteM = new ReviewVoteM();
        $result = $this->test()->call('post', 'user/likeAjax', ["user" => 22, "product" => 24, "like" => 0]);
        $row = $reviewVoteM->where('id_user', 21)->where('id_poster', 22)->where('id_product', 24)->where('like', 0)->countAllResults();
        assertEquals(1, $row);
    }

    public function test_likeAjaxDislikeToDislike() {
        $reviewVoteM = new ReviewVoteM();
        $result = $this->test()->call('post', 'user/likeAjax', ["user" => 22, "product" => 24, "like" => 0]);
        $row = $reviewVoteM->where('id_user', 21)->where('id_poster', 22)->where('id_product', 24)->where('like', 0)->countAllResults();
        assertEquals(0, $row);
    }

    public function test_friendAjaxAccept() {
        $result = $this->test()->call('post', 'user/friendAjax', ["user" => 29, "relationship" => 2]);
        $relationshipM = new RelationshipM();
        $status = $relationshipM->getStatus(21, 29);
        assertEquals(1, $status);
        $relationshipM->set('status', 0)->where('id_user1', 29)->where('id_user2', 21)->update();
    }

    public function test_friendAjaxDecline() {
        $result = $this->test()->call('post', 'user/friendAjax', ["user" => 26, "relationship" => 3]);
        $relationshipM = new RelationshipM();
        $status = $relationshipM->getStatus(21, 26);
        assertEquals(-1, $status);
        $relationshipM->insert([
            'id_user1' => 26,
            'id_user2' => 21,
            'status' => 0
        ]);
    }

    public function test_friendAjaxCancel() {
        $result = $this->test()->call('post', 'user/friendAjax', ["user" => 23, "relationship" => 0]);
        $relationshipM = new RelationshipM();
        $status = $relationshipM->getStatus(21, 23);
        assertEquals(-1, $status);
        $relationshipM->insert([
            'id_user1' => 21,
            'id_user2' => 23,
            'status' => 0
        ]);
    }

    public function test_ajaxUserLoad() {
        $nickname = 'cviki76';
        $result = $this->test()->call('post', 'user/ajaxUserLoad', ['nadimak' => $nickname]);
        $json = $result->getJSON();

        $result->assertEquals('http://localhost:8080/user/profile/21', json_decode($json));
    }

    public function test_ajaxUserSearch() {
        $nickname = 'cvik';
        $result = $this->test()->call('post', 'user/ajaxUserSearch', ['q' => $nickname]);
        $json = $result->getJSON();
        $result->assertEquals('cviki76', json_decode(json_decode($json))[0]->text);
    }

    public function test_awardUserSubmitCoupon() {
        $points = 1500;
        $couponsCountOld = ((new CouponM))->where('id_owner', 25)->countAllResults();
        $result = $this->test()->call('post', 'user/awardUserSubmit/25', ['points' => $points]);
        $couponsCountNew = ((new CouponM))->where('id_owner', 25)->countAllResults();
        $result->assertEquals($couponsCountOld + 1, $couponsCountNew);
    }

    public function test_awardUserSubmitNoCoupon() {
        $points = 100;
        $couponsCountOld = ((new CouponM))->where('id_owner', 25)->countAllResults();
        $result = $this->test()->call('post', 'user/awardUserSubmit/25', ['points' => $points]);
        $couponsCountNew = ((new CouponM))->where('id_owner', 25)->countAllResults();
        $result->assertEquals($couponsCountOld, $couponsCountNew);
    }

    public function test_logout() {
        $result = $this->test()->get('user/logout');
        $this->assertNull($_SESSION["user_id"]);
    }
}
