<?php
require_once "config.php";
global $db;

session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    die();
}
$username = $_SESSION['username'];
$query = $db->prepare('SELECT * FROM player WHERE username=:user');
$query->bindValue(':user', $username);
$query->execute();

$user = $query->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    header('Location: index.php');
    die();
}
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    die();
}
if (isset($_POST['newgame'])) {
    $query = $db->prepare('INSERT INTO game (player_1) VALUE (:id)');
    $query->bindValue(':id', $user['id']);
    $query->execute();
    header('Refresh:5; Location:waiting.php');
    die();
}
if (isset($_POST['join'])) {
    $games = $db->prepare('SELECT * FROM game WHERE id=:id');
    $games->bindValue(':id', $_POST['join'], PDO::PARAM_INT);
    $games->execute();
    $game = $games->fetch();
    if (is_null($game['player_1'])) {
        $query = $db->prepare("UPDATE game SET player_1=:id WHERE id=:game_id");
        $query->bindValue(':id', $user['id']);
        $query->bindValue(':game_id', $game['id']);
        $query->execute();
        header('Refresh:5; Location:waiting.php');
    } elseif(is_null($game['player_2'])) {
        $query = $db->prepare("UPDATE game SET player_2=:id, state='active' WHERE id=:game_id");
        $query->bindValue(':id', $user['id']);
        $query->bindValue(':game_id', $game['id']);
        $query->execute();
        header('Location:chess.php');
    }
    $_SESSION['game_id'] = $game['id'];
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
    <title>OreChess</title>
    <style>
        table, tr, th, td {
            border: 1px solid black;
            border-spacing: 0;
            text-align: center;
        }
    </style>
</head>
<body>
<h1>OreChess</h1>
<form method="post">
    <h2>Partidas activas del jugador <?= htmlentities($user['username']) ?></h2>
    <table>
        <thead>
        <tr>
            <th>Player 1</th>
            <th>Player 2</th>
            <th>State</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $games = $db->prepare("SELECT game.* FROM game JOIN player ON game.player_1 = player.id OR game.player_2 = player.id WHERE player.id=:username AND game.state <> 'finished'");
        $games->bindValue(':username', $user['id']);
        $games->setFetchMode(PDO::FETCH_ASSOC);
        $games->execute();
        if ($games->rowCount() > 0) {
            foreach ($games as $game) {
                echo "<tr>";
                echo "<td>" . $game['player_1'] . "</td>";
                echo "<td>" . $game['player_2'] . "</td>";
                echo "<td>" . htmlentities($game['state']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "No perteneces a ninguna partida";
        }

        ?>
        </tbody>
    </table>
    <h2>Partidas a las que te puedes unir</h2>
    <table>
        <thead>
        <tr>
            <th>Game ID</th>
            <th>Player 1</th>
            <th>Player 2</th>
            <th>State</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $games = $db->prepare("SELECT * FROM game WHERE state <> 'finished'");
        $games->setFetchMode(PDO::FETCH_ASSOC);
        $games->execute();
        if ($games->rowCount() > 0) {
            foreach ($games as $game) {
                echo "<tr>";
                echo "<td>" . htmlentities($game['id']) . "</td>";
                if (is_null($game['player_1'])) {
                    echo "<td>Vacio</td>";
                } else {
                    echo "<td>" . htmlentities($game['player_1']) . "</td>";
                }
                if (is_null($game['player_2'])) {
                    echo "<td>Vacio</td>";
                } else {
                    echo "<td>" . htmlentities($game['player_2']) . "</td>";
                }

                if ($game['state'] == 'inactive') {
                    echo "<td>" . htmlentities($game['state']) . "<button name='join' value=" . $game['id'] . ">Join</button></td>";
                } else {
                    echo "<td>" . htmlentities($game['state']) . "</td>";
                }
                echo "</tr>";
            }
        } else {
            echo "No hay partidas a las que te puedas unir";
        }

        ?>
        </tbody>
    </table>
    <button type="submit" name="logout">Log out</button>
    <button type="submit" name="newgame">New Game</button>
</form>
</body>
</html>
