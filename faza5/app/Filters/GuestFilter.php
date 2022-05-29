<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class GuestFilter implements FilterInterface {

    public function before(RequestInterface $request, $arguments = null) {
        $session = session();

        if ($session->has('user_id')) return redirect()->to(site_url('user'));
    }

    public function after(RequestInterface $request, ResponseInterface $reponse, $arguments = null) {
    }
}
