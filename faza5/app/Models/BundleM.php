<?php

namespace App\Models;

use CodeIgniter\Model;

class BundleM extends Model {

    protected $table = 'bundle';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $allowedFields = ['name', 'discount', 'description'];

    public $db;

    protected $validationRules = [
        'name' => [
            'rules'  => 'required|alpha_numeric_space|is_unique[bundle.name]',
            'errors' => [
                'is_unique' => 'Name of bundle already exists in database.'
            ]
        ]
    ];
}

