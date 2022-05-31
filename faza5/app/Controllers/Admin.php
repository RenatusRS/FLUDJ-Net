<?php

namespace App\Controllers;

use App\Models\GenreM;
use App\Models\ProductM;
use App\Models\BundleM;
use App\Models\UserM;
use App\Models\OwnershipM;
use App\Models\ReviewVoteM;
use App\Models\BundledProductsM;

class Admin extends BaseController {
    public function manageProduct($id = null) {
        $data = [];
        $product = $genres = $background = null;

        if ($id != null) {
            $productM = new ProductM();
            $product = $productM->find($id);

            $background = $productM->getBackground($id);

            if (!is_object($product)) {
                $data['errors'] = ['product' => "Product with ID [$id] doesn't exist"];
            } else {
                $genres = (new GenreM())->getGenres($id);
            }
        }

        $data['product'] = $product;
        $data['genres']  = $genres;
        $data['background'] = $background;

        $this->show('manageProduct', $data);
    }

    private function validateProduct($uploadedBackground) {
        $notValid = (!$this->validate([
            'name' =>   'required',
            'genres' => 'required',
            'price' =>  'required|numeric|greater_than_equal_to[1]',

            'developer' =>    'required',
            'publisher' =>    'required',
            'release_date' => 'required|valid_date[Y-m-d]',

            'os_min' =>  'required',
            'cpu_min' => 'required',
            'gpu_min' => 'required',
            'ram_min' => 'required',
            'mem_min' => 'required',
            'os_rec' =>  'required',
            'cpu_rec' => 'required',
            'gpu_rec' => 'required',
            'ram_rec' => 'required',
            'mem_rec' => 'required',

            'banner' =>  'uploaded[banner]|ext_in[banner,jpg]|is_image[banner]',
            'capsule' => 'uploaded[capsule]|ext_in[capsule,jpg]|is_image[capsule]',
            'ss1' =>     'uploaded[ss1]|ext_in[ss1,jpg]|is_image[ss1]',
            'ss2' =>     'uploaded[ss2]|ext_in[ss2,jpg]|is_image[ss2]',
            'ss3' =>     'uploaded[ss3]|ext_in[ss3,jpg]|is_image[ss3]',
        ]) ||
            ($uploadedBackground && !$this->validate([
                'background' => 'uploaded[background]|ext_in[background,png]|is_image[background]'
            ])));

        return !$notValid;
    }

    public function manageProductSubmit() {
        $uploaded = (is_uploaded_file($_FILES['background']['tmp_name']));
        if (!$this->validateProduct($uploaded))
            return $this->show('manageProduct', ['errors' => $this->validator->getErrors()]);


        $id = $this->request->getVar('id');
        $isEditing = ($id != -1);
        $data = [
            'id' => ($id != -1) ? $id : '',
            'name' =>         $this->request->getVar('name'),
            'price' =>        $this->request->getVar('price'),
            'developer' =>    $this->request->getVar('developer'),
            'publisher' =>    $this->request->getVar('publisher'),
            'release_date' => $this->request->getVar('release_date'),
            'os_min' =>       $this->request->getVar('os_min'),
            'cpu_min' =>      $this->request->getVar('cpu_min'),
            'gpu_min' =>      $this->request->getVar('gpu_min'),
            'ram_min' =>      $this->request->getVar('ram_min'),
            'mem_min' =>      $this->request->getVar('mem_min'),
            'os_rec' =>       $this->request->getVar('os_rec'),
            'cpu_rec' =>      $this->request->getVar('cpu_rec'),
            'gpu_rec' =>      $this->request->getVar('gpu_rec'),
            'ram_rec' =>      $this->request->getVar('ram_rec'),
            'mem_rec' =>      $this->request->getVar('mem_rec'),
            'description' =>  $this->request->getVar('description'),
        ];

        $baseGame = $this->request->getVar('base_game');

        if ($baseGame != '') $data['base_game'] = $baseGame;

        $genreM = new GenreM();
        $productM = new ProductM();

        // ažuriraj bazu
        if ($productM->save($data) === false) {
            return $this->show('manageProduct', ['errors' => $productM->errors()]);
        }

        if ($id != -1) // otklanjamo stare žanrove iz baze jer će biti zamenjeni novim
            $genreM->where('id_product', $id)->delete();

        // napravi niz žanrova
        $genres = explode(' ', $this->request->getVar('genres'));

        if ($id == -1)
            $id = $productM->getInsertID();

        foreach ($genres as $genre) {
            $genreM->insertComposite($id, $genre);
        }

        $targetDir = "uploads/product/$id";

        //if ($isEditing && file_exists($targetDir . "/background.png"))
        //    unlink($targetDir . "/background.png");

        $this->upload($targetDir, 'banner', 'banner');
        $this->upload($targetDir, 'capsule', 'capsule');
        $this->upload($targetDir, 'ss1', 'ss1');
        $this->upload($targetDir, 'ss2', 'ss2');
        $this->upload($targetDir, 'ss3', 'ss3');
        $this->upload($targetDir, 'video', 'video');
        if ($uploaded)
            $this->upload($targetDir, 'background', 'background');

        return redirect()->to(site_url("user/product/" . $id));
    }

    public function addBundle() {
        $this->manageBundle();
    }

    public function manageBundle($id = null) {
        $data = [];
        $bundle = null;
        $inBundle = $notInBundle = [];

        if ($id != null) {
            $bundle = (new BundleM())->find($id);

            if (!is_object($bundle)) {
                $data['errors'] = ['bundle' => "bundle doesn't exist, redirected to adding new bundle"];
            }

            $inBundle    = iterator_to_array((new BundledProductsM())->productsInBundle($id));
            $notInBundle = iterator_to_array((new BundledProductsM())->productsNotInBundle($id));
        }

        $data['bundle'] = $bundle;
        $data['inBundle'] = $inBundle;
        $data['notInBundle'] = $notInBundle;

        $this->show('manageBundle', $data);
    }

    private function validateBundle() {
        $max_disc = MAX_BUNDLE_DISCOUNT;
        $min_disc = MIN_BUNDLE_DISCOUNT;
        $max_descr = MAX_DESCRIPTION_SIZE;
        $min_descr = MIN_DESCRIPTION_SIZE;

        // TODO da se inputi forme zadržavaju nakon neuspešne validacije
        // TODO da se banner/background zadržavaju tokom editovanja bundle-a
        $notValid = (!$this->validate([
            'name' =>        'required',
            'discount' =>    "required|integer|less_than_equal_to[$max_disc]|greater_than_equal_to[$min_disc]",
            'description' => "required|min_length[$min_descr]|max_length[$max_descr]",

            'banner' =>      'uploaded[banner]|ext_in[banner,jpg]|is_image[banner]'
            // TODO za veličinu isto ograničenje za slike
            // TODO dinamička provera fajla koji može da bude uploadovan pod bilo kojim imenom
        ]));

        return !$notValid;
    }

    public function manageBundleSubmit() {
        if (!$this->validateBundle())
            return $this->show('manageBundle', ['errors' => $this->validator->getErrors()]);

        // ----------------- ubacivanje u bazu ----------------

        $id = $this->request->getVar('id');
        $data = [ // niz podataka koji se čuvaju kao red u bazi
            'name' =>           trim($this->request->getVar('name')),
            'discount' =>       trim($this->request->getVar('discount')),
            'description' =>    trim($this->request->getVar('description')),
            'id' => ($id != -1) ? $id : ''
        ];

        $bundleM = new BundleM();
        if ($bundleM->save($data) === false) { // ako čuvanje u bazu nije prošlo validaciju
            // TODO
            return $this->show('manageBundle', ['errors' => $bundleM->errors()]);
        }

        if ($id == -1)
            $id = $bundleM->getInsertID();

        $targetDir = 'uploads/bundle/' . $id;

        $this->upload($targetDir, 'banner', 'banner');

        return redirect()->to(site_url("user/bundle/" . $id));
    }

    /** 
     * Procesiranje brisanja recenzije od strane administratora
     * 
     * @return void
     */
    public function DeleteReviewAdminSubmit($id, $posterUsername) {
        $poster = (new UserM())->where('username', $posterUsername)->first();

        return $this->deleteReview($id, $poster->id, false);
    }

    /** 
     * Prikaz stranice za dodavanje popusta
     * 
     * @return void
     */
    public function setDiscount($id) {
        $this->show('setDiscount', ["productId" => $id]);
    }

    /** 
     * Procesiranje popusta
     * 
     * @return void
     */
    public function setDiscountSubmit($id) {

        if (!$this->validate(['discount' => 'required|greater_than_equal_to[5]|less_than_equal_to[90]|integer']))
            return $this->show('setDiscount', ['productId' => $id, 'errors' => $this->validator->getErrors()]);

        $expDate = date($_POST['expDate']);

        $future_date = ProductM::future_date($_POST['expDate']);

        if (!($future_date))
            return $this->show('setDiscount', ['productId' => $id, 'message' => "Discount must last at least one day."]);

        (new ProductM())->update($id, [
            'discount' => $this->request->getVar('discount'),
            'discount_expire' => $expDate
        ]);

        return redirect()->to(site_url("user/product/{$id}"));
    }

    /**
     * poziva se iz forme iz manageBundle.php.
     * svi proizvodi koji su štiklirani u sekciji za dodavanje bivaju dodati u kolekciju,
     * a svi koji su štiklirani u sekciji za otklanjanje se otklanjaju iz kolekcije
     *
     */
    public function updateBundleProducts() {
        $idBundle = $this->request->getVar('id');

        $in =  $this->request->getVar('inBundle')    ?? [];
        $out = $this->request->getVar('notInBundle') ?? [];

        foreach ($in as $idProduct)
            BundledProductsM::removeFromBundle($idBundle, $idProduct);
        foreach ($out as $idProduct)
            BundledProductsM::addToBundle($idBundle, $idProduct);

        return redirect()->to(site_url("admin/manageBundle/" . $idBundle));
    }

    public function ban($idUser) { UserM::banUser($idUser); }
    public function unban($idUser) { UserM::unbanUser($idUser); }
    public function promote($idUser) { UserM::promoteUser($idUser); }
    public function demote($idUser) { UserM::demoteUser($idUser); }
}
