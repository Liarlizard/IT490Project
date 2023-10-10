#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$request = array();
$request['type'] = "login";
$request['username'] = $_POST[0];
$request['password'] = $_POST[1];
$request['message'] = "Check if this user exists.";

?>