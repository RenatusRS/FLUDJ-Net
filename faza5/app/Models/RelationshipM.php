<?php

namespace App\Models;

use CodeIgniter\Model;

class RelationshipM extends Model {
    protected $table = 'relationship';
    protected $primaryKey = 'id_user1';

    protected $returnType = 'object';

    protected $allowedFields = ['id_user1', 'id_user2', 'status'];
}
