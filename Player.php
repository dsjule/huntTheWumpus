<?php
/**
 * A class to create Player objects.
 * 
 * David Jule 000792459, October 22th, 2020.
 */
class Player
{
    private $email;
    private $wins;
    private $losses;
    private $lastDayPlayed;

    function __construct($email, $wins, $losses, $lastDayPlayed)
    {
        $this->email = $email;
        $this->wins = $wins;
        $this->losses = $losses;
        $this->lastDayPlayed = $lastDayPlayed;
    }

    /**
     * Returns a string representation of the user object as a list item
     */
    function toListItem()
    {
        return "<li>$this->email $this->wins $this->losses $this->lastDayPlayed</li>";
    }

    /**
     * returns the email.
     */
    function getEmail() {
        return $this->email;
    }

    /**
     * returns the wins.
     */
    function getWins() {
        return $this->wins;
    }

    /**
     * returns the losses.
     */
    function getLosses(){
        return $this->losses;
    }

    /**
     * returns the last day played.
     */
    function getLastDayPLayed(){
        return $this->lastDayPlayed;
    }
}
