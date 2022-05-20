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
}
