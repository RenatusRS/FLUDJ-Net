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

        array_walk($results, fn ($id) =>
                                    (new ProductM())->find($id));

        return ($limit <= 0) ?
            $results :
            array_slice($results, ($offset * $limit), $limit);
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
