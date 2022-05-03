<?php

namespace App\Models;

use CodeIgniter\Model;

class GenreM extends Model {
    protected $table = 'genre';
    protected $primaryKey = 'id_product';

    protected $returnType = 'object';

    protected $allowedFields = ['genre_name'];
}
