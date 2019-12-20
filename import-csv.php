<?php

use Doctrine\DBAL\Schema\Table;
use League\Csv\Reader;
use League\Csv\Statement;
use Doctrine\DBAL\DriverManager;

require_once __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/config.php';

$csvPath = $argv[1];
$tableName = $argv[2];
$increment = $argv[3];
$delimiter = $argv[4] ?? ',';

$csv = Reader::createFromPath($csvPath, 'r');
$csv->setDelimiter($delimiter);
$header = $csv->fetchOne(0);

$dbalConfig = new \Doctrine\DBAL\Configuration();
$connectionParams = $config['dbal-config'];
$conn = DriverManager::getConnection($connectionParams, $dbalConfig);

$table = new Table($tableName);
foreach($header as $item) {
    $table->addColumn($item, 'string', ['length' => 200]);
}

echo "Criando tabela {$table->getName()}\n";
$conn->getSchemaManager()->dropAndCreateTable($table);

$offset = 1; //elimina a linha inicial de título

$stmt = (new Statement())
    ->limit($increment)
    ->offset($offset);

PHP_Timer::start();

$totalProcess = 0;
while (true) {
    $end = $offset+$increment;
    echo "Tentando inserir registros de {$offset} até {$end}\n";
    $records = $stmt->process($csv);
    $return = insertBulk($tableName, $header, $records, $conn);

    echo "Inseridos {$return} registros\n";
    if($return === false || $return < $increment) {
        break;
    }

    $offset = $end;
    $stmt = $stmt->offset($offset);

}

echo "Fim\n";

function insertBulk($tableName, $header, $lines, \Doctrine\DBAL\Connection $connection)
{
    $linesToInsert = [];
    foreach ($lines as $line) {
        $_line = prepareArrayToSql($line);
        $linesToInsert[] = '(' . implode(',', $_line) . ')';
    }

    if(count($linesToInsert) === 0) {
        return false;
    }

    $linesSql = implode(', ', $linesToInsert);
    $headerSql = implode(', ', prepareArrayToSql($header, "`"));

    $sql = "INSERT INTO `{$tableName}` ({$headerSql}) VALUES {$linesSql}";
    return $connection->exec($sql);
}

function prepareArrayToSql($array, $char = "'")
{
    $_array = [];
    foreach ($array as $item) {
        $item = trim($item);
        $item = addslashes($item);
        $_array[] = "{$char}{$item}{$char}";
    }

    return $_array;
}

