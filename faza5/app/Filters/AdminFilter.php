<?php

/**
 * Opis: Filter za /admin/ stranice
 * 
 * @version 1.0
 * 
 */

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

use App\Models\UserM;

class AdminFilter implements FilterInterface {

    public function before(RequestInterface $request, $arguments = null) {
        $session = session();

        if (!$session->has('user_id')) return redirect()->to(site_url('guest/registration'));

        if ((new UserM())->find($session->get('user_id'))->admin_rights != 1) return redirect()->to(site_url('user'));
    }

    public function after(RequestInterface $request, ResponseInterface $reponse, $arguments = null) {
    }
}
