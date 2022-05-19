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

    /**
    *Prikaz sadrzaja na stranici
    *@return void
    */
    protected function show($page, $data = [], $viewDir="pages") {
        $data['controller'] = 'User';
        $data['user'] = $this->session->get('user');
        echo view('template/header_user', $data);
        echo view("$viewDir/$page", $data);
        echo view('template/footer');
    }

    protected function showError($page, $data) {
        $this->show($page, $data, "errors");
    }

    public function index() {
        $this->show('index');
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
            $builder = $builder->set('nickname', $this->request->getVar('nickname'))->set('real_name', $this->request->getVar('real_name'))
                ->set('country', $this->request->getVar('location'))->set('description', $this->request->getVar('description'))
                ->/*set('featured_review', $this->request->getVar('review'))*/where('id', $user->id)->update();
            $this->upload('public/uploads/user/', 'profile_pic', $user->id);
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

        return redirect()->to(site_url("User/Profile/"));
    }

    /**
     * 
     * Izracunavanje skora
     * 
     * @return double  
     */
    protected function getRating($positiveVotes, $negativeVotes) {
        if ($positiveVotes == 0 && $negativeVotes == 0) return 50;
        $totalVotes = $positiveVotes + $negativeVotes;
        $average = $positiveVotes / $totalVotes;
        $score = $average - ($average - 0.5) * 2 ** -log10($totalVotes + 1);

        return $score * 100;
    }

    /**
     * 
     * Trazenje najboljih recenzija za zadati proizvod
     * 
     * @return array(reviews)  
     */
    protected function getTopReviews($id) {
        $review_voteM = new ReviewVoteM();
        $ownershipM = new OwnershipM();
        $ownerships = $ownershipM->where("id_product", $id)->where("text !=", "NULL")->where("rating !=", "NULL")->findAll();

        $posterScore = array();
        $posterPosNeg = array();

        foreach ($ownerships as $ownership) {

            $userPoster = (new userM())->find($ownership->id_user);
            if ($userPoster->review_ban == 1) continue;

            $reviewsForPoster = $review_voteM->where('id_product', $id)->where('id_poster', $ownership->id_user)->findAll();
            $positive = 0;
            $negative = 0;

            foreach ($reviewsForPoster as $review) {
                if ($review->like == 0) $negative++;
                else $positive++;
            }

            $score = $this->getRating($positive, $negative);
            $posterScore[$ownership->id_user] = $score;
            $posterPosNeg[$ownership->id_user] = ["positive" => $positive, "negative" => $negative];
        }

        arsort($posterScore);

        $userM = new UserM();
        $reviews = array();

        foreach ($posterScore as $poster => $score) {
            $review = $ownershipM->where('id_product', $id)->where('id_user', $poster)->first();
            $user = $userM->find($poster);
            $reviews[$user->username] = ["review" => $review, "positive" => $posterPosNeg[$poster]["positive"], "negative" => $posterPosNeg[$poster]["negative"]];
        }

        return $reviews;
    }

    /**
     * 
     * Prikaz stranice proizvoda
     * 
     * @return void   
     */
    public function product($id) {
        $productM = new ProductM();
        $product = $productM->find($id);

        $genres = implode(' ', (new GenreM())->getGenres($id));

        $product_base = $product->base_game != null ? $productM->find($product->base_game) : null;

        $product_dlc = $productM->asArray()->where('base_game', $product->id)->findAll();

        $user = $this->session->get('user');

        $product_review = (new OwnershipM())->where('id_product', $id)->where('id_user', $user->id)->first();

        if ($user->review_ban == 1 || !(isset($product_review)))
            $product_review = NULL;

        $topReviews = $this->getTopReviews($id);

        $this->show('product', ['product' => $product, 'genres' => $genres, 'product_base' => $product_base, 'product_dlc' => $product_dlc, 'product_review' => $product_review, 'reviews' => $topReviews]);
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

        return redirect()->to(site_url("User/Product/{$product->id}"));
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
    public function friendRequests(){
        $user = $this->session->get('user');
        $relationshipM= new RelationshipM();
        
        $requesters= $relationshipM->getIncoming($user);
        $requestedTo= $relationshipM->getSent($user);

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

        return redirect()->to(site_url("User/Product/{$product->id}"));
    }

    /**
     * 
     * Procesiranje lajkovanja recenzija
     * 
     * @return void   
     */
    public function LikeSubmit($id, $posterUsername) {
        $poster = (new UserM())->where('username', $posterUsername)->first();
        $user = $this->session->get('user');

        if ($poster->id == $user->id) return redirect()->to(site_url("User/Product/{$id}"));

        $review_voteM = new ReviewVoteM();

        $vote = $review_voteM->where("id_user", $user->id)->where("id_poster", $poster->id)->where("id_product", $id)->first();

        if ($vote) {
            if ($vote->like == 0)
                $review_voteM->where('id_product', $id)->where('id_user', $user->id)->where("id_poster", $poster->id)->set(['like' => 1])->update();
            else
                $review_voteM->where('id_product', $id)->where('id_user', $user->id)->where("id_poster", $poster->id)->delete();
        } else {
            $review_voteM->insert([
                'id_user' => $user->id,
                'id_poster' => $poster->id,
                'id_product' => $id,
                'like' => 1
            ]);
        }

        return redirect()->to(site_url("User/Product/{$id}"));
    }

    /**
     * 
     * Procesiranje dislajkovanja recenzija
     * 
     * @return void   
     */
    public function DislikeSubmit($id, $posterUsername) {
        $poster = (new UserM())->where('username', $posterUsername)->first();
        $user = $this->session->get('user');

        if ($poster->id == $user->id) return redirect()->to(site_url("User/Product/{$id}"));

        $review_voteM = new ReviewVoteM();
        $vote = $review_voteM->where("id_user", $user->id)->where("id_poster", $poster->id)->where("id_product", $id)->first();

        //user 1 poster 2 prod 1
        if ($vote) {
            if ($vote->like == 1)
                $review_voteM->where('id_product', $id)->where('id_user', $user->id)->where("id_poster", $poster->id)->set(['like' => 0])->update();
            else
                $review_voteM->where('id_product', $id)->where('id_user', $user->id)->where("id_poster", $poster->id)->delete();
        } else {
            $review_voteM->insert([
                'id_user' => $user->id,
                'id_poster' => $poster->id,
                'id_product' => $id,
                'like' => 0
            ]);
        }

        return redirect()->to(site_url("User/Product/{$id}"));
    }


    /**
     * prikaži bundle sa id-jem $id
     *
     * @param  integer $id
     * @return void
     */
    public function bundle($id) {
        $bundle = (new BundleM())->find($id);

        if (!isset($bundle))
            return $this->showError('doesntExist', ['errors' => "Bundle with id=[$id] doesn't exist"]);

        $result = [];
        foreach ((new BundledProductsM())->findBundledProducts($id) as $idproduct) {
            array_push($result, (new ProductM())->find($idproduct)->name);
        }

        return $this->show('bundle', ['bundle' => $bundle,
                                      'bundledProducts' => $result]);
    }

}
