<?php

namespace App\Models;

use CodeIgniter\Model;

class BundleM extends Model {

    protected $table = 'bundle';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $allowedFields = ['name', 'discount', 'description'];

    public $db;

    public function nameAlreadyExists($name) {
        $this->db = \Config\Database::connect();
        $builder = $this->db->table($this->table);
        $result = $builder->select('id')->where('name', $name)->countAllResults();

        return ($result > 0);
    }

}

