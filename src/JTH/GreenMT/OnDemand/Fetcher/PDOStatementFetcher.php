<?php

namespace JTH\GreenMT\OnDemand\Fetcher;

use JTH\GreenMT\OnDemand\Fetchable;
use PDOStatement;

class PDOStatementFetcher implements Fetchable {

    protected $stmt;

    public function __construct(PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    public function fetch()
    {
        return $this->stmt->fetch();
    }
}
