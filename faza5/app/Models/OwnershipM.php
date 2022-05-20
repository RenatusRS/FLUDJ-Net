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

}
