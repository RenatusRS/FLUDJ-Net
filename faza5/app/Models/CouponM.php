<?php

namespace App\Models;

use CodeIgniter\Model;

class CouponM extends Model {
    protected $table = 'coupon';
    protected $primaryKey = 'id_product';

    protected $returnType = 'object';

    protected $allowedFields = ['id_product', 'id_owner', 'discount'];

    /**
     * Proverava da li korisnik sa id-jem $idOwner poseduje
     * kupon za proizvod sa id-jem $idProduct
     *
     * @param  integer $idOwner
     * @param  integer $idProduct
     * @return boolean korisnik poseduje proizvod
     */
    public function ownsCoupon($idOwner, $idProduct) {
        $query = $this->where('id_product', $idProduct)
                      ->where('id_owner', $idOwner)
                      ->first();

        return (isset($query));
    }
    /**
     * dohvata sve kupone za korisnika $idOwner
     * vraća generator kupona
     *
     * @param  mixed $idOwner
     */
    public function getAllCoupons($idOwner) {
        $res = $this->where('id_owner', $idOwner)
                    ->findAll();

        foreach ($res as $row) {
            yield $row;
        }
    }

}