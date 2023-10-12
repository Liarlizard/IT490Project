#!/usr/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
class LoginRpcClient
{
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            'localhost',
            5672,
            'test',
            'test',
            'testHost'
        );
        $this->channel = $this->connection->channel();
        list($this->callback_queue, , ) = $this->channel->queue_declare(
            "",
            false,
            false,
            true,
            false
        );
        $this->channel->basic_consume(
            $this->callback_queue,
            '',
            false,
            true,
            false,
            false,
            array(
                $this,
                'onResponse'
            )
        );
    }

    public function onResponse($rep)
    {
        if ($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }

    public function call($credentials)
    {
        $this->response = null;
        $this->corr_id = uniqid();
        $screenname = $credentials[0];
        $password = $credentials[1];
        echo "Screen Name: $screenname \n Password: $password";
        $credString = "$screenname,$password";
        $msg = new AMQPMessage(
            $credString,
            array(
                'correlation_id' => $this->corr_id,
                'reply_to' => $this->callback_queue
            )
        );
        $this->channel->basic_publish($msg, '', 'rpc_queue');
        while (!$this->response) {
            $this->channel->wait();
        }
        $responseArray = array(intval($this->response), $screenname);
        return $responseArray;
    }

    public function close() {
        $this->connection->close();
        $this->channel->close();
    }
}
    $loginClient = new LoginRpcClient();
    $credentials = array($argv[1], $argv[2]);
    $response = $loginClient->call($credentials);
    echo var_dump($response);
    $loginClient->close();
?>