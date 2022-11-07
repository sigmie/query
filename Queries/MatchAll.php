<?php

declare(strict_types=1);

namespace Sigmie\Query\Queries;

class MatchAll extends Query
{
    public function toRaw(): array
    {
        return [
            'match_all' => (object) [
                'boost' => $this->boost,
            ],
        ];
    }
}
