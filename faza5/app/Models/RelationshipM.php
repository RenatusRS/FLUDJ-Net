<?php

/**
 * @author
 * Uros Loncar 2019/0691
 * Fedja Mladenovic 2019/0613
 * Luka Cvijan 2019/0154
 * 
 * Opis: Model za produkte
 * 
 * @version 1.0
 * 
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
    public function getFriends($user_id) {
        $userM = new UserM();
        $friendRows = (new RelationshipM())->where("status", 1)->groupStart()->where("id_user1", $user_id)->orWhere("id_user2", $user_id)->groupEnd()->findAll();

        $friends = [];
        foreach ($friendRows as $friendRow) {
            if ($friendRow->id_user1 == $user_id) $friend = $userM->find($friendRow->id_user2);
            else $friend = $userM->find($friendRow->id_user1);

            array_push($friends, $friend);
        }

        return $friends;
    }

    public function getFriendsWhoOwn($user_id, $product_id) {
        $friends = $this->getFriends($user_id);

        $friendsWhoOwn = [];
        $ownershipM = new OwnershipM();


        foreach ($friends as $friend) {
            if ($ownershipM->owns($friend->id, $product_id)) array_push($friendsWhoOwn, $friend);
        }

        return $friendsWhoOwn;
    }


    /**
     * 
     * Dohvatanje zahteva koji su poslati odredjenom korisniku
     * 
     * @return array(user)   
     */
    public function getIncoming($user) {
        $userM = new UserM();

        $RelationshipM = new RelationshipM();
        $requestersRows = $RelationshipM->where("status", 0)->groupStart()->where("id_user2", $user->id)->groupEnd()->findAll();

        $requesters = [];
        foreach ($requestersRows as $requestersRow) {
            $sender = $userM->find($requestersRow->id_user1);
            array_push($requesters, $sender);
        }

        return $requesters;
    }

    /**
     * 
     * Dohvatanje zahteva koji je poslao odredjeni korisnik
     * 
     * @return array(user)   
     */
    public function getSent($user) {
        $userM = new UserM();

        $RelationshipM = new RelationshipM();
        $requestedToRows = $RelationshipM->where("status", 0)->groupStart()->where("id_user1", $user->id)->groupEnd()->findAll();

        $requestedTo = [];
        foreach ($requestedToRows as $requestToRow) {
            $recipient = $userM->find($requestToRow->id_user2);
            array_push($requestedTo, $recipient);
        }

        return $requestedTo;
    }
}
