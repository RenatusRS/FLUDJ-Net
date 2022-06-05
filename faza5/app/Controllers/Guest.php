<?php

/**
 * @author
 *  Djordje Stanojevic 2019/0288
 *  Uros Loncar 2019/0691
 *  Luka Cvijan 2019/0154
 *  Fedja Mladenovic 2019/0613
 * 
 * Opis: Kontroler za gosta
 * 
 * @version 1.3
 * 
 */

namespace App\Controllers;

use App\Models\GenreM;
use App\Models\ProductM;
use App\Models\UserM;
use App\Models\OwnershipM;
use App\Models\RelationshipM;

class Guest extends BaseController {
    public function index() {
        $this->frontpage();
    }

    public function login($message = null) {
        $this->show('login', ['message' => $message]);
    }

    public function loginSubmit() {
        if (!$this->validate(['username' => 'required', 'password' => 'required']))
            return $this->show('login', ['errors' => $this->validator->getErrors()]);

        $user = (new UserM())->where('username', $this->request->getVar('username'))->first();

        if ($user == null || $user->password != $this->request->getVar('password'))
            return $this->login('Wrong username or password!');

        $this->session->set('user_id', $user->id);

        return redirect()->to(site_url('user'));
    }

    public function registration() {
        $this->show('registration');
    }

    public function registrationSubmit() {
        if (!$this->validate(['username' => 'required', 'password' => 'required']))
            return $this->show('registration', ['errors' => $this->validator->getErrors()]);

        $userM = new UserM();
        $data = [
            'username' => $this->request->getVar('username'),
            'password' => $this->request->getVar('password'),
            'nickname' => $this->request->getVar('username'),
            'real_name' => '',
            'description' => 'User has not set a description.'
        ];

        if ($userM->save($data) === false) {
            return $this->show('registration', ['errors' => $userM->errors()]);
        }

        $user = $userM->where('username', $this->request->getVar('username'))->first();
        $this->session->set('user_id', $user->id);
        return redirect()->to(site_url("user/profile/"));
    }

    protected function userViewProduct($id) {
        return [];
    }
}
