<?php

declare(strict_types=1);

namespace Sigmie\Query\Aggregations\Bucket;

class SignificantText extends Bucket
{
    public function __construct(
        protected string $name,
        protected string $field,
    ) {
    }

    public function value(): array
    {
        return [
            'significant_text' => [
                'field' => $this->field,
            ],
        ];
    }
}
