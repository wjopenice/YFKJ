<?php
require_once 'ethereum.php';
$ethereum = new Ethereum('0.0.0.0', '8545');
echo "<pre>";
print_r($ethereum->eth_accounts());
echo "<hr>";
print_r($ethereum->eth_getBlockByNumber());
echo "<hr>";
$user = $ethereum->eth_accounts();
print_r($ethereum->eth_getBalance($user[0]));
echo "<hr>";
print_r($ethereum->eth_getBalance($user[1]));
echo "<hr>";
var_dump($ethereum->eth_getTransactionCount($user[0]));
echo "<hr>";
var_dump($ethereum->eth_getBlockByHash("0x796eed25625f1d879de25b5f5a543f954cd86d82594a1b6b3fd5fc01d0461539"));
exit;