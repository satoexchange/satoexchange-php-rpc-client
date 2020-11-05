<?php
require_once './SatoClient.php';

$client = new SatoClient('...', '...');

//Example 1 get balances
$balances = $client->balances();
var_dump($balances);

//Example 2 buy doge with btc
$response = $client->buy(['market' => 'DOGE/BTC', 'price' => 0.00000027, 'amount' => 20234]);
var_dump($response);

//Example 2 sell doge with btc
$response = $client->sell(['market' => 'DOGE/BTC', 'price' => 0.00000030, 'amount' => 10000]);
var_dump($response);

// and so on...
