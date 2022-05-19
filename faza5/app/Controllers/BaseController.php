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
    protected function upload($destDir, $file, $name, $overwrite=true) {
        $file = $this->request->getFile($file);

        if ($file != null && $file->isValid() && !$file->hasMoved())
            $file->move($destDir, $name . '.' . $file->getExtension(), $overwrite);
    }

    protected function show($page, $data = []) {}

    /**
     *
     * Prikaz stranice proizvoda
     *
     * @return void
     */
    public function product($id) {} // TODO

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
     * determineBundlePriceAndDiscount determines price and discount of a bundle
     * for current user
     *
     * @param  mixed $products model fetched directly from database
     * @param  mixed $discount discount of bundle
     * @return array array is of 'price'=>price and 'discount'=>discount with price
     * denoting full price of bundle and discount denoting discount of current user
     */
    protected function determineBundlePriceAndDiscount($products, $discount) {
        $price = 0.0;
        $owned = 0;
        $user = $this->session->get('user');
        $cnt = count($products);

        foreach ($products as $product) {
            $query = (new OwnershipM())
                    ->where('id_product', $product->id)
                    ->where('id_user', $user->id)
                    ->first();

            if (isset($query)) {
                $owned++;
            } else {
                $price += $product->price;
            }
        }

        if ($cnt == $owned)
            return ['price' => 0, 'discount' => 0];
        if (($cnt - $owned) == 1)
            return ['price' => $price, 'discount' => 0];

        while ($owned > 0) {
            $discount -= ceil($discount / ($cnt - 1));
            $owned--;
        }

        return ['price' => $price,
                'discount' => $discount];
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

        $products = [];
        foreach ((new BundledProductsM())->findBundledProducts($id) as $idproduct) {
            array_push($products, (new ProductM())->find($idproduct));
        }

        $result = $this->determineBundlePriceAndDiscount($products, $bundle->discount);

        $result['final'] = $result['price'] * (100 - $result['discount']) / 100;

        return $this->show('bundle', ['bundle' => $bundle,
                                      'bundledProducts' => $products,
                                      'price' => $result]);
    }
}
