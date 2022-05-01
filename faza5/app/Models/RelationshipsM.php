<?php

namespace App\Models;

use CodeIgniter\Model;

class RelationshipsM extends Model {
    protected $table = 'relationships';
    protected $primaryKey = 'id_user1';

    protected $returnType = 'object';

    protected $allowedFields = ['id_user1', 'id_user2', 'status'];
}
