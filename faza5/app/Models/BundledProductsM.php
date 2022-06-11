<?php

/**
 * @author
 * Fedja Mladenovic 2019/0613
 * 
 * Opis: Model za produkte u bundlu
 * 
 * @version 1.0
 * 
 */

namespace App\Models;

use CodeIgniter\Model;

class BundledProductsM extends Model {

    protected $table = 'bundled';
    protected $primaryKey = 'id_bundle';

    protected $returnType = 'object';

    protected $allowedFields = ['id_bundle', 'id_product'];

    public $db;

    /**
     * otklanja sve proizvode iz kolekcije sa id-jem $idBundle
     *
     * @param  integer $idBundle
     * @return void
     */
    public static function clearBundle($idBundle) {
        (new BundledProductsM())->where('id_bundle', $idBundle)
            ->delete();
    }
    /**
     * dodaj proizvod sa id-jem $idProduct u kolekciju sa id-jem $idBundle
     *
     * @param  integer $idBundle
     * @param  integer $idProduct
     * @return boolean
     */
    public static function addToBundle($idBundle, $idProduct) {
        $model = (new BundledProductsM());
        if ($model->existsInBundle($idBundle, $idProduct))
            return false;
        $model->insert([
            'id_bundle'  => $idBundle,
            'id_product' => $idProduct
        ]);
        return true;
    }
    /**
     * vraća true ako proizvod sa id-jem $idProduct postoji u kolekciji sa id-jem $idBundle
     *
     * @param  integer $idBundle id kolekcije
     * @param  integer $idProduct id proizvoda
     * @return boolean
     */
    public function existsInBundle($idBundle, $idProduct) {
        $res = $this->where('id_bundle', $idBundle)
                    ->where('id_product', $idProduct)
                    ->first();

        return (isset($res));
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
