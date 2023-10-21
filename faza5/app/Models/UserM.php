<?php

/**
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

    /**
     * dohvata putanju do profilne slike za korisnika sa id-jem $id
     *
     * @param  integer $id
     * @return string path
     */
    public function getAvatar($id) {
        $avatar = $this->getAsset('uploads/user/' . $id . '.jpg');

        return $avatar ?: base_url('assets/avatar.png');
    }

    /**
     * dohvata putanju do pozadine za korisnika sa id-jem $id na osnovu featured recenzije
     *
     * @param  integer $id
     * @return string|null path
     */
    public function getBackground($id) {
        $user = $this->find($id);
        if (!isset($user))
            return null;
        $productId = $user->featured_review;

        return ($productId != null) ?
            (new ProductM())->getBackground($productId) :
            null;
    }

    /**
     * banuje korisnika sa id-jem $idUser ako je $val = 1, u suprotnom ga odbanuje
     *
     * @param  integer $idUser
     * @param  integer $val
     * @return void
     */
    private static function setBan($idUser, $val) {
        (new UserM())->update($idUser, [
            'review_ban' => $val
        ]);
    }
    /**
     * omogućava administratorske privilegije korisnika sa id-jem $idUser ako je $val = 1, u suprotnom mu ih oduzima
     *
     * @param  integer $idUser
     * @param  integer $val
     * @return void
     */
    private static function setPrivilege($idUser, $admin) {
        (new UserM())->update($idUser, [
            'admin_rights' => $admin
        ]);
    }

    /**
     * korisnik sa id-jem $idUser dobija zabranu pisanja.
     *
     * @param  integer $idUser
     * @return void
     */
    public static function banUser($idUser) {
        self::setBan($idUser, 1);
    }

    /**
     * korisnik sa id-jem $idUser gubi zabranu pisanja.
     *
     * @param  integer $idUser
     * @return void
     */
    public static function unbanUser($idUser) {
        self::setBan($idUser, 0);
    }

    /**
     * korisnik sa id-jem $idUser dobija administratorske privilegije
     *
     * @param  integer $idUser
     * @return void
     */
    public static function promoteUser($idUser) {
        self::setPrivilege($idUser, 1);
    }

    /**
     * korisnik sa id-jem $idUser gubi administratorske privilegije
     *
     * @param  integer $idUser
     * @return void
     */
    public static function demoteUser($idUser) {
        self::setPrivilege($idUser, 0);
    }
}
