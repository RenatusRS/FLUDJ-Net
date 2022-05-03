<?php

namespace App\Controllers;

use App\Models\UserM;

class Guest extends BaseController {

    protected function show($page, $data = []) {
        $data['controller'] = 'Guest';
        echo view('template/header_guest.php');
        echo view("pages/$page", $data);
        echo view('template/footer.php');
    }

    public function index() {
        $this->show('index');
    }

    public function login($message = null) {
        $this->show('login', ['message' => $message]);
    }

    public function loginSubmit() {
        if (!$this->validate(['username' => 'required', 'password' => 'required']))
            return $this->show('login', ['errors' => $this->validator->getErrors()]);

        $userModel = new UserM();
        $user = $userModel->where('username', $this->request->getVar('username'))->first();

        if ($user == null || $user->password != $this->request->getVar('password'))
            return $this->login('Wrong username or password!');

        $this->session->set('user', $user);

        return redirect()->to(site_url('User'));
    }

    public function registration() {
        $this->show('registration');
    }

    public function registrationSubmit() {
        if (!$this->validate(['username' => 'required', 'password' => 'required']))
            return $this->show('registration', ['errors' => $this->validator->getErrors()]);

        $userM = new UserM();
        $userM->save([
            'username' => $this->request->getVar('username'),
            'password' => $this->request->getVar('password'),
            'admin_rights' => false,
            'balance' => 0,
            'review_ban' => false,
            'nickname' => $this->request->getVar('username')
        ]);

        $user = $userM->where('username', $this->request->getVar('username'))->first();
        $this->session->set('user', $user);
        return redirect()->to(site_url("User/Profile/"));
    }
}
