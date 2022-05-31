<?php

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
            'rules'  => 'required|alpha_numeric_space',
        ]
    ];

    /**
     * određuje početnu cenu, sniženje i finalnu cenu kolekcije za trenutnog korisnika
     *
     * @param  array $products niz modela dohvaćenih iz baze sa ProductM->find($id)
     * @param  mixed $discount sniženje kolekcije
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

    public function bundleProducts($id) {
        $iter = (new BundledProductsM())
            ->where('id_bundle', $id)
            ->findAll();

        foreach ($iter as $bundle) {
            yield ((new ProductM())->find($bundle->id_product));
        }
    }

    public function getBackground($id) {
        $products = iterator_to_array($this->bundleProducts($id));
        $productM = new ProductM();

        $background = null;
        foreach ($products as $product) {
            $background = $productM->getBackground($product->id);
            if ($background != null) break;
        }

        return $background;
    }

    public function getBundles($product_id) {
        $bundlesList = (new BundledProductsM())->where('id_product', $product_id)->findAll();
        $bundles = [];

        foreach ($bundlesList as $bundled) {
            $bundle = $this->find($bundled->id_bundle);

            $bundles[$bundle->id] = $bundle;
        }

        return $bundles;
    }
}
