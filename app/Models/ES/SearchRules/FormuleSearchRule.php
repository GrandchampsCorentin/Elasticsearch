<?php

namespace App\Models\ES\SearchRules;

use ScoutElastic\SearchRule;

class FormuleSearchRule extends SearchRule
{
    public function buildQueryPayload()
    {
        return [
            'should' => [
                'match' => [
                    'cms.formule' => $this->builder->query,
                ],
            ],
        ];
    }
}
