<?php

declare(strict_types=1);

namespace Sigmie\Query;

use Sigmie\Base\APIs\Script as APIsScript;
use Sigmie\Base\APIs\Search as APIsSearch;
use Sigmie\Base\Http\Responses\Search as SearchResponse;
use Sigmie\Query\Contracts\Aggs as AggsInterface;
use Sigmie\Query\Contracts\QueryClause as Query;
use Sigmie\Query\Queries\MatchAll;

class Search
{
    use APIsSearch;
    use APIsScript;

    protected string $index;

    protected int|string $from = 0;

    protected int|string $size = 500;

    protected array $fields = ['*'];

    protected array $raw = [];

    protected array $sort = [];

    protected array $highlight = [];

    public function __construct(
        protected Query $query = new MatchAll(),
        protected AggsInterface $aggs = new Aggs()
    ) {
    }

    public function aggregate(callable $callable)
    {
        $callable($this->aggs);

        return $this;
    }

    public function fields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function from(string|int $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function size(int|string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function index(string $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function sort(string $field, string $direction = null): self
    {
        if ($field === '_score') {
            $this->sort[] = $field;

            return $this;
        }

        $this->sort[] = [$field => $direction];

        return $this;
    }

    public function addRaw(string $key, mixed $value)
    {
        $this->raw[$key] = $value;

        return $this;
    }

    public function highlight(string $field, string $preTag, string $postTag)
    {
        $this->highlight[$field] = [
            'type' => 'plain',
            'force_source' => true,
            'pre_tags' => [$preTag],
            'post_tags' => [$postTag],
            'fragment_size' => 150,
            'number_of_fragments' => 3,
            'no_match_size' => 150,
        ];
    }

    public function response()
    {
        $raw = $this->toRaw();

        return $this->searchAPICall($this->index, $raw);
    }

    public function get(): SearchResponse
    {
        $raw = $this->getDSL();

        ray($raw);

        return $this->searchAPICall($this->index, $raw);
    }

    public function getDSL(): array
    {
        return $this->toRaw();
    }

    public function query(Query $query)
    {
        $this->query = $query;

        return $this;
    }

    public function toRaw(): array
    {
        $result = [
            '_source' => $this->fields,
            'query' => $this->query->toRaw(),
            'from' => $this->from,
            'size' => $this->size,
            'sort' => [...$this->sort],
            'highlight' => [
                // 'require_field_match' => false,
                'force_source' => true,
                'no_match_size' => 100,
                'fields' => [
                    ...$this->highlight,
                ],
            ],
            ...$this->raw,
        ];

        if (count($this->aggs->toRaw()) > 0) {
            $result['aggs'] = $this->aggs->toRaw();
        }

        return $result;
    }
}
