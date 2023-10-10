#!/usr/bin/php
<?php
require_once __DIR__ . 'vendor/autoload.php';
require_once('login.php');
$login_rpc = new LoginRpcClient();
echo "
<!DOCTYPE html>
<html>
    <head>
        <title>Loading...</title>
    </head>
    <body>
        <head>Authorizing Login</head>
        <p>Attempting to login...</p>
    </body>
</html>
";
$response = $login_rpc->call($_POST);
if ($response[0] == 1) {
    exit;
} else {
    echo "
    <!DOCTYPE html>
    <html>
        <head>
            <title>Unsuccessful Login</title>
        </head>
        <body>
            <head>Login Unsuccesful</head>
            <p>Unsuccessful login attempt with screen name $screenname. <a href='login.html'>Try again?</a></p>
        </body>
    </html>
    ";
}
$login_rpc->channel->close();
$login_rpc->connection->close();
?>