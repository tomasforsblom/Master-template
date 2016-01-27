<?php
class CDiceGame {

private $dice;
private $roll;
private $diceImage;
private $db;

public function __construct($db) {
    $this->dice = isset($_SESSION["dice"]) ? $_SESSION["dice"]: new CDice();
    $this->roll = isset($_GET['roll']) ? true : false;
    $this->diceImage= new CDiceView();
    $this->db = $db;
}

/**
 * Metoden som styr själva spelet
 */
public function theGame() {
	$html = null;
	$html .= $this->diceImage->view();
	$this->startNewRound();
	$this->dice->roll();
    $html .= $this->checkIfOne();
    $this->saveThePoints();
    $html .= $this->diceImage->getRollsAsImage($this->dice->getAllRolls());
    $html .= "<p> Du har hittills " . $this->dice->getSumOfRound() . " poäng i denna runda.</p>";
    $html .= "<p> Du har " . $this->dice->getSavedPoints() . " sparade poäng.</p>";
    $html .= "<p> Du har " . $this->dice->getSum() . " poäng totalt.</p>";
    $html .= "<p> Du har kastat " . $this->dice->getNumberOfRolls() . " gånger.</p>";
    $html .= $this->checkIfWon();
    $_SESSION["dice"]=$this->dice;
    $html .= $this->diceImage->submitResult();
    return $html;
}

/**
 * Sänder kontaktformuläret och poängen till en databas
 */
public function sendToDatabase() {
	//Get incoming parameters
	$fnamn = isset($_POST['fnamn']) ? $_POST['fnamn'] : null;
	$enamn = isset($_POST['enamn']) ? $_POST['enamn'] : null;
	$epost = isset($_POST['epost']) ? $_POST['epost'] : null;
	$telefonnummer = isset($_POST['telefonnummer']) ? $_POST['telefonnummer'] : null;
	$points = $this->dice->getSum();
	$submitresult = isset($_POST['submitresult'])  ? true : false;
	
	// Check if form was submitted
	$output = null;
	if($submitresult) {
		$sql = 'INSERT INTO game_rm (fnamn, enamn, epost, telefonnummer, points, created) VALUES (?, ?, ?, ?, ?, NOW())';
		$params = array($fnamn, $enamn, $epost, $telefonnummer, $points);
		$this->db->ExecuteQuery($sql, $params);
		$output = 'Informationen sparades.';
	}
}


/**
 * Sparar poängen i variabeln $savePoints och raderar de poäng som finns i arrayen $lastRoll
 */
public function saveThePoints() {
	if(isset($_GET['savepoints'])) {
        $this->dice->setSavePoints($this->dice->getSum());
        $this->dice->setLastRoll();
    }
}

/**
 * Starta en ny runda
 */
public function startNewRound() {
    if(isset($_GET['end'])) {
    	$_SESSION = array();
    	session_destroy();
    	$this->dice = isset($_SESSION["dice"]) ? $_SESSION["dice"]: new CDice();
	}
}

/**
 * Kontrollerar om spelaren har vunnit
 */
public function checkIfWon() {
	$html="";
	if ($this->dice->getSum()>=100) {
		$html="Grattis! Du har fått 100 poäng på " . $this->dice->getNumberOfRolls() . " kast!";
		$this->dice->setSavePoints(0);
		$this->dice->setNumberOfRolls(0);
	}
	return $html;
}

/**
 * Kontrollerar om tärningen visar "1"
 * 
 */
public function checkIfOne() {
    $html="";
    if ($this->dice->getCurrentRoll() == 1) {
        $html .="Du fick 1 och börjar nu om från från dina sparade poäng. Tryck på 'Gör ett nytt kast";
        $this->dice->reset();
    }
    return $html;
}
}
