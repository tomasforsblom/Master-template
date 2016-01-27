<?php
class CDiceView {

/**
 * Visar instruktionerna till spelet samt spelarens val
 * 
 */
public function view() {	
$whatYouSee = <<<EOD
<header>
<h2>Tärningskast 100</h2>
</header>
<p>Vinn filmer genom att slå tärningar! I Tärningskast 100 samlar du poäng genom att slå med tärningen:</p>
<ul>
    <li>Två till sex ger två till sex i poäng</li>
    <li>Slår du etta så förlorar du dina poäng. Om du har sparat poäng får du nu börja på nytt med dessa poäng. Om du spelar själv får du börja om på nytt. Spelar du med en annan person går turen över till din motspelare</li>
    <li>Du deltar i utlottningen av filmer genom att lyckas samla ihop 100 poäng, utan någon etta bland tärningskasten</li>
	<li>Fyll i dina kontaktuppgifter nedan när du har lyckats samla ihop 100 poäng och tryck på "Skicka"</li>	
</ul>

<p class="btn"><a href='?end'>Starta en ny runda</a></p>
<p class="btn"><a href='?roll'>Gör ett nytt kast</a></p>
<p class="btn"><a href='?savepoints'>Spara dina poäng</a></p>
EOD;
return $whatYouSee;
}

/**
 * Formulär för att skicka in namn och kontaktuppgifter
 * 
 */
public function submitResult() {
	$result = <<<EOD
		<form method=post>
        <fieldset>
        <legend>Skriv in ditt namn och dina kontaktuppgifter för att vara med och tävla!</legend>
        <p><label>Förnamn:<br/><input type='text' name='fnamn'/></label></p>
        <p><label>Efternamn:<br/><input type='text' name='enamn'/></label></p>
        <p><label>E-post:<br/><input type='text' name='epost'/></label></p>
		<p><label>Telefonnummer:<br/><input type='text' name='telefonnummer'/></label></p>
        <p><input type='submit' name='submitresult' value='Skicka'</p>
        </fieldset>
        </form>
EOD;
	return $result;
}

/**
 * 
 * Visar tärningarna
 */
public function getRollsAsImage($rolls) {
	$html="<ul class='dice'>";
	foreach($rolls as $val) {
		$html .="<li class='dice-{$val}'></li>";
	}
	$html .="</ul>";
	return $html;
}

}
