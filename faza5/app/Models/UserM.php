<?php

namespace App\Models;

use CodeIgniter\Model;

class UserM extends Model {
    protected $table = 'user';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $allowedFields = ['username', 'password', 'admin_rights', 'balance', 'review_ban', 'avatar', 'description', 'real_name', 'nickname', 'featured_review', 'points', 'overflow'];
}
