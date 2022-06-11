<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Config\Factories;

use App\Models\GenreM;

class GenreTest extends CIUnitTestCase {
    use ControllerTestTrait;
    use DatabaseTestTrait;

    // protected $seed = 'Tests\Support\Database\Seeds\GenreSeeder';
    private $model;

    public function testModelFindAll() {
        $this->model = new GenreM();

        $this->assertGetGenres();
        $this->assertGetSimilarProducts();
        $this->assertCompositeExists();
        $this->assertInsertComposite();
    }

    private function assertGetGenres() {
        $model = $this->model;

        $g1 = $model->getGenres(1);
        $g2 = $model->getGenres(2);
        $g3 = $model->getGenres(-1);

        $this->assertIsArray($g1);
        $this->assertCount(4, $g1);
        $this->assertTrue(in_array("Action", $g1));
        $this->assertTrue(in_array("Adventure", $g1));
        $this->assertTrue(in_array("Multiplayer", $g1));
        $this->assertTrue(in_array("RPG", $g1));

        $this->assertIsArray($g2);
        $this->assertCount(7, $g2);
        $this->assertTrue(in_array("Anime", $g2));
        $this->assertTrue(in_array("Adventure", $g2));
        $this->assertTrue(in_array("JRPG", $g2));
        $this->assertTrue(in_array("Mystery", $g2));
        $this->assertTrue(in_array("Novel", $g2));
        $this->assertTrue(in_array("Story", $g2));
        $this->assertTrue(in_array("Turn-Based", $g2));

        $this->assertIsArray($g3);
        $this->assertCount(0, $g3);
    }
    private function assertGetSimilarProducts() {
        $model = $this->model;

        // TODO za to da li su ispravni produkti similar
        $p1 = $model->getSimilarProducts(1, true);
        $p1noDLC = $model->getSimilarProducts(1, false);
        $p2 = $model->getSimilarProducts(2, true);
        $p2noDLC = $model->getSimilarProducts(2, false);
        $p3 = $model->getSimilarProducts(-1, true);
        $p3noDLC = $model->getSimilarProducts(-1, false);

        $this->assertSimilar($p1);
        $this->assertSimilar($p1noDLC);
        $this->assertSimilar($p2);
        $this->assertSimilar($p2noDLC);
        $this->assertSimilar($p3);
        $this->assertSimilar($p3noDLC);
    }
    private function assertCompositeExists() {
        $model = $this->model;

        $exists = $model->compositeExists(1, "Action");
        $exists2 = $model->compositeExists(1, "Adventure");
        $doesntExist = $model->compositeExists(1, "ActionX");
        $doesntExist2 = $model->compositeExists(-1, "ActionX");

        $this->assertTrue($exists);
        $this->assertTrue($exists2);
        $this->assertFalse($doesntExist);
        $this->assertFalse($doesntExist2);
    }
    private function assertInsertComposite() {
        $model = $this->model;

        $this->assertFalse($model->insertComposite(1, "Action"));
        $this->assertTrue($model->insertComposite(1, "Masterpiece"));

        $this->assertTrue($model->compositeExists(1, "Masterpiece"));

        $model->where('id_product', 1)
              ->where('genre_name', "Masterpiece")
              ->delete();

        $this->assertFalse($model->compositeExists(1, "Masterpiece"));
    }

    // pomoćne
    private function assertSimilar($products) {
        $this->assertIsIterable($products);
        $products = iterator_to_array($products);
        foreach ($products as $p) {
            $this->assertIsObject($p);
            $this->assertObjectHasAttribute('match_count', $p);
        }
    }
}
