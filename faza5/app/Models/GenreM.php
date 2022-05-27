<?php

namespace App\Models;

use CodeIgniter\Model;

class GenreM extends Model {
    protected $table = 'genre';
    protected $primaryKey = 'id_product';

    protected $returnType = 'object';

    protected $allowedFields = ['id_product', 'genre_name'];

    public $db;

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
     */
    public function getSimilarProducts($productId) {
        $this->db = \Config\Database::connect();
        $res = $this->db->query(
            "SELECT t1.id_product, count(*) as match_count
             FROM (SELECT id_product, genre_name FROM $this->table WHERE id_product <> $productId) AS t1
             JOIN (SELECT genre_name             FROM $this->table WHERE id_product =  $productId) AS t2 ON t1.genre_name = t2.genre_name
             GROUP BY t1.id_product;"
        );

        foreach ($res->getResult('object') as $row) {
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
    private function compositeExists($productId, $genreName) {
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

        $this->db->query("INSERT INTO $this->table
                          (id_product, genre_name) VALUES
                          ('$productId', '$genreName')"
        );

        return true;
    }
}
