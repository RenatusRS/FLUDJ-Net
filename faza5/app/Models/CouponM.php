<?php

/**
 * @author
 * Fedja Mladenovic 2019/0613
 * 
 * Opis: Model za kupone
 * 
 * @version 1.0
 * 
 */

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
     * @param  integer $idOwner
     * @param  boolean $filterDLCs ako je truthy, DLC-evi se ne vraćaju u generatoru
     */
    public function getAllCoupons($idOwner, $filterDLCs = true) {
        $query = $this->db->query(
            "SELECT coupon.discount as coupon, product.*
             FROM coupon
             JOIN product ON id_product = id
             WHERE id_owner = $idOwner;"
        );

        foreach ($query->getResult('object') as $row) {
            if ($filterDLCs && isset($row->base_game))
                continue;
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
            (int)($coupon->discount) :
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

    /**
     * uklanja kupon korisnika sa id-jem $idOwner za proizvod sa id-jem $idProduct
     *
     * @param  integer $idOwner
     * @param  integer $idProduct
     * @return void
     */
    public static function removeCoupon($idOwner, $idProduct) {
        (new CouponM())->where('id_product', $idProduct)
            ->where('id_owner', $idOwner)
            ->delete();
    }

    /**
     * dodeljuje kupon korisniku sa id-jem $idUser
     *
     * @param  integer $idUser
     * @return boolean da li je uspešno dodeljen kupon
     */
    public static function awardCoupon($idUser) {
        $products = (new ProductM())->getDiscoveryProducts($idUser);
        $products = array_values(array_filter($products, function ($p) use (&$idUser) {
            $c = CouponM::couponWorth($idUser, $p->id);
            return ($c < MAX_COUPON_DISCOUNT);
        })); // lambda filtrira sve proizvode za koje postoji max kupon, a array values vraća ključeve da kreću od 0

        $cnt = count($products);
        if ($cnt == 0)
            return false;

        $choice = $products[rand(0, $cnt - 1)];
        CouponM::upgradeCoupon($idUser, $choice->id);
        return true;
    }

    /**
     * dodeli poenu korisniku sa id-jem $idUser na osnovu toga koliko je para potrošio
     *
     * @param  integer $idUser
     * @param  integer $spent
     * @return void
     */
    public static function awardPoints($idUser, $spent) {
        $points = (int)($spent * POINTS_PRODUCT);

        $userM = new UserM();
        $user = $userM->find($idUser);
        $currentPoints = $user->points + $points;

        $userM->update($idUser, [
            'points' => $currentPoints
        ]);
    }
}
