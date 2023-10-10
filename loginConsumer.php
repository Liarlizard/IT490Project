#!/usr/bin/php
<?php
require_once __DIR__.'/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'test', 'test', 'testHost');
$channel = $connection->channel();
list($exchange_name, ,) = $channel->exchange_declare('loginExchange', 'direct');
list($loginQueue, ,) = $channel->queue_declare("loginQueue");
$channel->queue_bind($loginQueue, $exchange_name, "login");

?>