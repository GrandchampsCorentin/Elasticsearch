<?php

namespace App\Models\ES\SearchRules;

use ScoutElastic\SearchRule;

class LangueSearchRule extends SearchRule
{
    public function buildQueryPayload()
    {
        return [
            'should' => [
                'match' => [
                    'cms.langue' => [$this->builder->query,
                    'operator' => 'and', ],
                ],
            ],
        ];
    }
}
