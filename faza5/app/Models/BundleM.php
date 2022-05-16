<?php

namespace App\Models;

use CodeIgniter\Model;

class BundleM extends Model {

    protected $table = 'bundle';
    protected $primaryKey = 'id_product';

    protected $returnType = 'object';

    protected $allowedFields = ['name', 'discount', 'description'];

}

