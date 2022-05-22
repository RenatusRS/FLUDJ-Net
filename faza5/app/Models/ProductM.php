<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductM extends Model {
    protected $table = 'product';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $allowedFields = ['name', 'price', 'base_game', 'discount', 'discount_expire', 'description', 'developer', 'publisher', 'release_date', 'os_min', 'ram_min', 'gpu_min', 'cpu_min', 'mem_min', 'os_rec', 'ram_rec', 'gpu_rec', 'cpu_rec', 'mem_rec'];

    protected $validationRules = [
        'name' => [
            'rules'  => "required|alpha_numeric_space|is_unique[product.name]",
            'errors' => [
                'is_unique' => 'Name of product already exists in database.'
            ]
        ]
    ];
    public function getHeroProduct() {
        // TODO
    }
    public function getHighRatingProducts() {
        // TODO
    }
    public function getTopSellersProducts() {
        // TODO
    }
    public function getDiscountedProducts() {
        // TODO
    }
    public function getDiscoveryProducts() {
        // TODO
    }
    public function getCouponProducts() {
        // TODO
    }
    public function getProductsUserLike() {
        // TODO
    }
    public function getProducsUserFriendsLike() {
        // TODO
    }
    public function getSimilarProducts($productId) {
        // TODO
    }

}
