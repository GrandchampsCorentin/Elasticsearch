<?php

namespace App\Models\ES\SearchRules;

use ScoutElastic\SearchRule;

class PaysSearchRule extends SearchRule
{
    public function buildQueryPayload()
    {
        return [
            'should' => [
                'match' => [
                    'cms.pays' => $this->builder->query,
                ],
            ],
        ];
    }
}
