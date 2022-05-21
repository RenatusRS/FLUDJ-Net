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
use App\Models\GenreM;
use App\Models\ProductM;

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

    protected function upload($location, $field, $name) {
        $file = $this->request->getFile($field);

        if ($file != null && $file->isValid() && !$file->hasMoved())
            $file->move($location, $name . '.' . $file->getExtension(), true);
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

    public function product($id) {
        $productM = new ProductM();
        $product = $productM->find($id);

        $genres = implode(' ', (new GenreM())->asArray()->where('id_product', $id)->findAll());

        $product_base = $product->base_game != null ? $productM->find($product->base_game) : null;

        $product_dlc = $productM->asArray()->where('base_game', $product->id)->findAll();

        $this->show('product', ['product' => $product, 'genres' => $genres, 'product_base' => $product_base, 'product_dlc' => $product_dlc]);
    }
}
