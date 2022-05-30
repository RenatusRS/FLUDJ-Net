<?php

namespace App\Models;

use CodeIgniter\Model;

class UserM extends Model {
    protected $table = 'user';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $allowedFields = ['username', 'password', 'admin_rights', 'balance', 'review_ban', 'avatar', 'description', 'real_name', 'nickname', 'featured_review', 'points', 'overflow'];

    public function getAvatar($id) {
        $avatar = $this->getAsset('uploads/user/' . $id . '.jpg');

        return $avatar ?: base_url('assets/avatar.png');
    }

    public function getBackground($id) {
        $product_id = $this->find($id)->featured_review;

        return $product_id != null ? (new ProductM())->getBackground($product_id) : null;
    }

    private static function setBan($idUser, $val) {
        (new UserM())->update($idUser, [
            'review_ban' => $val
        ]);
    }
    private static function setPrivilege($idUser, $admin) {
        (new UserM())->update($idUser, [
            'admin_rights' => $admin
        ]);
    }
    public static function banUser($idUser) { self::setBan($idUser, 1); }
    public static function unbanUser($idUser) { self::setBan($idUser, 0); }
    public static function promoteUser($idUser) { self::setPrivilege($idUser, 1); }
    public static function demoteUser($idUser) { self::setPrivilege($idUser, 0); }
}
