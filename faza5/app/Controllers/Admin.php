<?php

namespace App\Controllers;

use App\Models\GenreM;
use App\Models\ProductM;

class Admin extends BaseController {
    public function manageProduct($id = null) {
        if ($id != null) {
            $product = (new ProductM())->find($id);
            $genres = implode(' ', (new GenreM())->asArray()->where('id_product', $id)->findAll());

            $background = base_url('uploads/product/' . $id . '/background.png');
            if (!file_exists($background))
                $background = null;
        } else
            $product = $genres = $background = null;

        $this->show('manageProduct', ['product' => $product, 'genres' => $genres, 'background' => $background]);
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
}
