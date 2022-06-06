<?php

/**
 * @author
 * 	Uros Loncar 2019/0691
 *  Fedja Mladenovic 2019/0613
 * 
 * Opis: Model za korisnike
 * 
 * @version 1.3
 * 
 */

namespace App\Models;

use CodeIgniter\Model;

class UserM extends Model {
    protected $table = 'user';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $allowedFields = ['username', 'password', 'admin_rights', 'balance', 'review_ban', 'avatar', 'description', 'real_name', 'nickname', 'featured_review', 'points', 'overflow'];

    protected $validationRules = [
        'username' => 'required|is_unique[user.username]'
    ];
    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Username already exists.'
        ]
    ];

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

    public static function banUser($idUser) {
        self::setBan($idUser, 1);
    }

    public static function unbanUser($idUser) {
        self::setBan($idUser, 0);
    }

    public static function promoteUser($idUser) {
        self::setPrivilege($idUser, 1);
    }

    public static function demoteUser($idUser) {
        self::setPrivilege($idUser, 0);
    }
}
