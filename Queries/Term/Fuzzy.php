<?php

declare(strict_types=1);

namespace Sigmie\Query\Queries\Term;

use Sigmie\Query\Queries\Query;

class Fuzzy extends Query
{
    public function __construct(
        protected string $field,
        protected string $value,
    ) {
    }

    public function toRaw(): array
    {
        return [
            'fuzzy' => [
                $this->field => [
                    'value' => $this->value,
                    // "fuzziness" => "AUTO",
                    // "max_expansions" => 50,
                    // "prefix_length" => 0,
                    // "transpositions" => true,
                    // "rewrite" => "constant_score"
                ],
            ],
        ];
    }
}
