<?php

/**
 * @author
 * Fedja Mladenovic 2019/0613
 * 
 * Opis: Model za zanrove
 * 
 * @version 1.0
 * 
 */

namespace App\Models;

use CodeIgniter\Model;

class GenreM extends Model {
    protected $table = 'genre';
    protected $primaryKey = 'id_product';

    protected $returnType = 'object';

    protected $allowedFields = ['id_product', 'genre_name'];

    public $db;

    /**
     * dohvata niz žanrova
     *
     * @param  integer $productId
     * @return string[]
     */
    public function getGenres($productId) {
        $rows = $this->where($this->primaryKey, $productId)
            ->findAll();

        $genres = [];
        foreach ($rows as $row) {
            array_push($genres, $row->genre_name);
        }

        return $genres;
    }
    /**
     * vraća generator sortiranog niza uređenih parova [id_product, match_count] gde id_product označava
     * id nekog proizvoda a match_count (po kome je niz sortiran opadajuće) označava koliko
     * ima istih žanrova sa proizvodom za koji se traže slični proizvodi.
     *
     * @param  integer $productId
     * @param  boolean $filterDLCs ako je truthy, DLC-evi se ne vraćaju u generatoru
     */
    public function getSimilarProducts($productId, $filterDLCs = true) {
        $this->db = \Config\Database::connect();
        $res = $this->db->query(
            "SELECT t.match_count, product.*
             FROM
             (
                SELECT t1.id_product, count(*) as match_count
                FROM (SELECT id_product, genre_name FROM genre WHERE id_product <> $productId) AS t1
                JOIN (SELECT genre_name             FROM genre WHERE id_product =  $productId) AS t2 ON t1.genre_name = t2.genre_name
                GROUP BY t1.id_product
             ) AS t
             JOIN product ON t.id_product = id;"
        );

        foreach ($res->getResult('object') as $row) {
            if ($filterDLCs && isset($row->base_game))
                continue;
            yield $row;
        }
    }

    /**
     * compositeExists checks if composite key of productId-genreName exists
     * in genre table
     *
     * @param  int $productId
     * @param  string $genreName
     * @return boolean
     */
    public function compositeExists($productId, $genreName) {
        $this->db = \Config\Database::connect();

        $builder = $this->db->table($this->table);
        $result = $builder->select('id_product')
            ->where('id_product', $productId)
            ->where('genre_name', $genreName)
            ->countAllResults();

        return ($result > 0);
    }


    /**
     * insertComposite inserts composite key productId-genreName
     * to genres table if such entry does not yet exist
     *
     * @param  int $productId
     * @param  string $genreName
     * @return boolean ret value is true if insertion is successful
     */
    public function insertComposite($productId, $genreName) {
        $this->db = \Config\Database::connect();

        if ($this->compositeExists($productId, $genreName))
            return false;

        $this->db->query(
            "INSERT INTO $this->table
                          (id_product, genre_name) VALUES
                          ('$productId', '$genreName')"
        );

        return true;
    }
}
