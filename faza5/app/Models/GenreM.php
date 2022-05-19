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
