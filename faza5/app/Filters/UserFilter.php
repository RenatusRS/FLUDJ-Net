<?php

/**
 * @author
 *  Uros Loncar 2019/0691
 * 
 * Opis: Filter za /user/ stranice
 * 
 * @version 1.0
 * 
 */

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class UserFilter implements FilterInterface {

    public function before(RequestInterface $request, $arguments = null) {
        $session = session();

        if (!$session->has('user_id')) return redirect()->to(site_url('guest/registration'));
    }

    public function after(RequestInterface $request, ResponseInterface $reponse, $arguments = null) {
    }
}
