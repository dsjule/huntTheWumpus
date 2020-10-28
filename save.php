<!DOCTYPE html>
<?php 
/**
 * Author: David Jule. 000792459
 * Date Created: October 21th, 2020. 
 */
include "connect.php";
include "Player.php";

$emailAddress = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
$winOrLoss = filter_input(INPUT_POST, "winOrLoss", FILTER_VALIDATE_INT);
$paramsok = false;



/**
 * Return a command based on the player data that will be updated in the database. 
 * @param windOrLoss uses a value of 1 to update a win, and a 2 to update a loss.
 */
function updatePlayerDataCommand($winOrLoss){
    if ($winOrLoss == 1){
       $command = "UPDATE players SET Wins = ?, LastDayPlayed = ? WHERE Email = ?";
       return $command;
    }
    if ($winOrLoss == 2){
        $command = "UPDATE players SET Losses = ?, LastDayPlayed = ? WHERE Email = ?";
        return $command;
    }
}

/**
 * Use a querty statement to retrieve the number of wins that the player has.
 * @param emailAdress Email adress that will be used in the query.
 * @param dbh database where query will be executed.
 * @return String representing number of wins.
 */
function getNumberOfWins($emailAddress, $dbh){
    $command = "SELECT Wins FROM players WHERE Email = ?";
    $stmt = $dbh->prepare($command);
    $params = [$emailAddress];
    $success = $stmt->execute($params);
    //fetch() returns associative array.
    $data = $stmt->fetch();
    if ($success && $data){
        return $data["Wins"]; 
    }
    else{
        echo "<p>Failed to retrieve wins from player<p>"; 
    }
}

/**
 * Use a querty statement to retrieve the number of losses that the player has.
 * @param emailAdress Email adress that will be used in the query.
 * @param dbh database where query will be executed.
 * @return String representing number of losses.
 */
function getNumberOfLosses($emailAddress, $dbh){
    $command = "SELECT Losses FROM players WHERE Email = ?";
    $stmt = $dbh->prepare($command);
    $params = [$emailAddress];
    $success = $stmt->execute($params);
    //fetch() returns associative array.
    $data = $stmt->fetch();
    if ($success && $data){
        return $data["Losses"]; 
    }
    else{
        echo "<p>Failed to retrieve losses from player<p>"; 
    }
}

/**
 * Select either win or loss to be updated and create a statement for the paramenter of the query.
 * @param winOrLoss uses a value of 1 to update a win, and a 2 to update a loss.
 * @param dbh represents the database that will be used to execute the query.
 * @param emailAddress represents player in the database.
 * @return Integer representing the value that will be used for the update query.
 */
function winOrLossIncrementator($emailAddress, $dbh, $winOrLoss){
    if ($winOrLoss == 1){
       $wins = (int)getNumberOfWins($emailAddress, $dbh);
       $wins++;
       return $wins;
    }
    if ($winOrLoss == 2){
        $losses = (int)getNumberOfLosses($emailAddress, $dbh);
        $losses++;
        return $losses;
    }
}

/**
 * Verify if the email address exists in the database.
 * @param emailAdress represents player ID used to search in the database.
 * @param dbh represents the database that will be used to execute the query.
 */
function verifyEmailAdress($emailAddress, $dbh){
    $command = "SELECT * FROM players WHERE Email = ?";
    $stmt = $dbh->prepare($command);
    $params = [$emailAddress];
    $success = $stmt->execute($params);

    if($success && $stmt->fetch()){
        return $success;
    } 
    else{
        return null;
    }
}

/**
 * Create a new record for the player database.
 * @param emailAdress represents the primary key for the creation of the record.
 * @param dbh represents the database where the record will be created.
 */
function createNewPlayer($emailAddress, $dbh){
    $command = "INSERT INTO players (Email, LastDayPlayed) VALUES (?, ?) ";
    $stmt = $dbh->prepare($command);
    $params = [$emailAddress, date("l jS \of F Y h:i:s A")];
    $success = $stmt->execute($params);
    if (!$success){
        echo "Failed to created new player in database.";
    }
}

/**
 * Update player informartion in the database.
 * @param emailAdress represents player in the database.
 * @param dbh represents the database that will be used to execute the query.
 * @param winOrLoss represents a value of 1 to update a win, and a 2 to update a loss.
 */
function updatePlayer($emailAddress, $dbh, $winOrLoss){
    $command = updatePlayerDataCommand($winOrLoss);
    $stmt = $dbh->prepare($command);
    $params = [winOrLossIncrementator($emailAddress,$dbh,$winOrLoss),date("l jS \of F Y h:i:s A"),$emailAddress];
    $success = $stmt->execute($params); 
    if(!$success){
        echo "Failed to update player information.";
    }
}

/**
 * Get the player data.
 * @param emailAddress represents the ID used to get player data.
 * @param dbh represents the database that will be used to execute the query.
 * @return Player object representing the player's information.
 */
function getPlayerData($emailAddress, $dbh){
    $command = "SELECT * FROM players WHERE Email = ? ";
    $stmt = $dbh->prepare($command);
    $params = [$emailAddress];
    $success = $stmt->execute($params);

    if($success && $row = $stmt->fetch()){
        $player = new Player($row["Email"], $row["Wins"], $row["Losses"], $row["LastDayPlayed"]);
        return $player;
    }
    else{
        echo "<p>Error while retrieven Player's Information.</p>";
    }
}

/**
 * Get the top 10 players in the database.
 * @param dbh represents the database that will be used to execute the query.
 * @return Array representing a list of objects of type Player.
 */
function getTopPlayers($dbh){
    $limit = 10;
    $index = 0;
    $playerList = [];
    $command = "SELECT * FROM players ORDER BY Wins DESC";
    $stmt = $dbh->prepare($command);
    $success = $stmt->execute();

    if($success){
        while ($index <= $limit){
            $row = $stmt->fetch();
            $player = new Player($row["Email"], $row["Wins"], $row["Losses"], $row["LastDayPlayed"]);
            array_push($playerList, $player);
            $index++;
        }
        return $playerList;
    }
    else{
        echo "<p>Error while retrieving Top Players list.</p>";
    }

}
?>
<html>

<head>
    <title>Hunt the Wumpus!</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/wumpus.css">
</head>

<body>
    <?php
        //Parameters flag.
        if ($emailAddress !== null){
            if (verifyEmailAdress($emailAddress, $dbh)) {
                updatePlayer($emailAddress,$dbh,$winOrLoss);
                echo "<p>Player information has been updated successfully!</p>";   
            } else {
                createNewPlayer($emailAddress,$dbh);
                echo "<p>New player record was created!.<br></p>";
                updatePlayer($emailAddress,$dbh,$winOrLoss);
                echo "<p>Player information has been updated successfully!</p>";
            }
        } else {
            echo "<p>Bad Input Detected! Failed to receive player's email.</p>";
        }
    ?>
    <table>
        <tr>Your Information:</tr>
        <tr>
            <th>Email</th>
            <th>Wins</th>
            <th>Losses</th>
            <th>Last Day Played</th>
        </tr>
        <?php
            $player = getPlayerData($emailAddress, $dbh);
            echo "<tr><td>{$player->getEmail()}</td><td>{$player->getWins()}</td>
            <td>{$player->getLosses()}</td><td>{$player->getLastDayPlayed()}</td></tr>";
        ?>
    </table>
    <table>
        <tr>Top 10 best players:</tr>
        <tr>
            <th>Email</th>
            <th>Wins</th>
            <th>Losses</th>
            <th>Last Day Played</th>
        </tr>
        <?php
             $listOfPlayers = getTopPlayers($dbh);
             
             foreach($listOfPlayers as $player){
                echo "<tr><td>{$player->getEmail()}</td><td>{$player->getWins()}</td>
                <td>{$player->getLosses()}</td><td>{$player->getLastDayPlayed()}</td></tr>";
             }
        ?>
    </table>
    <p><a href='index.php'>Return to game!</a></p>

</body>

</html>