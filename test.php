<?php
/**
 * Author: jmassuch
 * Date: 22/01/2016
 */

use JTH\GreenMT\OnDemand\Fetcher\PDOStatementFetcher;

require_once 'vendor/autoload.php';

$pdo = new PDO("mysql:dbname=database_reference;host=localhost", 'master', 'jJbANhxfjh2h62nj', array(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
$stmt = $pdo->query("SELECT * FROM database_reference.main_quadruplet_searched LIMIT 1000");

$op = new \JTH\GreenMT\OnDemand\Pool(2);
$op->submit(new class extends \JTH\GreenMT\OnDemand\WorkAbstract {
    public function processPacket(array $packet)
    {
        print_r($packet);
    }
});

$op->dispatch(new PDOStatementFetcher($stmt), 10);
while($op->collect()) continue;
$op->shutdown();