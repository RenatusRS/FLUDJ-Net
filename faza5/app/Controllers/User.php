<?php
/*
Autori:
	Djordje Stanojevic 2019/0288
	Uros Loncar 2019/0691
	
Opis: Kontroler za korisnika

@version 1.3
*/

namespace App\Controllers;

use App\Models\GenreM;
use App\Models\ProductM;
use App\Models\UserM;

class User extends BaseController {

    /*
    Prikaz sadrzaja na stranici
    @return void
    */
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
    
    /*
    Odjavljivanje korisnika
    @return void
    */
    public function logout() {
        $this->session->destroy();
        return redirect()->to(site_url('/'));
    }

    /*
    Prikaz svog ili tudjeg profila
    @return void
    */
    public function profile($id = null) {
        $user = $id == null ? $this->session->get('user') : (new UserM())->find($id);
        if($id==null){
            $builder = \Config\Database::connect()->table('user');
            $builder = $builder->set('nickname', $this->request->getVar('nickname'))->set('real_name', $this->request->getVar('real_name'))
            ->set('country', $this->request->getVar('location'))->set('description', $this->request->getVar('description'))
            ->/*set('featured_review', $this->request->getVar('review'))*/where('id', $user->id)->update();
            $this->upload('public/uploads/user/','profile_pic', $user->id);
        }

        $this->show('user', ['user_profile' => $user]);
    }

    public function friends() {
        $this->show('friends.php');
    }
    
    public function addFunds() {
        $this->show('addFunds');
    }

    public function addFundsSubmit() {
        if (!$this->validate(['funds' => 'required|numeric|greater_than[0]']))
            return $this->show('addFunds', ['errors' => $this->validator->getErrors()]);

        $user = $this->session->get('user');

        $user->balance += $this->request->getVar('funds');

        $userM = new UserM();
        $userM->update($user->id, [
            'balance' => $user->balance
        ]);

        return redirect()->to(site_url("User/Profile/"));
    }

    public function product($id) {
        $productM = new ProductM();
        $product = $productM->find($id);

        $genres = implode(' ', (new GenreM())->asArray()->where('id_product', $id)->findAll());

        $product_base = $product->base_game != null ? $productM->find($product->base_game) : null;

        $product_dlc = $productM->asArray()->where('base_game', $product->id)->findAll();

        $this->show('product', ['product' => $product, 'genres' => $genres, 'product_base' => $product_base, 'product_dlc' => $product_dlc]);
    }

    /*
    Prikaz opcija za izmenu/unos podataka
    @return void
    */
    public function editProfile() {
        $this->show('editProfile.php');
    }

}
