<?php

/**
 * @author
 * Djorđe Stanojević 2019/0288
 * Luka Cvijan       2019/0154
 *
 * Opis: Model za veze izmedju korisnika
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
     * Dohvatanje svih prijatelja odredjenog korisnika
     *
     * @param  integer $userId
     * @return object[] niz objekta korisnika
     */
    public function getFriends($userId) {
        $userM = new UserM();
        $friendRows = (new RelationshipM())
                ->where("status", 1)
                ->groupStart()
                ->where("id_user1", $userId)
                ->orWhere("id_user2", $userId)
                ->groupEnd()
                ->findAll();

        $friends = [];
        foreach ($friendRows as $friendRow) {
            array_push($friends, ($friendRow->id_user1 == $userId) ?
                $userM->find($friendRow->id_user2) :
                $userM->find($friendRow->id_user1));
        }

        return $friends;
    }

    /**
     * dohvati sve prijatelje korisnika sa id-jem $idUser koji poseduju proizvod sa id-jem $idProduct
     *
     * @param  integer $idUser
     * @param  integer $idProduct
     * @return object[] niz objekata korisnika
     */
    public function getFriendsWhoOwn($idUser, $idProduct) {
        $friends = $this->getFriends($idUser);

        $friendsWhoOwn = [];
        $ownershipM = new OwnershipM();

        foreach ($friends as $friend) {
            if ($ownershipM->owns($friend->id, $idProduct)) {
                array_push($friendsWhoOwn, $friend);
            }
        }

        return $friendsWhoOwn;
    }


    /**
     *
     * Dohvatanje zahteva koji su poslati odredjenom korisniku $user
     *
     * @param  object $user objekat koji predstavlja korisnika
     * @return object[] niz objekta korisnika
     */
    public function getIncoming($user) {
        if (!isset($user))
            return [];

        $userM = new UserM();

        $RelationshipM = new RelationshipM();
        $requestersRows = $RelationshipM
                ->where("status", 0)
                ->groupStart()
                ->where("id_user2", $user->id)
                ->groupEnd()
                ->findAll();

        $requesters = [];
        foreach ($requestersRows as $requestersRow) {
            $sender = $userM->find($requestersRow->id_user1);
            array_push($requesters, $sender);
        }

        return $requesters;
    }

    /**
     *
     * dohvatanje svih korisnika kojima je trenutni korisnik poslao zahtev
     *
     * @param  object $user korisnik koji traži svoje zahteve
     * @return object[] niz korisnika kojima je poslat zahtev
     */
    public function getSent($user) {
        if (!isset($user))
            return [];

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

    /**
     * Dohvatanje statusa izmedju dva korisnika
     * 
     * @return int
     */
    public function getStatus($userId1, $userId2) {
        if ($userId1 == null || $userId2 == null) return -1;

        $stateLR = $this->where("id_user1", $userId1)->where("id_user2", $userId2)->first();
        $stateRL = $this->where("id_user2", $userId1)->where("id_user1", $userId2)->first();

        if (!isset($stateLR) && !isset($stateRL)) return -1;

        if (isset($stateLR)) return $stateLR->status;

        return $stateRL->status == 0 ? 2 : 1;
    }
}
