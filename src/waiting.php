<?php
session_start();
$player1 = $_SESSION['username'];
if (isset($_POST['back'])) {
    header('Location:lobby.php');
    die();
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Waiting player</title>
</head>
<body>
<h2>Esperando oponente...</h2>
<h3>Jugadores conectados:</h3>
<b><?php echo $player1 ?></b>
<form method="post">
    <button name="back">Back to lobby</button>
</form>
</body>
</html>
