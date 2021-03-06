<?php

/**
 * @author
 * 	Uros Loncar 2019/0691
 *  Luka Cvijan 2019/01548
 *  Fedja Mladenovic 2019/0613
 * 
 * Opis: Bazicni kontroler
 * 
 * @version 1.3
 * 
 */

namespace App\Controllers;

use App\Models\GenreM;
use App\Models\ProductM;
use App\Models\BundleM;
use App\Models\UserM;
use App\Models\OwnershipM;
use App\Models\ReviewVoteM;
use App\Models\BundledProductsM;
use App\Models\CouponM;
use App\Models\RelationshipM;

class Admin extends BaseController {

    /**
     *
     * Stranica za upravljanje proizvodom (specificnim ako je zadat id ili generalno - za dodavanje novog)
     *
     * @param  integer $id id proizvoda
     * @return void
     */
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


    /**
     *
     * Procesiranje podataka sa stranice za upravljanje proizvodom 
     *
     * 
     * @return void
     */
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

        // a??uriraj bazu. ako ime ve?? postoji u bazi, izbaci gre??ku; ako izmenjujemo proizvod a??uriraj bazu; ako dodajemo proizvod sa??uvaj ga.
        if ($productM->productNameExists($data['name'], $id)) {
            return $this->show('manageProduct', ['errors' => ["Name for product '{$data['name']}' already exists."], 'title' => 'Manage Product']);
        } else if ($id != -1) {
            $productM->update($id, $data);
        } else {
            $productM->save($data);
        }

        if ($id != -1) // otklanjamo stare ??anrove iz baze jer ??e biti zamenjeni novim
            $genreM->where('id_product', $id)->delete();

        // napravi niz ??anrova
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

    /**
     *
     * Prikaz stranice za dodavanje kolekcije
     *
     *
     * @return void
     */
    public function addBundle() {
        $this->manageBundle();
    }

    /**
     *
     * Prikaz stranice za upravljanje kolekcijom (specificnom ako je dat id ili generalno - za dodavanje nove)
     *
     * @param  integer $id id kolekcije
     * @return void
     */
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

        // TODO da se inputi forme zadr??avaju nakon neuspe??ne validacije
        // TODO da se banner/background zadr??avaju tokom editovanja bundle-a
        $notValid = (!$this->validate([
            'name' =>        'required',
            'discount' =>    "required|integer|less_than_equal_to[$max_disc]|greater_than_equal_to[$min_disc]",
            'description' => "required|min_length[$min_descr]|max_length[$max_descr]",

            'banner' =>      'uploaded[banner]|ext_in[banner,jpg]|is_image[banner]'
            // TODO za veli??inu isto ograni??enje za slike
            // TODO dinami??ka provera fajla koji mo??e da bude uploadovan pod bilo kojim imenom
        ]));

        return !$notValid;
    }

    /**
     *
     * Procesiranje upravljanja kolekcijom
     *
     * 
     * @return void
     */
    public function manageBundleSubmit() {
        if (!$this->validateBundle())
            return $this->show('manageBundle', ['errors' => $this->validator->getErrors()]);

        // ----------------- ubacivanje u bazu ----------------

        $id = $this->request->getVar('id');
        $data = [ // niz podataka koji se ??uvaju kao red u bazi
            'name' =>           trim($this->request->getVar('name')),
            'discount' =>       trim($this->request->getVar('discount')),
            'description' =>    trim($this->request->getVar('description')),
            'id' => ($id != -1) ? $id : ''
        ];

        $bundleM = new BundleM();

        if ($bundleM->bundleNameExists($data['name'], $id)) {
            return $this->show('manageBundle', ['errors' => ["Name for bundle '{$data['name']}' already exists."], 'title' => "Manage Bundle"]);
        } else if ($id != -1) {
            $bundleM->update($id, $data);
        } else {
            $bundleM->save($data);
        }

        if ($id == -1)
            $id = $bundleM->getInsertID();

        $targetDir = 'uploads/bundle/' . $id;

        $this->upload($targetDir, 'banner', 'banner');


        return redirect()->to(site_url("admin/managebundle/" . $id));
    }

    /**
     * funkcija admina za brisanje recenzije. Informacija o tome koja recenzija je u pitanju se dobija
     * iz requesta, dok se request pravi u Views/pages/product.php
     *
     * @return void
     */
    public function deleteReviewAjax() {
        $data = $this->request->getVar();

        $this->deleteReview($data['idProduct'], $data['idUser'], false);

        echo json_encode(array(
            "state" => 1,
        ));
    }

    /**
     * Prikaz stranice za dodavanje popusta za proizvod sa id-jem $id
     *
     * @param  integer $id id proizvoda
     * @return void
     */
    public function setDiscount($id) {
        $this->show('setDiscount', ["productId" => $id]);
    }

    /**
     * Procesiranje popusta za proizvod sa id-jem $id
     *
     * @param  integer $id id proizvoda
     * @return void
     */
    public function setDiscountSubmit($id) {

        if (!$this->validate(['discount' => 'required|greater_than_equal_to[5]|less_than_equal_to[90]|integer']))
            return $this->show('setDiscount', ['productId' => $id, 'errors' => $this->validator->getErrors()]);

        $expDate = date($this->request->getPost('expDate'));

        $future_date = ProductM::future_date($this->request->getPost('expDate'));

        if (!($future_date))
            return $this->show('setDiscount', ['productId' => $id, 'message' => "Discount must last at least one day."]);

        (new ProductM())->update($id, [
            'discount' => $this->request->getPost('discount'),
            'discount_expire' => $expDate
        ]);

        return redirect()->to(site_url("user/product/{$id}"));
    }

    /**
     * poziva se iz forme iz manageBundle.php.
     * svi proizvodi koji su ??tiklirani u sekciji za dodavanje bivaju dodati u kolekciju,
     * a svi koji su ??tiklirani u sekciji za otklanjanje se otklanjaju iz kolekcije
     *
     */
    public function updateBundleProducts() {
        $idBundle = $this->request->getVar('id');

        BundledProductsM::clearBundle($idBundle);

        $in =  $this->request->getVar('inBundle') ?? [];

        foreach ($in as $idProduct)
            BundledProductsM::addToBundle($idBundle, $idProduct);

        return redirect()->to(site_url("admin/manageBundle/" . $idBundle));
    }

    /**
     *
     * Brisanje proizvoda
     *
     * @param  integer $id proizvoda
     * @return void
     */
    public function deleteProduct($id) {
        (new UserM())
            ->where('featured_review', $id)
            ->set(['featured_review' => NULL])
            ->update();

        (new GenreM())->where('id_product', $id)->delete();

        (new ReviewVoteM())->where('id_product', $id)->delete();

        (new OwnershipM())->where('id_product', $id)->delete();

        (new CouponM())->where('id_product', $id)->delete();

        (new ProductM())->delete($id);

        return redirect()->to(base_url());
    }

    /**
     *
     * Brisanje korisnika
     *
     * @param  integer $id korisnika
     * @return void
     */
    public function deleteUser($id) {
        (new CouponM())->where('id_owner', $id)->delete();

        (new ReviewVoteM())->where('id_user', $id)->delete();

        (new OwnershipM())->where('id_user', $id)->delete();

        (new RelationShipM())->where('id_user1', $id)->OrWhere('id_user2', $id)->delete();

        (new UserM)->delete($id);

        return redirect()->to(base_url());
    }

    /**
     *
     * Brisanje kolekcije
     *
     * @param  integer $id kolekcije
     * @return void
     */
    public function deleteBundle($id) {
        (new BundledProductsM())->where('id_bundle', $id)->delete();
        (new BundleM)->delete($id);

        return redirect()->to(base_url());
    }

    /**
     *
     * Ajax funkcija za banovanje/unbanovanje korisnika
     *
     * @param  integer $id user
     * @return void
     */
    public function banajax() {
        $data = $this->request->getVar();
        $userM = new UserM();

        $user = $userM->find($data['user']);

        if ($user->review_ban == 0) $userM->banUser($user->id);
        else $userM->unbanUser($user->id);

        echo json_encode(array(
            "state" => $user->review_ban,
        ));
    }

    /**
     *
     * Ajax funkcija za promociju/demociju korisnika
     *
     * @param  integer $id user
     * @return void
     */
    public function promoteajax() {
        $data = $this->request->getVar();
        $userM = new UserM();

        $user = $userM->find($data['user']);

        if ($user->admin_rights == 0) $userM->promoteUser($user->id);
        else $userM->demoteUser($user->id);

        echo json_encode(array(
            "state" => !$user->admin_rights,
        ));
    }
}
