<?php
/**
 * OnDemand classes and interfaces are designed to traverse a very big set of data and dispatch the treatment of these
 * data, packet by packet. In this example we use unbuffered query, so the whole data set is not received in php's
 * memory. Internally, we wait for a Work to ask data, then we fetch a packet of rowset and send it to the Work.
 * When he's done it with this rowset it asks again. Once the PDOStatement is fully traversed, we close the "pipe",
 * which is a simple on/off switch to inform Works they should not wait data anymore.
 * When Works are done with row set they've already received, the dispatch method ends.
 */

use JTH\GreenMT\OnDemand\Fetcher\PDOStatementFetcher;
use JTH\GreenMT\OnDemand\Pool;
use JTH\GreenMT\OnDemand\WorkAbstract;

require_once '../vendor/autoload.php';

$pdo = new PDO("mysql:host=localhost", 'root', '', array(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
$stmt = $pdo->query("SELECT * FROM very_large_table");

$op = new Pool(2);
$op->submit(new class extends WorkAbstract {
    public function processPacket(array $packet)
    {
        echo sizeof($packet);
        echo PHP_EOL;
    }
});

$op->dispatch(new PDOStatementFetcher($stmt), 10);
while($op->collect()) continue;
$op->shutdown();