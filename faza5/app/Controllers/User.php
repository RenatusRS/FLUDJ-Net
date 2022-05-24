<?php
/*
Autori:
	Djordje Stanojevic 2019/0288
	Uros Loncar 2019/0691
	Luka Cvijan 2019/0154
Opis: Kontroler za korisnika

@version 1.3
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

class User extends BaseController {
    public function index() {
        $productM = new ProductM();

        $heroP = $productM->getHeroProduct();
        $heroP->description = explode(".", $heroP->description, 2)[0] . ".";

        $highRatingP = $productM->getHighRatingProducts();
        $topSellerP = $productM->getTopSellersProducts();
        $discountedP = $productM->getDiscountedProducts();
        $discoveryP = $productM->getDiscoveryProducts();
        $couponP = $productM->getCouponProducts();
        $userLikeP = $productM->getProductsUserLike();
        $friendsLikeP = $productM->getProductsUserFriendsLike();

        $this->show(
            'index',
            [
                'heroP' => $heroP,
                'highRatingP' => $highRatingP,
                'topSellerP' => $topSellerP,
                'discountedP' => $discountedP,
                'discoveryP' => $discoveryP,
                'couponP' => $couponP,
                'userLikeP' => $userLikeP,
                'friendsLikeP' => $friendsLikeP
            ]
        );
    }



    /**
     *Odjavljivanje korisnika
     *@return void
     */
    public function logout() {
        $this->session->destroy();
        return redirect()->to(site_url('/'));
    }

    /** 
     *Prikaz svog ili tudjeg profila
     *@return void
     */
    public function profile($id = null) {
        $user = $id == null ? $this->session->get('user') : (new UserM())->find($id);
        if ($id == null) {
            $builder = \Config\Database::connect()->table('user');

            if ($this->request->getVar('nickname') != "") {
                $builder = $builder->set('nickname', $this->request->getVar('nickname'))->set('real_name', $this->request->getVar('real_name'))
                    ->set('country', $this->request->getVar('location'))->set('description', $this->request->getVar('description'))
                    ->set('featured_review', $this->request->getVar('f_review'))->where('id', $user->id)->update();

                $this->upload('public/uploads/user/', 'profile_pic', $user->id);
            }
        }

        $this->show('user.php', ['user_profile' => $user]);
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

        $user = $this->session->get('user');

        $user->balance += $this->request->getVar('funds');

        $userM = new UserM();
        $userM->update($user->id, [
            'balance' => $user->balance
        ]);

        return redirect()->to(site_url("user/profile/"));
    }

    /**
     * 
     * Prikaz stranice za kupovanje proizvoda
     * 
     * @return void   
     */
    public function buyProduct($id) {
        $user = $this->session->get('user');

        $friends =  (new RelationshipM())->getFriends($user);

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
        $user = $this->session->get('user');

        $product_review = (new OwnershipM())->where('id_product', $id)->where('id_user', $user->id)->first();

        if ($user->review_ban == 1 || !(isset($product_review)))
            $product_review = NULL;

        $admin = $user->admin_rights;
        return ['product_review' => $product_review, 'admin' => $admin];
    }

    /**
     * 
     * Procesiranje kupovine proizvoda
     * 
     * @return void   
     */
    public function buyProductSubmit($id) {
        $userFrom = $this->session->get('user');

        $friends = (new RelationshipM())->getFriends($userFrom);

        $userFor = null;

        if ($this->request->getVar('buyOptions') != $userFrom->id) {
            $userM = new UserM();
            $userFor = $userM->find($this->request->getVar('buyOptions'));
        }

        $productM = new ProductM();
        $product = $productM->find($id);

        $productPrice = $productM->getDiscountedPrice($id);

        $user = $userFor == null ? $userFrom : $userFor;

        $ownershipM = new OwnershipM();
        $userProducts = $ownershipM->where('id_user', $user->id)->findAll();

        if ($product->base_game != null) {
            $baseGameForDLC = $ownershipM->where('id_user', $user->id)->where('id_product', $product->base_game)->findAll();
            if ($baseGameForDLC == null) {
                return  $this->show('buyProduct', ['product' => $product, 'friends' => $friends, 'message' => 'Nema base game!']);
            }
        }

        foreach ($userProducts as $userProduct) {
            if ($userProduct->id_product == $product->id) {
                return  $this->show('buyProduct', ['product' => $product, 'friends' => $friends, 'message' => 'Ima vec!']);
            }
        }
        if ($userFrom->balance <  $productPrice) {
            return  $this->show('buyProduct', ['product' => $product, 'friends' => $friends, 'message' => 'Nema novaca!']);
        }

        $userFrom->balance -=  $productPrice;
        $userFrom->points += $productPrice * 100;
        $userM->update($userFrom->id, [
            'balance' => $userFrom->balance,
            'points' => $userFrom->points
        ]);

        $ownershipM->insert([
            'id_product' => $product->id,
            'id_user' => $user->id,
            'text' => null,
            'rating' => null
        ]);

        return redirect()->to(site_url("user/product/{$product->id}"));
    }

    /**
     *Prikaz stranice sa opcijama za izmenu/unos podataka
     *@return void
     */
    public function editProfile() {
        $this->show('editProfile.php');
    }

    /**
     *Prikaz stranice sa listom zahteva prijateljsva (odlazeci i dolazeci)
     *@return void
     */
    public function friendRequests() {
        $user = $this->session->get('user');
        $relationshipM = new RelationshipM();

        $requesters = $relationshipM->getIncoming($user);
        $requestedTo = $relationshipM->getSent($user);

        $this->show('friendRequests.php', ['requesters' => $requesters, 'requestedTo' => $requestedTo]);
    }

    /**
     * 
     * Procesiranje pravljenja recenzije
     * 
     * @return void   
     */
    public function makeReviewSubmit($id) {
        $text = $this->request->getVar('text');
        $rating = $this->request->getVar('rating');
        $user = $this->session->get('user');

        (new OwnershipM())->where('id_product', $id)->where('id_user', $user->id)->set(['rating' => $rating, 'text' => $text])->update();

        $product = (new ProductM())->find($id);
        (new ProductM())->update($id, [
            'rev_cnt' => $product->rev_cnt + 1,
            'rev_sum' => $product->rev_sum + $rating
        ]);

        return redirect()->to(site_url("User/Product/{$product->id}"));
    }

    /**
     * 
     * Procesiranje lajkovanja recenzija
     * 
     * @return void   
     */
    public function LikeDislikeSubmit($id, $posterUsername) {
        $poster = (new UserM())->where('username', $posterUsername)->first();
        $user = $this->session->get('user');

        if ($poster->id == $user->id) return redirect()->to(site_url("User/Product/{$id}"));

        $review_voteM = new ReviewVoteM();

        $vote = $review_voteM->where("id_user", $user->id)->where("id_poster", $poster->id)->where("id_product", $id)->first();

        $like = $this->request->getVar('like');

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

        return redirect()->to(site_url("user/product/{$id}"));
    }

    /**
     *Ajax funkcija za azurno ucitavanje rezultata korisnika
     *@return array(data)
     */
    public function ajaxUserSearch() {
        helper(['form', 'url']);

        $data = [];
        $db      = \Config\Database::connect();
        $builder = $db->table('user');
        $request = \Config\Services::request();
        $query = $builder->like('nickname', $request->getVar('q'))->select('id, nickname as text')->limit(10)->get();
        $data = $query->getResult();
        echo json_encode($data);
    }

    /**
     *Ajax funkcija za promenu stranice na profil odabranog pretrazenog korisnika
     *@return String
     */
    public function ajaxUserLoad() {
        $nickname = $_GET['nadimak'];
        $myUsr = (new UserM())->where('nickname', $nickname)->first();
        return "profile/" . $myUsr->id;
    }

    /**
     *Prikaz stranice za pretragu korisnika
     *@return void
     */
    public function searchUser() {
        $this->show('searchUser.php');
    }

    /**
     *Ajax funkcija za azurno ucitavanje rezultata proizvoda
     *@return array(data)
     */
    public function ajaxProductSearch() {
        helper(['form', 'url']);

        $data = [];
        $db      = \Config\Database::connect();
        $builder = $db->table('product');
        $request = \Config\Services::request();
        $query = $builder->like('name', $request->getVar('q'))->select('id, name as text')->limit(10)->get();
        $data = $query->getResult();
        echo json_encode($data);
    }

    /**
     *Ajax funkcija za promenu stranice na odabrani proizvod
     *@return String
     */
    public function ajaxProductLoad() {
        $name = $_GET['ime'];
        $myProduct = (new ProductM())->where('name', $name)->first();
        return "Product/" . $myProduct->id;
    }

    /**
     *Prikaz stranice za pretragu proizvoda
     *@return void
     */
    public function searchProduct() {
        $this->show('searchProduct.php');
    }

    /** 
     * Procesiranje brisanja recenzije
     * @return void
     */
    public function deleteReviewSubmit($id) {
        $user = $this->session->get('user');

        $rating = (new OwnershipM())->where('id_product', $id)
                                    ->where('id_user', $user->id)
                                    ->rating;

        (new OwnershipM())->where('id_product', $id)->where('id_user', $user->id)->set(['rating' => NULL, 'text' => NULL])->update();

        $product = (new ProductM())->find($id);
        (new ProductM())->update($id, [
            'rev_cnt' => $product->rev_cnt - 1,
            'rev_sum' => $product->rev_sum - $rating
        ]);

        (new ReviewVoteM())->where('id_product', $id)->where("id_poster", $user->id)->delete();

        return redirect()->to(site_url("User/Product/{$id}"));
    }

    /**
     * prikaz stranice za kupovanje kolekcije
     *
     * @param  integer $id id kolekcije
     * @return void
     */
    public function buyBundle($id = null) {
        $user = $this->session->get('user');
        $bundle = (new BundleM())->find($id);

        if (!isset($bundle)) {
            return redirect()->to(site_url());
        }

        $friends = (new RelationshipM())->getFriends($user);
        $price = [
            'price'    => $this->request->getVar('price'),
            'discount' => $this->request->getVar('discount'),
            'final'    => $this->request->getVar('final'),
        ];

        $this->show('buyBundle', ['bundle' => $bundle, 'friends' => $friends, 'price' => $price]);
    }

    public function buyBundleSubmit($id) {
        $finalPrice = $this->request->getVar('final');
        $user = $this->session->get('user');

        if ($user->balance < $finalPrice) {
            return;
        }

        $products = $this->bundleProducts($id);
        foreach ($products as $product) {
            (new OwnershipM())
                ->acquire($user->id, $product->id);
        }

        $user->balance -= $finalPrice;
        (new UserM())->update($user->id, [
            'balance' => $user->balance
        ]);

        // TODO redirect
    }
}
