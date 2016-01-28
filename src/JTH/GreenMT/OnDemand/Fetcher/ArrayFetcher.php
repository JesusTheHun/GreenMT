<?php

namespace JTH\GreenMT\OnDemand\Fetcher;

use JTH\GreenMT\OnDemand\Fetchable;

class ArrayFetcher implements Fetchable {

    protected $arr;

    public function __construct(array $arr)
    {
        $this->arr = $arr;
    }

    public function fetch()
    {
        $r = current($this->arr);
        next($this->arr);
        return $r;
    }
}