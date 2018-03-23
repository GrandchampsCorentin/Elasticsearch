<?php

namespace App\Models\ES\SearchRules;

use ScoutElastic\SearchRule;

class CategorieSearchRule extends SearchRule
{
    public function buildQueryPayload()
    {
        return [
            'should' => [
                'match' => [
                    'cms.categorie' => $this->builder->query,
                ],
            ],
        ];
    }
}
