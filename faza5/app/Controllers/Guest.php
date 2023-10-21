<?php

/**
 * Opis: Kontroler za gosta
 * 
 * @version 1.3
 * 
 */

namespace App\Controllers;

use App\Models\UserM;

class Guest extends BaseController {

    /**
     *
     * Glavna stranica
     * 
     * @return void
     */
    public function index() {
        $this->frontpage();
    }

    /**
     *
     * Stranica za prijavljivanje
     * 
     * @return void
     */
    public function login($message = null) {
        $this->show('login', ['message' => $message]);
    }

    /**
     *
     * Funkcija za procesiranje prijavljivanja
     * 
     * @return void
     */
    public function loginSubmit() {
        if (!$this->validate(['username' => 'required', 'password' => 'required']))
            return $this->show('login', ['errors' => $this->validator->getErrors()]);

        $user = (new UserM())->where('username', $this->request->getPost('username'))->first();

        if ($user == null || $user->password != $this->request->getPost('password'))
            return $this->login('Wrong username or password!');

        $this->session->set('user_id', $user->id);

        return redirect()->to(site_url('user'));
    }

    /**
     *
     * Stranica za registraciju
     * 
     * @return void
     */
    public function registration() {
        $this->show('registration');
    }

    /**
     *
     * Funkcija za procesiranje registracije
     * 
     * @return void
     */
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

    /**
     *
     * Pomocna funkcija za prikaz proizvoda
     *
     * @param  integer $id id proizvoda
     * @return void
     */
    protected function userViewProduct($id) {
        return [];
    }
}
