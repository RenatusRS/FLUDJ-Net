<?php

namespace App\Models;

use CodeIgniter\Model;

function interleave_arrays() {
    $output = array();
    for ($args = func_get_args(); count($args); $args = array_filter($args)) {
        foreach ($args as &$arg) { // BUGFIX moguće je da ovde postoji bug jer se pojavljuju neki indeksi u povratnom nizu niotkuda
            $output[] = array_shift($arg);
        }
    }
    return array_values(array_filter($output));
}

class ProductM extends Model {
    protected $table = 'product';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $allowedFields = ['name', 'price', 'base_game', 'discount', 'discount_expire', 'description', 'developer', 'publisher', 'release_date', 'os_min', 'ram_min', 'gpu_min', 'cpu_min', 'mem_min', 'os_rec', 'ram_rec', 'gpu_rec', 'cpu_rec', 'mem_rec', 'rev_cnt', 'rev_sum'];

    protected $validationRules = [
        'name' => [
            'rules'  => 'required|is_unique[product.name]',
        ]
    ];

    /** 
     * Poredjenje unetog i trenutnog datuma
     * @return bool
     */
    public static function future_date($date) {
        $curdate = date("Y/m/d");

        $date1 = date_create($curdate);
        $date2 = date_create($date);

        return $date1 < $date2;
    }

    /** 
     * Vracanje popusta
     * @return int
     */
    public function getDiscount($id) {
        $product = $this->find($id);
        $discountExpired = self::future_date($product->discount_expire);

        if (!$discountExpired) {
            $this->update($id, [
                'discount' => 0,
                'discount_expire' => "2000-01-01"
            ]);

            return 0;
        }

        return $product->discount;
    }

    /** 
     * Vracanje cene sa popustom
     * @return double
     */
    public function getDiscountedPrice($id) {
        return ((100 - $this->getDiscount($id)) / 100) * $this->find($id)->price;
    }


    /**
     * vraća sve objekte proizvoda u generatoru
     *
     * @param  boolean $filterDLCs ako je truthy, DLC-evi se ne vraćaju u generatoru
     */
    public function getAllProducts($filterDLCs = true) {
        $this->db = \Config\Database::connect();
        $res = $this->db->query(
            "SELECT *
                                 FROM $this->table;"
        );

        foreach ($res->getResult('object') as $product) {
            if ($filterDLCs && isset($product->base_game))
                continue;
            yield $product;
        }
    }

    /**
     * vraća rejting nečega sa pristrasnošću ka proseku. veći broj ocena smanjuje težnju ka proseku
     *
     * @param  integer $cnt koliko puta je nešto ocenjeno
     * @param  integer $sum suma svih ocena
     * @param  integer $base baza o kojoj se radi. ako je rangiranje binarno (da-ne), baza je 1, ako je rangiranje npr od 1-5, baza je 5 itd.
     * @return integer rejting, veće = bolje
     */
    public static function getRating($cnt, $sum, $base = 1) {
        if ($cnt == 0)
            return 0;

        $average = (float)($sum / ($base * $cnt));
        $score = $average - ($average - 0.5) * 2 ** (-log10($cnt + 1));

        return $score * $base;
    }

    public static function getProductRating($product) {
        if ($product->rev_cnt == 0)
            return 0;
        $average = (float)($product->rev_sum / (5 * $product->rev_cnt));
        $score = $average - ($average - 0.5) * 2 ** -log10($product->rev_cnt + 1);
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

        usort($products, fn ($p1, $p2) => (ProductM::getProductRating($p2) <=> ProductM::getProductRating($p1)));

        return $products;
    }

    // ====================== front page algoritmi ==================

    /**
     * dohvata proizvod iz najbolje prodavanih ili najbolje ocenjenih, nasumično
     *
     * @param  integer $idUser
     * @return object proizvod
     */
    public function getHeroProduct($idUser = null) {
        $res = (rand() % 2 == 0) ?
            $this->getHighRatingProducts($idUser, 0, 5) :
            $this->getTopSellersProducts($idUser, 0, 5);

        $cnt = count($res);
        return ($cnt > 0) ?
            $res[rand(0, $cnt - 1)] :
            [];
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

        return array_values(($limit <= 0) ?
            $results :
            array_slice($results, ($offset * $limit), $limit));
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
            $results = array_filter($results, function ($temp) use (&$idUser) {
                return !((new OwnershipM())->owns($idUser, $temp->id));
            });
        }

        return array_values(($limit <= 0) ?
            $results :
            array_slice($results, ($offset * $limit), $limit));
    }

    /**
     * dohvata niz proizvoda na sniženju. bolja sniženja i bolje ocenjeni proizvodi će biti
     * više pri početku.
     *
     * $limit označava koliko dugačak povratni niz se traži.
     *
     * ako se pretražuje više stranica (npr na svakoj stranici ima 10 proizvoda),
     * $offset uvek označava koja stranica se prikazuje, npr sa $limit = 5 i $offset = 2,
     * prikazivali bi se proizvodi od 11-15 po poretku
     *
     * @param  integer $idUser
     * @param  integer $offset
     * @param  integer $limit
     * @return array
     */
    public function getDiscountedProducts($idUser = null, $offset = 0, $limit = 0) {
        $results = iterator_to_array($this->getAllProducts());

        $results = array_filter($results, function ($product) use (&$idUser) {
            return (ProductM::future_date($product->discount_expire)) &&
                (!isset($idUser) || !((new OwnershipM())->owns($idUser, $product->id)));
        }); // lambda kaže "ako je proizvod na sniženju + ako korisnik nije ulogovan, ili ako ulogovan ne poseduje proizvod, ostavi ga u nizu"

        usort($results, fn ($p1, $p2) =>
        ProductM::getDiscountRating($p2) <=> ProductM::getDiscountRating($p1));

        return array_values(($limit <= 0) ?
            $results :
            array_slice($results, ($offset * $limit), $limit));
    }

    /**
     * dohvata proizvode iz najbolje ocenjenih, sličnih kategorija korisnika i kategorija koje
     * prijatelji korisnika vole.
     *
     * @param  integer $idUser
     * @return array
     */
    public function getDiscoveryProducts($idUser = null) {
        if ($idUser == null)
            return [];

        $matching = array();
        $fun = function (&$p) use (&$matching) {
            $id = $p->id;
            if (array_key_exists($id, $matching))
                return false;

            $matching[$id] = 1;
            return true;
        };

        $res1 = $this->getHighRatingProducts($idUser, 0, DISCOVERY_LENGTH);
        foreach ($res1 as $p)
            $matching[$p->id] = 1;

        $res2 = $this->getProductsUserLike($idUser, 0, DISCOVERY_LENGTH);
        $res2 = array_filter($res2, function ($p) use (&$fun) {
            return $fun($p);
        });

        $res3 = $this->getProductsUserFriendsLike($idUser, 0, DISCOVERY_LENGTH);
        $res3 = array_filter($res3, function ($p) use (&$fun) {
            return $fun($p);
        });

        $result = interleave_arrays($res1, $res2, $res3);

        return array_values(array_slice($result, 0, DISCOVERY_LENGTH));
    }

    /**
     * dohvata niz proizvoda za koje korisnik $idUser ima kupone.
     * rangirani su, npr jaki kuponi za bolju igricu vrede više nego jaki kuponi
     * za lošiju igricu itd.
     *
     * @param  integer $idUser
     * @param  integer $offset
     * @param  integer $limit
     * @return array
     */
    public function getCouponProducts($idUser = null, $offset = 0, $limit = 0) {
        if ($idUser == null)
            return [];

        $products = iterator_to_array((new CouponM())->getAllCoupons($idUser));

        usort($products, function ($p1, $p2) {
            $c1 = $p1->coupon;
            $c2 = $p2->coupon;
            return ProductM::getCouponRating($p2, $c2) <=> ProductM::getCouponRating($p1, $c1);
        });

        return array_values(($limit <= 0) ?
            $products :
            array_slice($products, ($offset * $limit), $limit));
    }

    /**
     * dohvata proizvode poređane po tome koliko su slični (po broju žanrova) sa proizvodima
     * koje korisnik ($idUser) već poseduje
     *
     * $limit označava koliko dugačak povratni niz se traži.
     *
     * ako se pretražuje više stranica (npr na svakoj stranici ima 10 proizvoda),
     * $offset uvek označava koja stranica se prikazuje, npr sa $limit = 5 i $offset = 2,
     * prikazivali bi se proizvodi od 11-15 po poretku (najviše sličnih proizvoda)
     *
     * @param  integer $idUser
     * @param  integer $offset
     * @param  integer $limit
     * @return array
     */
    public function getProductsUserLike($idUser = null, $offset = 0, $limit = 0) {
        if ($idUser == null)
            return [];

        $products = [];
        $ownM = (new OwnershipM());
        foreach ($ownM->matchingGenres($idUser) as $product) {
            if ($ownM->owns($idUser, $product->id))
                continue;

            array_push($products, $product);
        }

        usort($products, fn ($p1, $p2) => // TODO za sada je jedini kriterijum sortiranja koliko žanrova se matchuje
        $p2->matching <=> $p1->matching);

        return array_values(($limit <= 0) ?
            $products :
            array_slice($products, ($offset * $limit), $limit));
    }

    /**
     * dohvata proizvode koje su prijatelji najbolje ocenili
     *
     * $limit označava koliko dugačak povratni niz se traži.
     *
     * ako se pretražuje više stranica (npr na svakoj stranici ima 10 proizvoda),
     * $offset uvek označava koja stranica se prikazuje, npr sa $limit = 5 i $offset = 2,
     * prikazivali bi se proizvodi od 11-15 po poretku
     *
     * @param  integer $idUser
     * @param  integer $offset
     * @param  integer $limit
     * @return array
     */
    public function getProductsUserFriendsLike($idUser = null, $offset = 0, $limit = 0) {
        if ($idUser == null)
            return [];

        $temp = iterator_to_array((new OwnershipM())->friendsLikes($idUser));
        usort($temp, fn ($t1, $t2) =>
        ProductM::getRating($t2->rev_cnt, $t2->rev_sum, 5) <=> ProductM::getRating($t1->rev_cnt, $t1->rev_sum, 5));

        $products = [];
        foreach ($temp as $t) {
            $id = $t->id_product;
            if ((new OwnershipM())->owns($idUser, $id))
                continue;

            $product = (new ProductM())
                ->find($id);
            array_push($products, $product);
        }

        return array_values(($limit <= 0) ?
            $products :
            array_slice($products, ($offset * $limit), $limit));
    }

    /**
     * uzima najsličnije proizvode proizvodu sa id-jem $productId
     *
     * $limit označava koliko dugačak povratni niz se traži.
     *
     * ako se pretražuje više stranica (npr na svakoj stranici ima 10 proizvoda),
     * $offset uvek označava koja stranica se prikazuje, npr sa $limit = 5 i $offset = 2,
     * prikazivali bi se proizvodi od 11-15 po poretku
     *
     * @param  integer $productId id proizvoda za koje se traže slični
     * @param  integer $offset objašnjeno u opisu funkcije
     * @param  integer $limit objašnjeno u opisu funkcije
     * @return array vraća niz objekta proizvoda poređanih po sličnosti opadajuće
     */
    public function getSimilarProducts($productId, $idUser = null, $offset = 0, $limit = 0) {
        $similar = (new GenreM())->getSimilarProducts($productId);

        $products = [];
        foreach ($similar as $p) {
            if ($idUser != null && ((new OwnershipM())->owns($idUser, $p->id)))
                continue;

            array_push($products, $p);
        }

        usort($products, fn ($p1, $p2) => // za sada usort samo radi po tome ko se više puta pojavljuje, TODO
        $p2->match_count <=> $p1->match_count);

        return array_values(($limit <= 0) ?
            $products :
            array_slice($products, ($offset * $limit), $limit));
    }

    public function getBackground($id) {
        $background = $this->getAsset('uploads/product/' . $id . '/background.png');

        return $background ?: null;
    }
}
