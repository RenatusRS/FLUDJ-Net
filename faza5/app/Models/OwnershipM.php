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
        $query = $this->where('id_product', $idProduct)
            ->where('id_user', $idUser)
            ->first();

        return (isset($query));
    }

    /**
     * Proverava da li korisnik sa id-jem $idUser poseduje
     * proizvod sa id-jem $idProduct
     *
     * @param  integer $idUser
     * @param  integer $idProduct
     * @return boolean korisnik poseduje proizvod
     */
    public function getOwned($idUser) {
        $ownedList = $this->where('id_user', $idUser)->findAll();
        $productM = new ProductM();

        $owned = array();

        foreach ($ownedList as $own) {
            $product = $productM->find($own->id_product);

            $owned[$own->id_product] = [
                'product' => $product,
                'rating' => $own->rating,
                'review' => $own->text,
            ];
        }

        return $owned;
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

    /**
     * dohvata ocenu koju je korisnik sa id-jem $idUser ostavio na proizvodu
     * sa id-jem $idProduct
     *
     * ako nije ostavio ocenu, vraća 0
     *
     * @param  integer $idUser
     * @param  integer $idProduct
     * @return integer
     */
    public function getRating($idUser, $idProduct) {
        $query = $this
            ->where('id_user', $idUser)
            ->where('id_product', $idProduct)
            ->first();

        return (isset($query)) ?
            ($query->rating ?? 0) :
            0;
    }

    /**
     * nalazi sve proizvode koje se pojavljuju najviše puta, odnosno proizvodi
     * koje najviše ljudi ima
     *
     * $limit ograničava koliko će biti proizvoda u povratnoj vrednosti, po podrazumevanom
     * je "beskonačno" (konačno je ali nedostižan broj) jer ne može drugačije sa mysql
     *
     * ako se pretražuje više stranica (npr na svakoj stranici ima 5 proizvoda),
     * $offset uvek označava koja stranica se prikazuje, npr sa $limit = 5 i $offset = 2,
     * bili bi vraćeni id_product 11-15
     *
     * @param  integer $limit
     * @param  integer $offset
     * @param  boolean $filterDLCs ako je truthy, DLC-evi se ne vraćaju u generatoru
     */
    public function ownedSum($limit = 1000000, $offset = 0, $filterDLCs = true) { // limit koristi magičan broj 1000000 jer nema oznaka za beskonačno
        $this->db = \Config\Database::connect();
        $res = $this->db->query(
            "SELECT t.cnt, product.*
             FROM
             (
                SELECT *, count(id_product) AS cnt
                FROM $this->table
                GROUP BY id_product
             ) AS t
             JOIN product ON product.id = t.id_product
             ORDER BY t.cnt DESC
             LIMIT $limit
             OFFSET $offset; "
        );

        foreach ($res->getResult('object') as $row) {
            if ($filterDLCs && isset($row->base_game))
                continue;
            yield $row;
        }
    }
    /**
     * vraća proizvode sa dodatom kolonom "matching" koja označava
     * koliko žanrova koje taj proizvod sadrži korisnik ($idUser) već ima
     *
     *
     * @param  integer $idUser
     * @param  boolean $filterDLCs ako je truthy, DLC-evi se ne vraćaju u generatoru
     */
    public function matchingGenres($idUser, $filterDLCs = true) {
        $this->db = \Config\Database::connect();
        $res = $this->db->query(
            "SELECT sum(cnt) AS matching, p.*
                 FROM (
                     SELECT genre_name, count(genre_name) AS cnt
                     FROM $this->table
                     JOIN genre ON genre.id_product = ownership.id_product
                     WHERE id_user = $idUser
                     GROUP BY genre_name
                     -- ORDER BY cnt DESC
                 ) AS z
                     JOIN (
                     SELECT genre_name, product.*
                     FROM product
                     JOIN genre ON product.id = genre.id_product
                 ) AS p ON z.genre_name = p.genre_name
                 GROUP BY id; "
        );

        foreach ($res->getResult('object') as $row) {
            if ($filterDLCs && isset($row->base_game))
                continue;
            yield $row;
        }
    }

    /**
     * vraća niz gde su ključevi [id_product, rev_sum, rev_cnt] koji respektivno
     * označavaju id proizvoda, sumu njegovih ocena i koliko puta je ocenjen.
     * ove ocene su samo ocene prijatelja.
     *
     * @param  integer $idUser
     * @param  boolean $filterDLCs ako je truthy, DLC-evi se ne vraćaju u generatoru
     */
    public function friendsLikes($idUser, $filterDLCs = true) {
        $this->db = \Config\Database::connect();
        $res = $this->db->query(
            "SELECT id_product, sum(rating) AS rev_sum, count(*) AS rev_cnt

                 FROM (
                    SELECT id_user2 AS id
                    FROM relationship
                    WHERE id_user1 = $idUser

                    UNION

                    SELECT id_user1 AS id
                    FROM relationship
                    WHERE id_user2 = $idUser
                 ) AS t

                 JOIN ownership ON (ownership.id_user = t.id)
                 WHERE rating IS NOT NULL
                 GROUP BY id_product"
        );

        foreach ($res->getResult('object') as $row) {
            if ($filterDLCs && isset($row->base_game))
                continue;
            yield $row;
        }
    }
}
