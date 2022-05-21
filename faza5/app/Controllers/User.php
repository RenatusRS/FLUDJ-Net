<?php
/*
Autori:
	Djordje Stanojevic 2019/0288
	Uros Loncar 2019/0691
	
Opis: Kontroler za korisnika

@version 1.3
*/

namespace App\Controllers;

use App\Models\GenreM;
use App\Models\ProductM;
use App\Models\UserM;
use App\Models\OwnershipM;
use App\Models\RelationshipM;

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

    /*
    Odjavljivanje korisnika
    @return void
    */
    public function logout() {
        $this->session->destroy();
        return redirect()->to(site_url('/'));
    }

    /*
    Prikaz svog ili tudjeg profila
    @return void
    */
    public function profile($id = null) {
        $user = $id == null ? $this->session->get('user') : (new UserM())->find($id);
        if ($id == null) {
            $builder = \Config\Database::connect()->table('user');
            $builder = $builder->set('nickname', $this->request->getVar('nickname'))->set('real_name', $this->request->getVar('real_name'))
                ->set('description', $this->request->getVar('description'))
                ->/*set('featured_review', $this->request->getVar('review'))*/where('id', $user->id)->update();
            $this->upload('uploads/user/', 'profile_pic', $user->id);
        }

        $this->show('user', ['user_profile' => $user]);
    }

    public function friends() {
        $this->show('friends.php');
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
        if ($userFrom->balance < $product->price) {
            return  $this->show('buyProduct', ['product' => $product, 'friends' => $friends, 'message' => 'Nema novaca!']);
        }

        $userFrom->balance -= $product->price;
        $userM->update($userFrom->id, [
            'balance' => $userFrom->balance
        ]);

        $ownershipM->insert([
            'id_product' => $product->id,
            'id_user' => $user->id,
            'text' => null,
            'rating' => null
        ]);

        return redirect()->to(site_url("user/product/{$product->id}"));
    }

    /*
    Prikaz opcija za izmenu/unos podataka
    @return void
    */
    public function editProfile() {
        $this->show('editProfile.php');
    }
}
