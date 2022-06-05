<?php

/**
 * @author
 * Luka Cvijan 2019/0154
 * 
 * Opis: Model za glasanje na recenzijama
 * 
 * @version 1.0
 * 
 */

namespace App\Models;

use CodeIgniter\Model;

class ReviewVoteM extends Model {
    protected $table = 'review_vote';
    protected $primaryKey = 'id_user';

    protected $returnType = 'object';

    protected $allowedFields = ['id_user', 'id_poster', 'id_product', 'like'];
}
