<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminFilter implements FilterInterface {

    public function before(RequestInterface $request, $arguments = null) {
        $session = session();

        if (!$session->has('user'))
            return redirect()->to(site_url('Guest'));

        if ($session->user->admin_rights != 1) {
            return redirect()->to(site_url('User'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $reponse, $arguments = null) {
    }
}
