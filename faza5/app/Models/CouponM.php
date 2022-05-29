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
     * @return boolean korisnik poseduje kupon
     */
    public function ownsCoupon($idOwner, $idProduct) {
        return ($this->couponWorth($idOwner, $idProduct) != 0);
    }
    /**
     * dohvata sve kupone za korisnika $idOwner
     * vraća generator kupona
     *
     * @param  mixed $idOwner
     */
    public function getAllCoupons($idOwner) {
        $query = $this->db->query(
            "SELECT coupon.discount as coupon, product.*
             FROM coupon
             JOIN product ON id_product = id
             WHERE id_owner = $idOwner;"
        );

        foreach ($query->getResult('object') as $row) {
            yield $row;
        }
    }

    /**
     * vraća vrednost kupona koji je vezan za korisnika $idOwner i proizvod $idProduct (ako kupon ne postoji, vraća 0)
     *
     * @param  integer $idOwner
     * @param  integer $idProduct
     * @return integer discount
     */
    public static function couponWorth($idOwner, $idProduct) {
        $coupon = (new CouponM())->where('id_product', $idProduct)
                                 ->where('id_owner', $idOwner)
                                 ->first();

        return (isset($coupon)) ?
            $coupon->discount :
            0;
    }
    /**
     * dodeljuje kupon ako ne postoji ili ga unapređuje
     * kupon se odnosi na korisnika sa id-jem $idOwner i proizvod sa id-jem $idProduct
     *
     * @param  integer $idOwner
     * @param  integer $idProduct
     * @return void
     */
    public static function upgradeCoupon($idOwner, $idProduct) {
        $currentWorth = self::couponWorth($idOwner, $idProduct);

        $couponM = new CouponM();
        self::removeCoupon($idOwner, $idProduct);

        $worth = $currentWorth + COUPON_INCREMENT;
        if ($worth > MAX_COUPON_DISCOUNT)
            $worth = MAX_COUPON_DISCOUNT;

        $couponM->insert([
            'id_product' => $idProduct,
            'id_owner'   => $idOwner,
            'discount'   => $worth
        ]);
    }
    public static function removeCoupon($idOwner, $idProduct) {
        (new CouponM())->where('id_product', $idProduct)
                       ->where('id_owner', $idOwner)
                       ->delete();
    }
}