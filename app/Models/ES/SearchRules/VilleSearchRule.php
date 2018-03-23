<?php

namespace App\Models\ES\SearchRules;

use ScoutElastic\SearchRule;

class VilleSearchRule extends SearchRule
{
    public function buildQueryPayload()
    {
        return [
            'should' => [
                'match' => ['
                    cms.villes' => $this->builder->query,
                ],
            ],
        ];
    }
}
