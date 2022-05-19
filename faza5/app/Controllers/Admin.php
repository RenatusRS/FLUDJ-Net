<?php

namespace App\Controllers;

use App\Models\GenreM;
use App\Models\ProductM;
use App\Models\BundleM;
use App\Models\UserM;
use App\Models\OwnershipM;
use App\Models\ReviewVoteM;

class Admin extends BaseController {
    protected function show($page, $data = []) {
        $data['controller'] = 'User';
        $data['user'] = $this->session->get('user');
        echo view('template/header_user', $data);
        echo view("pages/$page", $data);
        echo view('template/footer');
    }

    public function manageProduct($id = null) {
        $data = [];
        $product = $genres = null;

        if ($id != null) {
            $product = (new ProductM())->find($id);

            if (!is_object($product)) {
                $data['errors'] = ['product' => "product with id [$id] doesn't exist"];
            } else {
                $genres = (new GenreM())->getGenres($id);
            }
        }

        $data['product'] = $product;
        $data['genres']  = $genres;

        $this->show('manageProduct', $data);
    }

    public function manageProductSubmit() {
        if (!$this->validate([
            'name' =>   'required',
            'genres' => 'required',
            'price' =>  'required|numeric|greater_than_equal_to[1]',

            'developer' =>    'required',
            'publisher' =>    'required',
            'release_date' => 'required|valid_date[d/m/Y]',

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

            'banner' =>     'uploaded[banner]|ext_in[banner,jpg]|is_image[banner]',
            'background' => 'uploaded[background]|ext_in[background,jpg]|is_image[background]',

            'ss1' => 'uploaded[ss1]|ext_in[ss1,jpg]|is_image[ss1]',
            'ss2' => 'uploaded[ss2]|ext_in[ss2,jpg]|is_image[ss2]',
            'ss3' => 'uploaded[ss3]|ext_in[ss3,jpg]|is_image[ss3]',
        ])) return $this->show('manageProduct', ['errors' => $this->validator->getErrors()]);

        $id = $this->request->getVar('id');
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
            'description' =>  $this->request->getVar('description')
        ];

        $genreM = new GenreM();
        $productM = new ProductM();

        // ako unosimo novi proizvod i ime je već zauzeto bacamo grešku
        if ($id == -1 && $productM->nameAlreadyExists($data['name'])) {
            return $this->show('manageProduct', ['errors' => ['name' => "Name [{$data['name']}] already exists"]]);
        }

        // ažuriraj bazu
        if ($id != -1)
            $genreM->where('id_product', $id)->delete();
        $productM->save($data);

        // napravi niz žanrova
        $genres = explode(' ', $this->request->getVar('genres'));

        if ($id == -1)
            $id = $productM->getInsertID();

        foreach ($genres as $genre) {
            $genreM->insertComposite($id, $genre);
        }

        $targetDir = "uploads/product/$id";
        $this->upload($targetDir, 'banner', 'banner');
        $this->upload($targetDir, 'background', 'background');
        $this->upload($targetDir, 'ss1', 'ss1');
        $this->upload($targetDir, 'ss2', 'ss2');
        $this->upload($targetDir, 'ss3', 'ss3');

        return redirect()->to(site_url("User/Product/" . $id));
    }

    public function addBundle() {
        $this->manageBundle();
    }

    public function manageBundle($id = null) {
        $data = [];
        $bundle = null;

        if ($id != null) {
            $bundle = (new BundleM())->find($id);

            if (!is_object($bundle)) {
                $data['errors'] = ['bundle' => "bundle doesn't exist, redirected to adding new bundle"];
            }
        }

        $data['bundle'] = $bundle;

        $this->show('manageBundle', $data);
    }

    public function manageBundleSubmit() {
        $max_disc = MAX_BUNDLE_DISCOUNT;
        $min_disc = MIN_BUNDLE_DISCOUNT;
        $max_descr = MAX_DESCRIPTION_SIZE;
        $min_descr = MIN_DESCRIPTION_SIZE;

        // TODO da se inputi forme zadržavaju nakon neuspešne validacije
        // TODO da se banner/background zadržavaju tokom editovanja bundle-a
        if (!$this->validate([
            'name' =>        'required',
            'discount' =>    "required|integer|less_than_equal_to[$max_disc]|greater_than_equal_to[$min_disc]",
            'description' => "required|min_length[$min_descr]|max_length[$max_descr]",

            'banner' =>      'uploaded[banner]|ext_in[banner,jpg]|is_image[banner]',
            'background' =>  'uploaded[background]|ext_in[background,jpg]|is_image[background]'

            // TODO za veličinu isto ograničenje za slike
            // TODO dinamička provera fajla koji može da bude uploadovan pod bilo kojim imenom
        ])) return $this->show('manageBundle', ['errors' => $this->validator->getErrors()]);

        $id = $this->request->getVar('id');
        $data = [ // niz podataka koji se čuvaju kao red u bazi
            'name' =>           trim($this->request->getVar('name')),
            'discount' =>       trim($this->request->getVar('discount')),
            'description' =>    trim($this->request->getVar('description')),
            'id' => ($id != -1) ? $id : ''
        ];

        $bundleM = new BundleM();
        // ako se ubacuje novi bundle i ako bundle sa takvim imenom već postoji
        if ($id == -1 && $bundleM->nameAlreadyExists($data['name'])) {
            return $this->show('manageBundle', ['errors' => ['name' => 'name already exists in database']]);
        }

        $bundleM->save($data);

        $target_dir = 'uploads/bundle/' . (($id == -1) ? $bundleM->getInsertID() : $id);
        $this->upload($target_dir, 'banner', 'banner');
        $this->upload($target_dir, 'background', 'background');

        return redirect()->to(site_url("User/Bundle/" . $id));
    }

    public function DeleteReviewAdminSubmit($id, $posterUsername) {
        $poster = (new UserM())->where('username', $posterUsername)->first();

        $user = $this->session->get('user');

        (new OwnershipM())->where('id_product', $id)->where('id_user', $poster->id)->set(['rating' => NULL, 'text' => NULL])->update();

        (new ReviewVoteM())->where('id_product', $id)->where("id_poster", $poster->id)->delete();

        return redirect()->to(site_url("User/Product/{$id}"));
    }
}
