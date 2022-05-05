<?php
/*
Autori:
	Djordje Stanojevic 2019/0288
	
Opis: Model za vezu (prijateljski zahtev) izmedju korisnika 


@version 1.3
@return object RelationshipM

*/
namespace App\Models;

use CodeIgniter\Model;

class RelationshipM extends Model {
    protected $table = 'relationship';
    protected $primaryKey = 'id_user1';

    protected $returnType = 'object';

    /*
    id_user1 je korisnik koji je inicirao zahtev - posiljalac
    id_user2 je korisnik koji prima zahtev - primalac
    status==0 zahtev nije prihvacen
    status==1 zahtev je prihvacen - dva korisnika su prijatelji
    */
    protected $allowedFields = ['id_user1', 'id_user2', 'status'];

    /**
     * 
     * Dohvatanje prijatelja odredjenog korisnika
     * 
     * @return array(user)   
     */
    public function getFriends($user) {
        $userM = new UserM();

        $RelationshipM = new RelationshipM();
        $friendRows = $RelationshipM->where("status", 1)->groupStart()->where("id_user1", $user->id)->orWhere("id_user2", $user->id)->groupEnd()->findAll();
        $friends = [];
        foreach ($friendRows as $friendRow) {
            if ($friendRow->id_user1 == $user->id) {
                $friend = $userM->find($friendRow->id_user2);
            } else {
                $friend = $userM->find($friendRow->id_user1);
            }
            array_push($friends, $friend);
        }
        return $friends;
    }
}
