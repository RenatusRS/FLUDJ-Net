<?php

/**
 * Opis: Model za bundlove
 * 
 * @version 1.0
 * 
 */

namespace App\Models;

use CodeIgniter\Model;

class BundleM extends Model {

    protected $table = 'bundle';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $allowedFields = ['name', 'discount', 'description'];

    public $db;

    protected $validationRules = [
        'name' => [
            'rules'  => 'required',
        ]
    ];

    /**
     * vraća true ako postoji kolekcija sa imenom $name koja nema id = $id.
     *
     * @param  string $name
     * @param  integer $id
     * @return boolean
     */
    public function bundleNameExists($name, $id = -1) {
        $query = $this->where('name', $name)
                      ->where('id !=', $id)
                      ->first();

        return (isset($query));
    }

    /**
     * određuje početnu cenu, sniženje i finalnu cenu kolekcije za trenutnog korisnika
     *
     * @param  array $products niz modela dohvaćenih iz baze sa ProductM->find($id)
     * @param  integer $discount sniženje kolekcije
     * @return array 'price' => puna cena, 'discount' => sniženje,
     * 'final' => finalna cena kada se primeni sniženje
     *
     */
    public function bundlePrice($products, $discount, $user_id) {
        $price = 0.0;
        $owned = 0;
        $cnt = count($products);

        foreach ($products as $product) {
            $owns = (new OwnershipM())
                ->owns($user_id, $product->id);

            if ($owns === true) {
                $owned++;
            } else {
                $price += $product->price;
            }
        }

        if ($cnt == $owned) {
            $price = $discount = 0;
        } else if (($cnt - $owned) == 1) {
            $discount = 0;
        } else {
            while ($owned > 0) {
                $discount -= ceil($discount / ($cnt - 1));
                $owned--;
            }
        }

        $final = ($price == 0) ?
            0 :
            $price - ($price * $discount) / 100;

        return [
            'price'    => $price,
            'discount' => $discount,
            'final'    => $final
        ];
    }

    /**
     * vraća sve proizvode u generatoru iz kolekcije sa id-jem $id
     *
     * @param  integer $id
     */
    public function bundleProducts($id) {
        return (new BundledProductsM())->productsInBundle($id);
    }

    /**
     * dohvata lokaciju pozadine za kolekciju sa id-jem $id
     *
     * @param  integer $id
     * @return string
     */
    public function getBackground($id) {
        $products = $this->bundleProducts($id);
        $productM = new ProductM();

        $background = null;
        foreach ($products as $product) {
            $background = $productM->getBackground($product->id);
            if ($background != null) break;
        }

        return $background;
    }

    /**
     * dohvata sve kolekcije u kojima se nalazi proizvod sa id-jem $productId
     *
     * @param  integer $productId
     * @return array
     */
    public function getBundles($productId) {
        $bundlesList = (new BundledProductsM())
            ->where('id_product', $productId)
            ->findAll();
        $bundles = [];

        foreach ($bundlesList as $bundled) {
            $bundle = $this->find($bundled->id_bundle);

            $bundles[$bundle->id] = $bundle;
        }

        return $bundles;
    }
}
