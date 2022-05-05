<?php

namespace App\Models;

use CodeIgniter\Model;

class OwnershipM extends Model {
    protected $table = 'ownership';
    protected $primaryKey = 'id_product';

    protected $returnType = 'object';

    protected $allowedFields = ['id_product', 'id_user', 'text', 'rating'];
}
