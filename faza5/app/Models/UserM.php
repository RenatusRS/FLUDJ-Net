<?php

namespace App\Models;

use CodeIgniter\Model;

class UserM extends Model {
    protected $table = 'user';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $allowedFields = ['username', 'password', 'admin_rights', 'balance', 'review_ban', 'avatar', 'description', 'real_name', 'nickname', 'featured_review', 'points', 'overflow'];

    public function getAvatar($id) {
        $avatar = $this->getAsset('uploads/user/' . $id . '.png');

        return $avatar ?: base_url('assets/avatar.png');
    }

    public function getBackground($id) {
        $product_id = $this->find($id)->featured_review;

        return $product_id != null ? (new ProductM())->getBackground($product_id) : null;
    }
}
