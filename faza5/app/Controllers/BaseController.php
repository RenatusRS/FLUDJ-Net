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
}
