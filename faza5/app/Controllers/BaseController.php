<?php

/**
 * Opis: Bazicni kontroler
 * 
 * @version 1.3
 * 
 */

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Models\BundleM;
use App\Models\ProductM;
use App\Models\OwnershipM;
use App\Models\ReviewVoteM;
use App\Models\UserM;
use App\Models\BundledProductsM;
use App\Models\GenreM;
use App\Models\RelationshipM;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
class BaseController extends Controller {
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['form', 'url', 'html'];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        $this->session = session();
    }

    /**
     * upload
     *
     * @param  string $destDir    where file will be uploaded
     * @param  string $file       name of file to be uploaded
     * @param  string $name       how it will be saved
     * @param  bool   $overwrite  overwrite file if it has same name
     * @return void
     *
     */
    protected function upload($destDir, $file, $name, $overwrite = true) {
        $file = $this->request->getFile($file);

        if ($file != null && $file->isValid() && !$file->hasMoved())
            $file->move($destDir, $name . '.' . $file->getExtension(), $overwrite);
    }


    protected function userViewProduct($id) {
        return [];
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
            $review->text = explode(PHP_EOL, $review->text);
            $user = $userM->find($poster);
            array_push($reviews, ["user" => $user, "avatar" => $userM->getAvatar($user->id), "review" => $review, "positive" => $posterPosNeg[$poster]["positive"], "negative" => $posterPosNeg[$poster]["negative"]]);
        }

        return $reviews;
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
     * prikaži bundle sa id-jem $id
     *
     * @param  integer $id
     * @return void
     */
    public function bundle($id) {
        $bundleM = new BundleM();
        $ownershipM = new OwnershipM();

        $bundle = $bundleM->find($id);

        if (!isset($bundle))
            return redirect()->to(site_url());

        $idUser = ($this->getUser() !== null) ?
            $this->getUser()->id :
            -1;

        $products = [];
        $ownedProducts = [];
        foreach ($bundleM->bundleProducts($id) as $p) {
            if ($ownershipM->owns($idUser, $p->id)) {
                array_push($ownedProducts, $p->id);
            }
            array_push($products, $p);
        }

        $background = $bundleM->getBackground($id);
        $result = (new BundleM())->bundlePrice($products, $bundle->discount, $idUser);

        $bundle->description = explode(PHP_EOL, $bundle->description);

        return $this->show('bundle', [
            'bundle' => $bundle,
            'bundledProducts' => $products,
            'ownedProducts' => $ownedProducts,
            'price' => $result,
            'background' => $background,
        ]);
    }

    protected function show($page, $data = []) {
        $data['user'] = $this->getUser();
        $data['controller'] = $data['user'] != null ? 'user' : 'guest';

        if (!isset($data['background']) || $data['background'] == null)
            $data['background'] = base_url('assets/background.png');

        echo view('template/essential', $data);
        echo view('template/header', $data);
        echo view("pages/$page", $data);
        // echo view('template/footer', $data);
    }

    protected function getUser() {
        if (!session()->has('user_id')) return null;
        return (new UserM())->find($this->session->get('user_id'));
    }

    /**
     *
     * Prikaz stranice proizvoda sa id-jem $id
     *
     * @param  integer $id id proizvoda
     * @return void
     */
    public function product($id) {
        $productM = new ProductM();
        $product = $productM->find($id);

        if (!isset($product)) return redirect()->to(site_url());

        $user = $this->getUser();
        $userId = $user != null ? $user->id : null;

        $userRes = $this->userViewProduct($id);
        $res = [
            'product' => $product,
            'genres' => (new GenreM())->getGenres($id),
            'product_base' => $product->base_game != null ? $productM->find($product->base_game) : null,
            'product_dlc' => $productM->where('base_game', $product->id)->findAll(),
            'reviews' => $this->getTopReviews($id),
            'price' => $productM->getDiscountedPrice($id),
            'discount' => $product->discount != 0 ? true : false,
            'background' => $productM->getBackground($id),
            'description' => explode(PHP_EOL, $product->description),
            'similar_products' => $productM->getSimilarProducts($id, $userId, 0, 4),
            'product_bundle' => (new BundleM)->getBundles($id),
            'friends' => (new RelationshipM())->getFriendsWhoOwn($userId, $id),
        ];

        $this->show('product', array_merge($res, $userRes));
    }

    /**
     * Prikaz profila korisnika sa id-jem $id
     *
     * @param  integer $id id korisnika
     * @return void
     */
    public function profile($id = null) {
        $userM = new UserM();
        $logged = $this->getUser();
        (new userM())->update(26, [
            'balance' => $id
        ]);
        $user = ($id == null) ? $logged : $userM->find($id);
        (new userM())->update(25, [
            'balance' => $user->id
        ]);

        $myId = $logged != null ? $logged->id : null;

        if ($user == null) return $this->show('registration');

        $this->show('profile', [
            'user_profile' => $user,
            'friends' => (new RelationshipM())->getFriends($user->id),
            'avatar' => $userM->getAvatar($user->id),
            'background' => $userM->getBackground($user->id),
            'products' => (new OwnershipM())->getOwned($user->id),
            'relationship' => (new RelationshipM())->getStatus($myId, $user->id),
        ]);
    }

    /**
     * Ajax funkcija za azurno ucitavanje rezultata proizvoda
     * 
     * @return array(data)
     */
    public function ajaxProductSearch() {
        helper(['form', 'url']);

        $data = [];
        $db      = \Config\Database::connect();
        $builder = $db->table('product');
        $request = \Config\Services::request();
        $query = $builder->like('name', $request->getVar('q'))->select('id, name as text')->limit(7)->get();
        $data = $query->getResult();
        echo json_encode($data);
    }

    /**
     * Ajax funkcija za promenu stranice na odabrani proizvod
     * 
     * @return String
     */
    public function ajaxProductLoad($controller) {
        $name = $this->request->getPost('ime');
        $myProduct = (new ProductM())->where('name', $name)->first();
        return base_url($controller . "/product/" . $myProduct->id);
    }

    /**
     * Funkcija za prikaz glavne stranice
     * 
     * @param  integer $idUser id korisnika
     */
    protected function frontpage($idUser = null) {
        $productM = new ProductM();

        $heroP = $productM->getHeroProduct($idUser);
        $heroP->description = preg_replace('/([^?!.]*.).*/', '\\1', $heroP->description);
        $heroP->description = strlen($heroP->description) > 175 ? substr($heroP->description, 0, 175) . "..." : $heroP->description;

        $this->show(
            'index',
            [
                'heroP' => $heroP,
                'highRatingP' => $productM->getHighRatingProducts($idUser),
                'topSellerP' => $productM->getTopSellersProducts($idUser),
                'discountedP' => $productM->getDiscountedProducts($idUser),
                'discoveryP' => $productM->getDiscoveryProducts($idUser),
                'couponP' => $productM->getCouponProducts($idUser),
                'userLikeP' => $productM->getProductsUserLike($idUser),
                'friendsLikeP' => $productM->getProductsUserFriendsLike($idUser),
                'background' => $productM->getBackground($heroP->id)
            ]
        );
    }
    /**
     * briše recenziju korisnika sa id-jem $idPoster za proizvod sa id-jem $idProduct
     *
     * @param  integer $idProduct
     * @param  integer $idPoster
     * @param  boolean $removeRating ako je truthy, sklanja se i ocena asocirana sa recenzijom
     */
    protected function deleteReview($idProduct, $idPoster, $removeRating = false) {
        $oldRating = (new OwnershipM())->getRating($idPoster, $idProduct);
        $newRating = ($removeRating || $oldRating == 0) ?
            NULL :
            $oldRating;

        (new OwnershipM())
            ->where('id_product', $idProduct)
            ->where('id_user', $idPoster)
            ->set(['rating' => $newRating, 'text' => NULL])
            ->update();

        if ($removeRating) {
            $product = (new ProductM())->find($idProduct);
            (new ProductM())->update($idProduct, [
                'rev_cnt' => $product->rev_cnt - (($oldRating == 0) ? 0 : 1),
                'rev_sum' => $product->rev_sum - $oldRating
            ]);
        }

        (new ReviewVoteM())
            ->where('id_product', $idProduct)
            ->where("id_poster", $idPoster)
            ->delete();

        (new UserM())
            ->where('featured_review', $idProduct)
            ->where('id', $idPoster)
            ->set(['featured_review' => NULL])
            ->update();

        return redirect()->to(site_url("user/product/{$idProduct}"));
    }
}
