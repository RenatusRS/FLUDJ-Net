<?php

namespace App\Controllers;

use App\Models\UserM;
use Tests\Support\Models\UserModel;

class User extends BaseController {

    protected function show($page, $data = []) {
        $data['controller'] = 'User';
        $data['user'] = $this->session->get('user');
        echo view('template/header_user', $data);
        echo view("pages/$page", $data);
        echo view('template/footer');
    }

    public function index() {
        $this->show('index');
    }

    public function logout() {
        $this->session->destroy();
        return redirect()->to(site_url('/'));
    }

    public function profile($id = null) {
        if ($id == null)
            $user = $this->session->get('user');
        else {
            $userModel = new UserM();
            $user = $userModel->find($id);
        }

        $this->show('user', ['user_profile' => $user]);
    }
}
