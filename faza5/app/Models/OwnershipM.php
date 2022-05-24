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
     */
    public function ownedSum($limit = 1000000, $offset = 0) { // limit koristi magičan broj 1000000 jer nema oznaka za beskonačno
        $this->db = \Config\Database::connect();
        $res = $this->db->query("SELECT *, count(*) as cnt
                                 FROM $this->table
                                 GROUP BY id_product
                                 ORDER BY cnt DESC
                                 LIMIT $limit, $offset; ");

        foreach ($res->getResult('array') as $row) {
            yield $row;
        }
    }
}
