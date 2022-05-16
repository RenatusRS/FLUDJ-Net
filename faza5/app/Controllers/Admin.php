<?php

namespace App\Controllers;

use App\Models\GenreM;
use App\Models\ProductM;
use App\Models\BundleM;

class Admin extends BaseController {
    protected function show($page, $data = []) {
        $data['controller'] = 'User';
        $data['user'] = $this->session->get('user');
        echo view('template/header_user', $data);
        echo view("pages/$page", $data);
        echo view('template/footer');
    }

    public function manageProduct($id = null) {
        if ($id != null) {
            $product = (new ProductM())->find($id);
            $genres = implode(' ', (new GenreM())->asArray()->where('id_product', $id)->findAll());
        } else
            $product = $genres = null;

        $this->show('manageProduct', ['product' => $product, 'genres' => $genres]);
    }

    public function manageProductSubmit() {
        if (!$this->validate([
            'name' => 'required',
            'genres' => 'required',
            'price' => 'required|numeric|greater_than_equal_to[1]',

            'developer' => 'required',
            'publisher' => 'required',
            'release_date' => 'required|valid_date[d/m/Y]',

            'os_min' => 'required',
            'cpu_min' => 'required',
            'gpu_min' => 'required',
            'ram_min' => 'required',
            'mem_min' => 'required',
            'os_rec' => 'required',
            'cpu_rec' => 'required',
            'gpu_rec' => 'required',
            'ram_rec' => 'required',
            'mem_rec' => 'required',

            'banner' => 'uploaded[banner]|ext_in[banner,jpg]|is_image[banner]',

            'ss1' => 'uploaded[ss1]|ext_in[ss1,jpg]|is_image[ss1]',
            'ss2' => 'uploaded[ss2]|ext_in[ss2,jpg]|is_image[ss2]',
            'ss3' => 'uploaded[ss3]|ext_in[ss3,jpg]|is_image[ss3]',
        ])) return $this->show('manageProduct', ['errors' => $this->validator->getErrors()]);

        $background = $this->request->getVar('background');
        if ($background != null && !$this->validate(['background' => 'ext_in[background,png]|is_image[background]']))
            return $this->show('manageProduct', ['errors' => $this->validator->getErrors()]);

        $productM = new ProductM();

        $data = [
            'name' =>  $this->request->getVar('name'),
            'price' =>  $this->request->getVar('price'),
            'developer' => $this->request->getVar('developer'),
            'publisher' => $this->request->getVar('publisher'),
            'release_date' => $this->request->getVar('release_date'),
            'os_min' =>  $this->request->getVar('os_min'),
            'cpu_min' =>  $this->request->getVar('cpu_min'),
            'gpu_min' =>  $this->request->getVar('gpu_min'),
            'ram_min' =>  $this->request->getVar('ram_min'),
            'mem_min' =>  $this->request->getVar('mem_min'),
            'os_rec' =>  $this->request->getVar('os_rec'),
            'cpu_rec' =>  $this->request->getVar('cpu_rec'),
            'gpu_rec' =>  $this->request->getVar('gpu_rec'),
            'ram_rec' =>  $this->request->getVar('ram_rec'),
            'mem_rec' =>  $this->request->getVar('mem_rec'),
        ];

        $genreM = new GenreM();

        $id = $this->request->getVar('id');
        if ($id != -1)
            $data['id'] = $id;
        $genreM->where('id_product', $id)->delete();

        $genres = array_filter(explode(' ', $this->request->getVar('genres')));

        $productM->save($data);

        if ($id == -1)
            $id = $productM->getInsertID();

        foreach ($genres as $genre)
            $genreM->save([
                'id_product' => $id,
                'genre_name' => $genre,
            ]);

        $this->upload('uploads/product/' . $id, 'banner', 'banner');
        $this->upload('uploads/product/' . $id, 'background', 'background');
        $this->upload('uploads/product/' . $id, 'ss1', 'ss1');
        $this->upload('uploads/product/' . $id, 'ss2', 'ss2');
        $this->upload('uploads/product/' . $id, 'ss3', 'ss3');

        return redirect()->to(site_url("User/Product/" . $id));
    }

    public function manageBundle($id=null) {
        if ($id != null) {
            $bundle = (new BundleM())->find($id);
        } else {
            $bundle = null;
        }

        $this->show('manageBundle', ['bundle' => $bundle]);
    }

    public function manageBundleSubmit() {
        $max_disc = MAX_BUNDLE_DISCOUNT;
        $min_disc = MIN_BUNDLE_DISCOUNT;

        if (!$this->validate([
            'name' => 'required',
            'discount' => 'required|integer|less_than_equal_to[15]',

            'big_rect' => 'uploaded[big_rect]|ext_in[big_rect,jpg]|is_image[big_rect]',
            'small_rect' => 'uploaded[small_rect]|ext_in[small_rect,jpg]|is_image[small_rect]'

        ])) return $this->show('manageBundle', ['errors' => $this->validator->getErrors()]);

        $background = $this->request->getVar('background');
        if ($background != null && !$this->validate(['background' => 'ext_in[background,png]|is_image[background]']))
            return $this->show('manageBundle', ['errors' => $this->validator->getErrors()]);

        $data = [
            'name' => $this->request->getVar('name'),
            'discount' => $this->request->getVar('discount'),
            'description' => $this->request->getVar('description')
        ];

        $bundleM = new bundleM();

        $id = $this->request->getVar('id');
        if ($id != -1)
            $data['id'] = $id;

        $bundleM->save($data);

        if ($id == -1)
            $id = $bundleM->getInsertID();

        $this->upload('uploads/bundle/' . $id, 'big_rect', 'big_rect');
        $this->upload('uploads/bundle/' . $id, 'small_rect', 'small_rect');
        $this->upload('uploads/bundle/' . $id, 'background', 'background');

        return redirect()->to(site_url("User/Bundle/" . $id));
    }
}
