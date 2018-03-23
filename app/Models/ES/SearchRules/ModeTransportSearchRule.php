<?php

namespace App\Models\ES\SearchRules;

use ScoutElastic\SearchRule;

class ModeTransportSearchRule extends SearchRule
{
    public function buildQueryPayload()
    {
        return [
            'should' => [
                'match' => [
                    'cms.modes_transports' => $this->builder->query,
                ],
            ],
        ];
    }
}
