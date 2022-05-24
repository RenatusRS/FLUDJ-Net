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
    public function getHighRatingProducts($idUser = null, $offset = 0, $limit = 5) {
        $results = OwnershipM::getTopProducts();

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
    public function getTopSellersProducts($idUser = null, $offset = 0, $limit = 5) {
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
        $results = iterator_to_array((new OwnerShipM())->getRatingSums());

        $results = array_filter($results, function($product) use (&$idUser) {
            return ($product->discount > 0) &&
                   (!isset($idUser) || !((new OwnershipM())->owns($idUser, $product->id)));
        }); // lambda kaže "ako je proizvod na sniženju + ako korisnik nije ulogovan, ili ako ulogovan ne poseduje proizvod, ostavi ga u nizu"

        usort($results, fn($p1, $p2) =>
                    OwnershipM::getDiscountRating($p2) <=> OwnershipM::getDiscountRating($p1));

        return $results;
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
            array_push($products, (new ProductM())->find($id));
            $products[$id]['coupon'] = $coupon->discount;
        }

        usort($products, function ($p1, $p2) {
            $c1 = $p1['coupon'];
            $c2 = $p2['coupon'];
            return OwnershipM::getCouponRating($p2, $c2) <=> OwnershipM::getCouponRating($p1, $c1);
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
    public function getSimilarProducts($productId, $offset = 0, $limit = 0) {
        $similar = (new GenreM())->getSimilarProducts($productId);

        $products = [];
        foreach ($similar as $product) {
            $id = $product['id_product'];
            $newProduct = (new ProductM())->find($id);
            $newProduct['match_count'] = $product['match_count'];
            array_push($products, $newProduct);
        }

        usort($products, fn ($p1, $p2) =>  // za sada usort samo radi po tome ko se više puta pojavljuje, TODO
                                $p2['match_count'] <=> $p1['match_count']);

        return ($limit <= 0) ?
            $products :
            array_slice($products, ($offset * $limit), $limit);
    }

}
