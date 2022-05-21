<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductM extends Model {
    protected $table = 'product';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $allowedFields = ['name', 'price', 'base_game', 'discount', 'discount_expire', 'description', 'developer', 'publisher', 'release_date', 'os_min', 'ram_min', 'gpu_min', 'cpu_min', 'mem_min', 'os_rec', 'ram_rec', 'gpu_rec', 'cpu_rec', 'mem_rec'];

    public function getDiscount($id) {
        $product = $this->find($id);

        if ($product->discount_expire == NULL) return 26;

        return $product->discount;
    }

    public function getDiscountedPrice($id) {
        return ((100 - $this->getDiscount($id)) / 100) * $this->find($id)->price;
    }

    public function getHeroProduct() {
        return $this->find(3);
    }

    public function getHighRatingProducts() {
        return array(
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3)
        );
    }

    public function getTopSellersProducts() {
        return array(
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3)
        );
    }

    public function getDiscountedProducts() {
        return array(
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3)
        );
    }

    public function getDiscoveryProducts() {
        return array(
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3)
        );
    }

    public function getCouponProducts() {
        return array(
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3)
        );
    }

    public function getProductsUserLike() {
        return array(
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3)
        );
    }

    public function getProductsUserFriendsLike() {
        return array(
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3)
        );
    }

    public function getSimilarProducts($productId) {
        return array(
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3),
            $this->find(3)
        );
    }

    protected $validationRules = [
        'name' => [
            'rules'  => "required|alpha_numeric_space|is_unique[product.name]",
            'errors' => [
                'is_unique' => 'Name of product already exists in database.'
            ]
        ]
    ];
}
