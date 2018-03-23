<?php

namespace App\Models\ES\SearchRules;

use ScoutElastic\SearchRule;

class HebergementSearchRule extends SearchRule
{
    public function buildQueryPayload()
    {
        return [
            'should' => [
                'match' => [
                    'cms.hebergements' => $this->builder->query,
                ],
            ],
        ];
    }
}
