<?php

/**
 * @author
 * 	Djordje Stanojevic 2019/0288
 * 	Uros Loncar 2019/0691
 * 	Luka Cvijan 2019/0154
 *  Fedja Mladenovic 2019/0613
 * 
 * Opis: Kontroler za korisnika
 * 
 * @version 1.3
 * 
 */

namespace App\Controllers;

use App\Models\GenreM;
use App\Models\ProductM;
use App\Models\UserM;
use App\Models\OwnershipM;
use App\Models\RelationshipM;
use App\Models\ReviewVoteM;
use App\Models\BundleM;
use App\Models\BundledProductsM;
use App\Models\CouponM;

class User extends BaseController {
    public function index() {
        $user = $this->getUser();
        $this->frontpage($user->id);
    }

    /**
     * Odjavljivanje korisnika
     *
     * @return void
     */
    public function logout() {
        $this->session->destroy();
        return redirect()->to(site_url('/'));
    }

    /**
     *
     * Prikaz stranice za uplacivanje novca
     *
     * @return void
     */
    public function addFunds() {
        $this->show('addFunds');
    }

    /**
     *
     * Procesiranje uplacivanja novca
     *
     * @return void
     */
    public function addFundsSubmit() {
        if (!$this->validate(['funds' => 'required|numeric|greater_than[0]']))
            return $this->show('addFunds', ['errors' => $this->validator->getErrors()]);

        $user = $this->getUser();

        $user->balance += $this->request->getVar('funds');

        $userM = new UserM();
        $userM->update($user->id, [
            'balance' => $user->balance
        ]);

        return redirect()->to(site_url("user/profile/"));
    }

    /**
     *
     * Prikaz stranice za kupovanje proizvoda sa id-jem $id
     *
     * @param  integer $id id proizvoda
     * @return void
     */
    public function buyProduct($id) {
        $user = $this->getUser();

        $friends =  (new RelationshipM())->getFriends($user->id);

        $productM = new ProductM();
        $product = $productM->find($id);

        $this->show('buyProduct', ['product' => $product, 'friends' => $friends]);
    }

    /**
     * userViewProduct performs all user-specific actions regarding viewing products
     *
     *
     * @param  integer $id id of product that is being viewed
     * @return array array containing user-specific info such as if user has review of product, and if user has admin privileges
     */
    protected function userViewProduct($id) {
        $user = $this->getUser();

        $product_review = (new OwnershipM())->where('id_product', $id)->where('id_user', $user->id)->first();

        if ($user->review_ban == 1 || !(isset($product_review)))
            $product_review = NULL;

        return ['product_review' => $product_review];
    }

    /**
     *
     * Procesiranje kupovine proizvoda sa id-jem $id
     *
     * @param  integer $id id proizvoda
     * @return void
     */
    public function buyProductSubmit($id) {
        $userFrom = $this->getUser();

        $friends = (new RelationshipM())->getFriends($userFrom->id);

        $userFor = null;

        if ($this->request->getVar('buyOptions') != $userFrom->id) {
            $userM = new UserM();
            $userFor = $userM->find($this->request->getVar('buyOptions'));
        }

        $productM = new ProductM();
        $product = $productM->find($id);

        $productPrice = $productM->getDiscountedPrice($id);
        $coupon = CouponM::couponWorth($userFrom->id, $id);
        $productPrice *= ((100 - $coupon) / 100);

        $user = $userFor == null ? $userFrom : $userFor;

        $ownershipM = new OwnershipM();
        $userProducts = $ownershipM->where('id_user', $user->id)->findAll();

        if ($product->base_game != null) {
            $baseGameForDLC = $ownershipM->where('id_user', $user->id)->where('id_product', $product->base_game)->findAll();
            if ($baseGameForDLC == null) {
                return  $this->show('buyProduct', [
                    'product' => $product,
                    'friends' => $friends,
                    'message' => "User doesn't own the base product.",
                ]);
            }
        }

        foreach ($userProducts as $userProduct) {
            if ($userProduct->id_product == $product->id) {
                return  $this->show('buyProduct', [
                    'product' => $product,
                    'friends' => $friends,
                    'message' => 'User already owns this product.',
                ]);
            }
        }
        if ($userFrom->balance <  $productPrice) {
            return  $this->show('buyProduct', [
                'product' => $product,
                'friends' => $friends,
                'message' => 'You have insufficient funds.',
            ]);
        }

        $userFrom->balance -=  $productPrice;
        $userM->update($userFrom->id, [
            'balance' => $userFrom->balance
        ]);

        $ownershipM->insert([
            'id_product' => $product->id,
            'id_user' => $user->id,
            'text' => null,
            'rating' => null
        ]);

        CouponM::removeCoupon($userFrom->id, $product->id);

        CouponM::awardPoints($userFrom->id, $product->price);

        return redirect()->to(site_url("user/product/{$product->id}"));
    }

    /**
     * Prikaz stranice sa opcijama za izmenu/unos podataka
     *
     * @return void
     */
    public function editProfile() {
        $this->show('editProfile.php');
    }

    /**
     * Prikaz stranice sa listom zahteva prijateljsva (odlazeci i dolazeci)
     *
     * @return void
     */
    public function friendRequests() {
        $user = $this->getUser();
        $relationshipM = new RelationshipM();

        $requesters = $relationshipM->getIncoming($user);
        $requestedTo = $relationshipM->getSent($user);

        $this->show('friendRequests.php', [
            'requesters' => $requesters,
            'requestedTo' => $requestedTo,
        ]);
    }

    /**
     *
     * Procesiranje pravljenja recenzije za proizvod sa id-jem $id
     *
     * @param  integer $id id proizvoda
     * @return void
     */
    public function makeReviewSubmit($id) {
        $text = $this->request->getVar('text');
        if (empty($text)) $text = null;
        $rating = $this->request->getVar('rating');
        $user = $this->getUser();

        $oldRating = (new OwnershipM())->getRating($user->id, $id);

        (new OwnershipM())
            ->where('id_product', $id)
            ->where('id_user', $user->id)
            ->set(['rating' => $rating, 'text' => $text])
            ->update();

        $product = (new ProductM())->find($id);
        (new ProductM())->update($id, [
            'rev_cnt' => $product->rev_cnt + (($oldRating == 0) ? 1 : 0),
            'rev_sum' => $product->rev_sum + ($rating - $oldRating)
        ]);

        return redirect()->to(site_url("user/product/{$product->id}"));
    }

    public function awardUser($idUser) {
        $user = $this->getUser();
        $awardee = (new UserM())->find($idUser);

        $this->show('awardPoints', ['currentUser' => $user, 'awardee' => $awardee]);
    }

    public function awardUserSubmit($idUser) {
        $user = $this->getUser();
        $userM = new UserM();

        $receiver = $userM->find($idUser);
        $sender = $userM->find($user->id);

        $pointsAwarded = $this->request->getVar('points');

        $user->points = $sender->points - $pointsAwarded;

        $receiverPoints = $receiver->overflow + $pointsAwarded;
        $overflow = ($receiverPoints % COUPON_POINTS);

        $userM->update($user->id, [
            'points' => $user->points
        ]);
        $userM->update($receiver->id, [
            'overflow' => $overflow
        ]);

        $data = [
            'currentPoints' => $sender->points - $pointsAwarded,
            'pointsAwarded' => $pointsAwarded,
            'awardeePoints' => $overflow,
        ];

        while ($receiverPoints >= COUPON_POINTS) {
            CouponM::awardCoupon($idUser);
            $receiverPoints -= COUPON_POINTS;
        }

        redirect()->to(site_url("user/profile/{$idUser}"));
    }

    /**
     * Ajax funkcija za azurno ucitavanje rezultata korisnika
     * 
     * @return array(data)
     */
    public function ajaxUserSearch() {
        helper(['form', 'url']);

        $data = [];
        $db      = \Config\Database::connect();
        $builder = $db->table('user');
        $request = \Config\Services::request();
        $query = $builder->like('nickname', $request->getVar('q'))->select('id, nickname as text')->limit(7)->get();
        $data = $query->getResult();
        echo json_encode($data);
    }

    /**
     * Ajax funkcija za promenu stranice na profil odabranog pretrazenog korisnika
     * 
     * @return String
     */
    public function ajaxUserLoad() {
        $nickname = $_GET['nadimak'];
        $myUsr = (new UserM())->where('nickname', $nickname)->first();
        return base_url("user/profile/" . $myUsr->id);
    }


    /**
     * Procesiranje brisanja recenzije koja se odnosi na proizvod sa id-jem $id
     *
     * @param  integer $id id proizvoda
     * @return void
     */
    public function deleteReviewSubmit($id) {
        $user = $this->getUser();

        return $this->deleteReview($id, $user->id, true);
    }

    /**
     * prikaz stranice za kupovanje kolekcije
     *
     * @param  integer $id id kolekcije
     * @return void
     */
    public function buyBundle($id = null) {
        $user = $this->getUser();
        $bundle = (new BundleM())->find($id);

        if (!isset($bundle)) {
            return redirect()->to(site_url());
        }

        $products = iterator_to_array((new BundleM())->bundleProducts($id));
        $price = (new BundleM())->bundlePrice($products, $bundle->discount, $user->id);

        $this->show('buyBundle', [
            'bundle' => $bundle,
            'price' => $price,
        ]);
    }

    public function buyBundleSubmit($id) {
        $finalPrice = $this->request->getVar('final');
        $user = $this->getUser();

        if ($user->balance < $finalPrice) {
            return redirect()->to(site_url("user/bundle/{$id}"));
        }

        $products = (new BundleM())->bundleProducts($id);
        foreach ($products as $product) {
            (new OwnershipM())
                ->acquire($user->id, $product->id);
        }

        $user->balance -= $finalPrice;
        (new UserM())->update($user->id, [
            'balance' => $user->balance
        ]);

        CouponM::awardPoints($user->id, $finalPrice);

        return redirect()->to(site_url("user/bundle/{$id}"));
    }

    private function validateUser($profile_pic) {
        $notValid = ($profile_pic && !$this->validate([
            'profile_pic' => 'uploaded[profile_pic]|ext_in[profile_pic,jpg]|is_image[profile_pic]'
        ]));

        return !$notValid;
    }

    public function editProfileSubmit() {
        $user = $this->getUser();
        $uploaded = (is_uploaded_file($_FILES['profile_pic']['tmp_name']));
        if (!$this->validateUser($uploaded))
            return $this->show('editProfile', ['errors' => $this->validator->getErrors()]);

        $featured_review = $this->request->getVar('f_review');
        if ($featured_review == "none") $featured_review = null;
        if ($this->request->getVar('nickname') != "") {
            (new UserM())
                ->set('nickname', $this->request->getVar('nickname'))
                ->set('real_name', $this->request->getVar('real_name'))
                ->set('description', $this->request->getVar('description'))
                ->set('featured_review', $featured_review)
                ->where('id', $user->id)
                ->update();

            $this->upload('uploads/user/', 'profile_pic', $user->id);
        }

        return redirect()->to(site_url("user/profile/"));
    }

    public function coupons() {
        $user = $this->getUser();

        $this->show('coupons', [
            'coupons' => iterator_to_array((new CouponM())->getAllCoupons($user->id)),
        ]);
    }

    public function likeAjax() {
        $data = $this->request->getVar();

        $poster = (new UserM())->find($data['user']);
        $user = $this->getUser();
        $id = $data['product'];
        $like = $data['like'];

        if ($poster->id == $user->id) return redirect()->to(site_url("User/Product/{$id}"));

        $review_voteM = new ReviewVoteM();

        $vote = $review_voteM->where("id_user", $user->id)->where("id_poster", $poster->id)->where("id_product", $id)->first();

        if ($vote) {
            if ($vote->like == $like)
                $review_voteM->where('id_product', $id)->where('id_user', $user->id)->where("id_poster", $poster->id)->delete();
            else
                $review_voteM->where('id_product', $id)->where('id_user', $user->id)->where("id_poster", $poster->id)->set(['like' => $like])->update();
        } else {
            $review_voteM->insert([
                'id_user' => $user->id,
                'id_poster' => $poster->id,
                'id_product' => $id,
                'like' => $like
            ]);
        }

        $votes = (new ReviewVoteM())->getVotes($id, $poster->id);

        echo json_encode(array(
            "state" => 1,
            "votes" => $votes,
        ));
    }

    public function friendAjax() {
        $data = $this->request->getVar();
        $relationshipM = new RelationshipM();
        $userMe = $this->getUser()->id;
        $userThem = $data['user'];
        $remove = false;

        switch ($data['relationship']) {
            case (-1):
                $relationshipM->insert([
                    'id_user1' => $userMe,
                    'id_user2' => $userThem,
                    'status' => 0
                ]);
                $state = 0;
                break;
            case (0):
                $relationshipM->where('id_user1', $userMe)->where('id_user2', $userThem)->delete();
                $state = -1;
                break;

            case (1):
                $relationshipM->where('id_user1', $userMe)->where('id_user2', $userThem)->OrWhere('id_user1', $userThem)->where('id_user2', $userMe)->delete();
                $state = -1;
                $remove = true;
                break;

            case (2):
                $relationshipM->set('status', 1)->where('id_user1', $userThem)->where('id_user2', $userMe)->update();
                $state = 1;
                break;
            case (3):
                $relationshipM->where('id_user1', $userThem)->where('id_user2', $userMe)->delete();
                $state = -1;
                break;
            default:
        }

        echo json_encode(array(
            "state" => $state,
            "remove" => $remove,
        ));
    }
}
