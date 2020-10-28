<!DOCTYPE html>
<?php 
/**
 * Author: David Jule. 000792459
 * Date Created: October 21th, 2020. 
 */
include "connect.php"; 

$recievedRow = filter_input(INPUT_GET, "row", FILTER_VALIDATE_INT);
$receivedColumn = filter_input(INPUT_GET, "col", FILTER_VALIDATE_INT);
//1 for win, 2 for loss.
$winOrLoss;

if($receivedColumn !== null && $recievedRow !== null){
    $command = "SELECT * FROM wumpuses";
    $stmt = $dbh->prepare($command);
    $params = [$receivedColumn,$recievedRow];
    $success = $stmt->execute($params); 
}



if ($success) { 
    //look for the wumpus in the database that was found by the user.
    while ($wumpus = $stmt->fetch()){
        //use "==" equals operator as extracted numbers are strings, not integers.
        if ($wumpus["Column"] == $receivedColumn && $wumpus["Row"] == $recievedRow){
            echo "<h1>YOU WON!</h1>";
            echo "<img src='images/Victory.gif' alt='Victory Dance'>"; 
            echo "<img src='images/winner.gif' alt='Victory Cup'>";
            $winOrLoss = 1;
        break;
        }
        else{
            echo "<h1>Better luck next time!<h1>";
            echo "<img src='images/gameOver.gif' alt='Game Over'>";
            $winOrLoss = 2;
        break;
        }
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
    <h3>Save your progress!</h3>
    <form action='save.php' method='POST'>
        <input type='email' name='email' placeholder='Enter Email.' required>
        <input type='hidden' name='winOrLoss' value='<?=$winOrLoss?>'>
        <input type='submit' value='Enter'>
    </form> 
</body>

</html>