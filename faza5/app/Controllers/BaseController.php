<?php
/*
Autori:
	Uros Loncar 2019/0691
    Djordje Stanojevic 2019/0288

Opis: Bazicni kontroler
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
            $user = $userM->find($poster);
            $reviews[$user->username] = ["poster" => $poster, "review" => $review, "positive" => $posterPosNeg[$poster]["positive"], "negative" => $posterPosNeg[$poster]["negative"]];
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
     * određuje početnu cenu, sniženje i finalnu cenu kolekcije za trenutnog korisnika
     *
     * @param  array $products niz modela dohvaćenih iz baze sa ProductM->find($id)
     * @param  mixed $discount sniženje kolekcije
     * @return array 'price' => puna cena, 'discount' => sniženje,
     * 'final' => finalna cena kada se primeni sniženje
     *
     */
    protected function bundlePrice($products, $discount) {
        $price = 0.0;
        $owned = 0;
        $user = $this->session->get('user');
        $cnt = count($products);

        foreach ($products as $product) {
            $owns = (new OwnershipM())
                ->owns($user->id, $product->id);

            if ($owns === true) {
                $owned++;
            } else {
                $price += $product->price;
            }
        }

        if ($cnt == $owned) {
            $price = $discount = 0;
        } else if (($cnt - $owned) == 1) {
            $discount = 0;
        } else {
            while ($owned > 0) {
                $discount -= ceil($discount / ($cnt - 1));
                $owned--;
            }
        }

        $final = ($price == 0) ?
            0 :
            $price - ($price * $discount) / 100;

        return [
            'price'    => $price,
            'discount' => $discount,
            'final'    => $final
        ];
    }

    protected function bundleProducts($bundleId) {
        $iter = (new BundledProductsM())
            ->where('id_bundle', $bundleId)
            ->findAll();

        foreach ($iter as $bundle) {
            yield ((new ProductM())->find($bundle->id_product));
        }
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
            return redirect()->to(site_url());

        $products = iterator_to_array($this->bundleProducts($id));

        $result = $this->bundlePrice($products, $bundle->discount);

        return $this->show('bundle', [
            'bundle' => $bundle,
            'bundledProducts' => $products,
            'price' => $result
        ]);
    }

    protected function show($page, $data = []) {
        $data['user'] = $this->session->get('user');
        if (!isset($data['background']) || $data['background'] == null)
            $data['background'] = base_url('assets/background.png');

        echo view('template/essential', $data);
        echo view('template/header', $data);
        echo view("pages/$page", $data);
        echo view('template/footer', $data);
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

        if (!isset($product))
            return redirect()->to(site_url());

        $genres = implode(' ', (new GenreM())->getGenres($id));

        $product_base = $product->base_game != null ? $productM->find($product->base_game) : null;

        $product_dlc = $productM->asArray()->where('base_game', $product->id)->findAll();

        $topReviews = $this->getTopReviews($id);

        $price = $productM->getDiscountedPrice($id);

        $discount = $product->discount != 0 ? true : false;

        $userRes = $this->userViewProduct($id);
        $res = [
            'product' => $product,
            'genres' => $genres,
            'product_base' => $product_base,
            'product_dlc' => $product_dlc,
            'reviews' => $topReviews,
            'price' => $price,
            'discount' => $discount
        ];

        $this->show('product', array_merge($res, $userRes));
    }

    protected function frontpage($idUser = null) {
        $productM = new ProductM();

        $heroP = $productM->getHeroProduct($idUser);
        $heroP->description = explode(".", $heroP->description, 2)[0] . ".";

        $highRatingP =  $productM->getHighRatingProducts($idUser);
        $topSellerP =   $productM->getTopSellersProducts($idUser);
        $discountedP =  $productM->getDiscountedProducts($idUser);
        $discoveryP =   $productM->getDiscoveryProducts($idUser);
        $couponP =      $productM->getCouponProducts($idUser);
        $userLikeP =    $productM->getProductsUserLike($idUser);
        $friendsLikeP = $productM->getProductsUserFriendsLike($idUser);

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
}
