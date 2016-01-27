<?php

class CDice {

private $lastRoll=array();
private $currentRoll;
private $savePoints;
private $sum;
private $numberOfRolls;

public function __construct() {
    $this->lastRoll=array();
    $this->savePoints = 0;
    $this->sum = 0;
    $this->currentRoll = 0;
    $this->numberOfRolls = 0; 
}

/**
 * Gör ett nytt kast
 */
public function roll() {
	if(isset($_GET['roll'])) {
		$this->currentRoll = rand(1, 6);
		$this->lastRoll[] = $this->currentRoll;
		$this->numberOfRolls+=1;
		$this->sum + $this->currentRoll;
	}
}

/**
 * Den totala summan, inklusive osparade poäng
 */
public function getSum() {
    return array_sum($this->lastRoll) + $this->savePoints;
}

/**
 * 
 * Sätter ett nytt värde på variablen $savePoints
 */
public function setSavePoints($savePoints) {
	$this->savePoints = $savePoints;
}

/**
 * Get-funktion för variabeln $savePoints
 */
public function getSavedPoints() {
	return $this->savePoints;
}

/**
 * Antalet kast totalt
 */
public function getNumberOfRolls() {
    return $this->numberOfRolls;
}

/**
 * 
 * Sätter ett nytt värde på $numberOfRolls
 */
public function setNumberOfRolls($setRolls) {
    $this->numberOfRolls = $setRolls;
}

/**
 * Raderar kasten i arayen $lastRoll
 */
public function setLastRoll() {
    $this->lastRoll = [];
}

/**
 * Get-metod för det aktuella kastet
 */
public function getCurrentRoll() {
    return $this->currentRoll;
}

/**
 * Get-metod för de kast som finns i den aktuella rundan
 */
public function getAllRolls() {
    return $this->lastRoll;
}

/**
 * Räknar ut summan av den aktuella rundan
 */
public function getSumOfRound() {
    return array_sum($this->lastRoll);
}

/**
 * Återställer $sum till den sparade ställningen samt raderar kasten i arrayen lastRoll
 */
public function reset() {
    $this->lastRoll=array();
    $this->sum = $this->savePoints;
}

} 

