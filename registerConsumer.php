#!/usr/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once('login.php');
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'test', 'test', 'testHost');
$channel = $connection->channel();

$channel->queue_declare('rpc_queue', false, false, false, false);

function login($screenname, $password)
{
    $loginDB = new loginDB();
    return $loginDB -> validateLogin($screenname, $password);
}

echo " [x] Awaiting RPC requests\n";
$callback = function ($req) {
    $credentials = explode(",", $req->body);
    $screenname = $credentials[0];
    $password = $credentials[1];
    $response = login($screenname, $password);
    $msg = new AMQPMessage(
        (string) $response,
        array('correlation_id' => $req->get('correlation_id'))
    );
    $req->getChannel()->basic_publish(
        $msg,
        '',
        $req->get('reply_to')
    );
    $req->ack();
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>