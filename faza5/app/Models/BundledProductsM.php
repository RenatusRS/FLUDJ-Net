<?php

namespace App\Models;

use CodeIgniter\Model;

class BundledProductsM extends Model {

    protected $table = 'bundled';
    protected $primaryKey = 'id_bundle';

    protected $returnType = 'object';

    protected $allowedFields = ['id_bundle', 'id_product'];

    public $db;

    /**
     * find products from bundle with id $id
     *
     * @param  integer $id
     * @return array array of id-s of found products
     */
    public function findBundledProducts($id) { // TODO ovo je isti kod kao traženje žanrova. napraviti u nadklasi apstrakciju ova dva
        $this->db = \Config\Database::connect();

        $query = $this->db->query("SELECT id_product
                                   FROM   $this->table
                                   WHERE  $this->primaryKey = $id ;"
        );

        $result = [];
        foreach ($query->getResult() as $row) {
            array_push($result, $row->id_product);
        }

        return $result;
    }
}
