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
$screenname = $_POST[0];
$password = $_POST[1];
$msg = new AMQPMessage(`$screenname,$password`);
$channel->basic_publish($msg, $exchange_name, "login");

echo `[x] Sent login request for user $screenname`;

$channel->close();
$connection->close();
?>