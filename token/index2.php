<?php
require_once 'vendor/autoload.php';
//1. 项目依赖
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\Random\Random;
use BitWasp\Bitcoin\Key\Factory\HierarchicalKeyFactory;
use BitWasp\Bitcoin\Mnemonic\Bip39\Bip39Mnemonic;
use BitWasp\Bitcoin\Mnemonic\Bip39\Bip39SeedGenerator;
use BitWasp\Bitcoin\Mnemonic\MnemonicFactory;
use Web3p\EthereumUtil\Util;
//2. 创建助记词
// Bip39
$math = Bitcoin::getMath();
$network = Bitcoin::getNetwork();
$random = new Random();
// 生成随机数(initial entropy)
$entropy = $random->bytes(Bip39Mnemonic::MIN_ENTROPY_BYTE_LEN);
$bip39 = MnemonicFactory::bip39();
// 通过随机数生成助记词
$mnemonic = $bip39->entropyToMnemonic($entropy);
$data = [];
$data['walletKey']['xcoinpayCode'] = $mnemonic;
//3. 助记词产生主私钥和主公钥
$seedGenerator = new Bip39SeedGenerator();
// 通过助记词生成种子，传入可选加密串'xcoinpay'
$seed = $seedGenerator->getSeed($mnemonic, 'xcoinpay');
$data['walletKey']['seed'] = $seed->getHex();
$hdFactory = new HierarchicalKeyFactory();
$master = $hdFactory->fromEntropy($seed);
// 主私钥
$data['walletKey']['mainPrivateKey'] = $master->getPrivateKey()->getHex();
// 主公钥
$data['walletKey']['mainPublicKey'] = $master->getPublicKey()->getHex();

//创建一组RSA公钥私钥
$res = openssl_pkey_new();
openssl_pkey_export($res, $privkey);
$d= openssl_pkey_get_details($res);
$pubkey = $d['key'];
$data['walletKey']['rsaPrivateKey'] = $privkey;
$data['walletKey']['rsaPubKey'] = $pubkey;
//4. 批量生成主私钥生成子私钥、子公钥和地址
$count = 5; // 生成以太坊账户数量
$util = new Util();
//生成BTC冷钱包
$hardened = $master->derivePath("44X/0X/0X/0/0");
$data['walletKey']['btcColdKeyObj']['path'] = "M/44X/0X/0X/0/0";
$data['walletKey']['btcColdKeyObj']['publicKey'] = $hardened->getPublicKey()->getHex();
$data['walletKey']['btcColdKeyObj']['privateKey'] = $hardened->getPrivateKey()->getHex();
$data['walletKey']['btcColdKeyObj']['address'] = $util->publicKeyToAddress($util->privateKeyToPublicKey($hardened->getPrivateKey()->getHex()));
//生成BTC热钱包
$hardened = $master->derivePath("44X/0X/1X/0/0");
$data['walletKey']['btcHotKeyObj']['path'] = "M/44X/0X/1X/0/0";
$data['walletKey']['btcHotKeyObj']['publicKey'] = $hardened->getPublicKey()->getHex();
$data['walletKey']['btcHotKeyObj']['privateKey'] = $hardened->getPrivateKey()->getHex();
$data['walletKey']['btcHotKeyObj']['address'] = $util->publicKeyToAddress($util->privateKeyToPublicKey($hardened->getPrivateKey()->getHex()));
//生成ETH冷钱包
$hardened = $master->derivePath("44X/60X/0X/0/0");
$data['walletKey']['ethColdKeyObj']['path'] = "M/44X/60X/0X/0/0";
$data['walletKey']['ethColdKeyObj']['publicKey'] = $hardened->getPublicKey()->getHex();
$data['walletKey']['ethColdKeyObj']['privateKey'] = $hardened->getPrivateKey()->getHex();
$data['walletKey']['ethColdKeyObj']['address'] = $util->publicKeyToAddress($util->privateKeyToPublicKey($hardened->getPrivateKey()->getHex()));
//生成ETH热钱包
$hardened = $master->derivePath("44X/60X/1X/0/0");
$data['walletKey']['ethHotKeyObj']['path'] = "M/44X/60X/1X/0/0";
$data['walletKey']['ethHotKeyObj']['publicKey'] = $hardened->getPublicKey()->getHex();
$data['walletKey']['ethHotKeyObj']['privateKey'] = $hardened->getPrivateKey()->getHex();
$data['walletKey']['ethHotKeyObj']['address'] = $util->publicKeyToAddress($util->privateKeyToPublicKey($hardened->getPrivateKey()->getHex()));
//生成EOS冷钱包
$hardened = $master->derivePath("44X/194X/0X/0/0");
$data['walletKey']['eosColdKeyObj']['path'] = "M/44X/194X/0X/0/0";
$data['walletKey']['eosColdKeyObj']['publicKey'] = $hardened->getPublicKey()->getHex();
$data['walletKey']['eosColdKeyObj']['privateKey'] = $hardened->getPrivateKey()->getHex();
$data['walletKey']['eosColdKeyObj']['address'] = $util->publicKeyToAddress($util->privateKeyToPublicKey($hardened->getPrivateKey()->getHex()));
//生成EOS热钱包
$hardened = $master->derivePath("44X/194X/1X/0/0");
$data['walletKey']['eosHotKeyObj']['path'] = "M/44X/194X/1X/0/0";
$data['walletKey']['eosHotKeyObj']['publicKey'] = $hardened->getPublicKey()->getHex();
$data['walletKey']['eosHotKeyObj']['privateKey'] = $hardened->getPrivateKey()->getHex();
$data['walletKey']['eosHotKeyObj']['address'] = $util->publicKeyToAddress($util->privateKeyToPublicKey($hardened->getPrivateKey()->getHex()));
//生成USDT冷钱包
$hardened = $master->derivePath("44X/194X/3X/0/0");
$data['walletKey']['eosColdKeyObj']['path'] = "M/44X/0X/3X/0/0";
$data['walletKey']['eosColdKeyObj']['publicKey'] = $hardened->getPublicKey()->getHex();
$data['walletKey']['eosColdKeyObj']['privateKey'] = $hardened->getPrivateKey()->getHex();
$data['walletKey']['eosColdKeyObj']['address'] = $util->publicKeyToAddress($util->privateKeyToPublicKey($hardened->getPrivateKey()->getHex()));
//生成USDT热钱包
$hardened = $master->derivePath("44X/194X/4X/0/0");
$data['walletKey']['eosHotKeyObj']['path'] = "M/44X/0X/4X/0/0";
$data['walletKey']['eosHotKeyObj']['publicKey'] = $hardened->getPublicKey()->getHex();
$data['walletKey']['eosHotKeyObj']['privateKey'] = $hardened->getPrivateKey()->getHex();
$data['walletKey']['eosHotKeyObj']['address'] = $util->publicKeyToAddress($util->privateKeyToPublicKey($hardened->getPrivateKey()->getHex()));
//输出结果

echo "<pre>";
print_r($data) ;
echo "</pre>";

$json_string =  json_encode($data,320);
$bool = file_put_contents("xcoinpay_key.txt",$json_string);
echo $bool?"生成成功":"生成失败";
exit;