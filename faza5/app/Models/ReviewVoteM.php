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

    public function getVotes($idProduct, $idPoster) {
        $this->db = \Config\Database::connect();

        $res = $this->db->query("SELECT * from (
            SELECT COUNT(*) AS pos
            FROM review_vote
            WHERE id_poster = $idPoster AND id_product = $idProduct AND `like` = 1
            GROUP BY `like`
          ) as t1 join (
            SELECT COUNT(*) AS neg
            FROM review_vote
            WHERE id_poster = $idPoster AND id_product = $idProduct AND `like` = 0
            GROUP BY `like`
          ) as t2");

        return $res->getResult('object');
    }
}
