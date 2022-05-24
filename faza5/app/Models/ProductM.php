<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductM extends Model {
    protected $table = 'product';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $allowedFields = ['name', 'price', 'base_game', 'discount', 'discount_expire', 'description', 'developer', 'publisher', 'release_date', 'os_min', 'ram_min', 'gpu_min', 'cpu_min', 'mem_min', 'os_rec', 'ram_rec', 'gpu_rec', 'cpu_rec', 'mem_rec', 'rev_cnt', 'rev_sum'];

    protected $validationRules = [
        'name' => [
            'rules'  => "required|alpha_numeric_space|is_unique[product.name]",
            'errors' => [
                'is_unique' => 'Name of product already exists in database.'
            ]
        ]
    ];

    public function getAllProducts() {
        $this->db = \Config\Database::connect();
        $res = $this->db->query("SELECT *
                                 FROM $this->table;"
        );

        foreach ($res->getResult('array') as $product) {
            yield $product;
        }
    }
    public static function getProductRating($product) {
        if ($product['rev_cnt'] == 0)
            return 0;
        $average = (float)($product['rev_sum'] / (5 * $product['rev_cnt']));
        $score = $average - ($average - 0.5) * 2 ** -log10( $product['rev_cnt'] + 1);
        // malo je previše pristrasna formula u regresiji ka proseku
        return $score * 5;
    }
    public static function getDiscountRating($product, $couponDiscount = null) {
        $discount = ($couponDiscount == null) ?
            $product->discount :
            $couponDiscount;
        $rating = ProductM::getProductRating($product);
        $score = $rating * $discount ** (log10($rating));

        return $score;
    }
    public static function getCouponRating($product, $coupon) {
        return ProductM::getDiscountRating($product, $coupon);
    }
    public static function getTopProducts() {
        $products = iterator_to_array((new ProductM())->getAllProducts());

        usort($products, fn ($p1, $p2) =>
                   (ProductM::getProductRating($p2) <=> ProductM::getProductRating($p1)));

        return $products;
    }

    // ====================== front page algoritmi ==================

    public function getHeroProduct() {
        // TODO
    }
    /**
     * uzima najbolje ocenjene proizvode po formuli datoj u OwnershipM::getRating(...).
     * u slučaju da niko nije ulogovan, prikazuje ih tako kakve jesu, ako je neko ulogovan
     * prikazuje samo one koje korisnik ne poseduje
     *
     * $limit označava koliko dugačak povratni niz se traži.
     *
     * ako se pretražuje više stranica (npr na svakoj stranici ima 10 proizvoda),
     * $offset uvek označava koja stranica se prikazuje, npr sa $limit = 5 i $offset = 2,
     * prikazivali bi se proizvodi od 11-15 po poretku (najvećih korisničkih ocena)
     *
     * @param  integer $idUser id trenutnog korisnika (NULL za gosta)
     * @param  integer $offset objašnjeno u opisu funkcije
     * @param  integer $limit objašnjeno u opisu funkcije
     * @return array vraća niz objekta proizvoda poređanih po ocenama
     */
    public function getHighRatingProducts($idUser = null, $offset = 0, $limit = 0) {
        $results = $this->getTopProducts();

        if (isset($idUser)) { // ako korisnik nije ulogovan prikazuju mu se svi proizvodi jer ni jedan ne poseduje
            $results = array_filter($results, function ($product) use (&$idUser) {
                return !((new OwnershipM())->owns($idUser, $product->id));
            }); // lambda za filtriranje niza kaže: "ako ulogovan korisnik ne poseduje proizvod, ubaci proizvod u niz"
        }

        return ($limit <= 0) ?
            $results :
            array_slice($results, ($offset * $limit), $limit);
    }
    /**
     * uzima najprodavanije proizvode
     * u slučaju da niko nije ulogovan, prikazuje ih tako kakve jesu, ako je neko ulogovan
     * prikazuje samo one koje korisnik ne poseduje
     *
     * $limit označava koliko dugačak povratni niz se traži.
     *
     * ako se pretražuje više stranica (npr na svakoj stranici ima 10 proizvoda),
     * $offset uvek označava koja stranica se prikazuje, npr sa $limit = 5 i $offset = 2,
     * prikazivali bi se proizvodi od 11-15 po poretku (najviše prodanih kopija)
     *
     * @param  integer $idUser id trenutnog korisnika (NULL za gosta)
     * @param  integer $offset objašnjeno u opisu funkcije
     * @param  integer $limit objašnjeno u opisu funkcije
     * @return array vraća niz objekta proizvoda poređanih količini prodanih kopija
     */
    public function getTopSellersProducts($idUser = null, $offset = 0, $limit = 0) {
        $results = iterator_to_array((new OwnershipM())->ownedSum());

        if (isset($idUser)) {
            $results = array_filter($results, function ($productId) use (&$idUser) {
                return !((new OwnershipM())->owns($idUser, $productId));
            });
        }

        return ($limit <= 0) ?
            $results :
            array_slice($results, ($offset * $limit), $limit);
    }
    public function getDiscountedProducts($idUser = null, $offset = 0, $limit = 0) {
        $results = iterator_to_array($this->getAllProducts());

        $results = array_filter($results, function($product) use (&$idUser) {
            return ($product->discount > 0) &&
                   (!isset($idUser) || !((new OwnershipM())->owns($idUser, $product->id)));
        }); // lambda kaže "ako je proizvod na sniženju + ako korisnik nije ulogovan, ili ako ulogovan ne poseduje proizvod, ostavi ga u nizu"

        usort($results, fn($p1, $p2) =>
                    ProductM::getDiscountRating($p2) <=> ProductM::getDiscountRating($p1));

        return ($limit <= 0) ?
            $results :
            array_slice($results, ($offset * $limit), $limit);
    }
    public function getDiscoveryProducts() {
        // TODO
    }
    public function getCouponProducts($idUser = null, $offset = 0, $limit = 0) {
        if ($idUser == null)
            return [];

        $coupons = (new CouponM())->getAllCoupons($idUser);
        $products = [];
        foreach ($coupons as $coupon) {
            $id = $coupon->id;
            $newProduct = (new ProductM())->find($id);
            $newProduct['coupon'] = $coupon->discount;
            array_push($products, $newProduct);
        }

        usort($products, function ($p1, $p2) {
            $c1 = $p1['coupon'];
            $c2 = $p2['coupon'];
            return ProductM::getCouponRating($p2, $c2) <=> ProductM::getCouponRating($p1, $c1);
        });

        return ($limit <= 0) ?
            $products :
            array_slice($products, ($offset * $limit), $limit);
    }
    public function getProductsUserLike() {
        // TODO
    }
    /**
     * uzima najsličnije proizvode proizvodu sa id-jem $productId
     *
     * $limit označava koliko dugačak povratni niz se traži.
     *
     * ako se pretražuje više stranica (npr na svakoj stranici ima 10 proizvoda),
     * $offset uvek označava koja stranica se prikazuje, npr sa $limit = 5 i $offset = 2,
     * prikazivali bi se proizvodi od 11-15 po poretku (najviše prodanih kopija)
     *
     * @param  integer $productId id proizvoda za koje se traže slični
     * @param  integer $offset objašnjeno u opisu funkcije
     * @param  integer $limit objašnjeno u opisu funkcije
     * @return array vraća niz objekta proizvoda poređanih po sličnosti opadajuće
     */
    public function getSimilarProducts($productId, $idUser = null, $offset = 0, $limit = 0) {
        $similar = (new GenreM())->getSimilarProducts($productId);

        $products = [];
        $counts = [];
        foreach ($similar as $product) {
            $id = $product['id_product'];
            if ($idUser != null && ((new OwnershipM())->owns($idUser, $id)))
                continue;

            $newProduct = (new ProductM())->find($id);
            array_push($products, $newProduct);

            $counts[$id] = $product['match_count'];
        }

        usort($products, function ($p1, $p2) use (&$counts) { // za sada usort samo radi po tome ko se više puta pojavljuje, TODO
            return $counts[$p2->id] <=> $counts[$p1->id];
        });

        return ($limit <= 0) ?
            $products :
            array_slice($products, ($offset * $limit), $limit);
    }

}
