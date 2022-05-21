<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewVoteM extends Model {
    protected $table = 'review_vote';
    protected $primaryKey = 'id_user';

    protected $returnType = 'object';

    protected $allowedFields = ['id_user','id_poster','id_product','like'];
}
