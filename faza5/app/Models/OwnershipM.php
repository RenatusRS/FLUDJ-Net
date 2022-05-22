<?php

namespace App\Models;

use CodeIgniter\Model;

class OwnershipM extends Model {
    protected $table = 'ownership';
    protected $primaryKey = 'id_product';

    protected $returnType = 'object';

    protected $allowedFields = ['id_product', 'id_user', 'text', 'rating'];

    /**
     * Proverava da li korisnik sa id-jem $idUser poseduje
     * proizvod sa id-jem $idProduct
     *
     * @param  integer $idUser
     * @param  integer $idProduct
     * @return boolean korisnik poseduje proizvod
     */
    public function owns($idUser, $idProduct) {
        $query = $this ->where('id_product', $idProduct)
                       ->where('id_user', $idUser)
                       ->first();

        return (isset($query));
    }

    /**
     * korisnik sa id-jem $idUser dobija proizvod sa id-jem $idProduct ako ga već nije imao.
     *
     * @param  mixed $idUser
     * @param  mixed $idProduct
     * @return boolean vraća true ako ga je dobio, a false ako nije
     */
    public function acquire($idUser, $idProduct) {
        if ($this->owns($idUser, $idProduct))
            return false;

        $this->db = \Config\Database::connect();
        $this->db->query("INSERT INTO $this->table
                          (id_product, id_user) VALUES
                          ('$idProduct', '$idUser'); ");

        return true;
    }

    public function getRatingSums() {
        $this->db = \Config\Database::connect();
        $res = $this->db->query("SELECT id_product, sum(rating) as s, count(*) as cnt
                                 FROM $this->table
                                 GROUP BY id_product; ");

        foreach ($res->getResult('array') as $row) {
            yield $row;
        }
    }

    public static function getProductRating($product) {
        $average = (float)($product['s'] / (5 * $product['cnt']));
        $score = $average - ($average - 0.5) * 2 ** -log10( $product['cnt'] + 1);
        // malo je previše pristrasna formula u regresiji ka proseku
        return $score * 5;
    }

    public static function getTopProducts() {
        $products = iterator_to_array((new OwnershipM())->getRatingSums());

        usort($products, fn ($p1, $p2) =>
                   ($this->getProductRating($p2) <=> $this->getProductRating($p1)));

        return $products;
    }
}
