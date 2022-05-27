<?php

namespace App\Models;

use CodeIgniter\Model;

class BundledProductsM extends Model {

    protected $table = 'bundled';
    protected $primaryKey = 'id_bundle';

    protected $returnType = 'object';

    protected $allowedFields = ['id_bundle', 'id_product'];

    public $db;

    /**
     * ukloni proizvod sa id-jem $idProduct iz kolekcije sa id-jem $idBundle
     *
     * @param  integer $idBundle
     * @param  integer $idProduct
     * @return void
     */
    public static function removeFromBundle($idBundle, $idProduct) {
        (new BundledProductsM())->where('id_bundle', $idBundle)
                                ->where('id_product', $idProduct)
                                ->delete();
    }
    /**
     * dodaj proizvod sa id-jem $idProduct u kolekciju sa id-jem $idBundle
     *
     * @param  integer $idBundle
     * @param  integer $idProduct
     * @return void
     */
    public static function addToBundle($idBundle, $idProduct) {
        (new BundledProductsM())->insert([
            'id_bundle'  => $idBundle,
            'id_product' => $idProduct
        ]);
    }
    /**
     * find products from bundle with id $id
     *
     * @param  integer $id
     * @return array array of id-s of found products
     */
    public function findBundledProducts($id) { // TODO ovo je isti kod kao traženje žanrova. napraviti u nadklasi apstrakciju ova dva
        $rows = $this->where($this->primaryKey, $id)
                     ->findAll();

        $result = [];
        foreach ($rows as $row) {
            array_push($result, $row->id_product);
        }

        return $result;
    }

    /**
     * proverava da li je proizvod sa id-jem $productId u kolekciji
     * sa id-jem $bundleId
     *
     * @param  integer $bundleId
     * @param  integer $productId
     * @return bool
     */
    public function inBundle($idBundle, $idProduct) {
        $query = $this->where('id_product', $idProduct)
                      ->where('id_bundle', $idBundle)
                      ->first();

        return (isset($query));
    }

    /**
     * vrati sve proizvode koji se nalaze u kolekciji sa id-jem $idBundle
     *
     * @param  integer $idBundle
     */
    public function productsInBundle($idBundle) {
        $query = $this->db->query(
            "SELECT id_bundle, product.*
             FROM bundled
             JOIN product ON bundled.id_product = product.id
             WHERE bundled.id_bundle = $idBundle;"
        );

        foreach ($query->getResult('object') as $row) {
            yield $row;
        }
    }
    /**
     * vrati sve proizvode koji se ne nalaze u kolekciji sa id-jem $idBundle
     *
     * @param  integer $idBundle
     */
    public function productsNotInBundle($idBundle) {
        $query = $this->db->query(
            "SELECT *
             FROM product
             WHERE product.id NOT IN
             (
                SELECT id_product
                FROM bundled
                WHERE id_bundle = $idBundle
             );"
        );

        foreach ($query->getResult('object') as $row) {
            yield $row;
        }
    }
}
