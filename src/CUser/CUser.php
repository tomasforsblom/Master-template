<?php

class CUser {

private $db;
private $acronym;
private $name;
private $stmt = null;
	
public function __construct($options) {
	//check if there is a user in the session
    if(isset($_SESSION['user'])) {
       $this->acronym = $_SESSION['user']->acronym;
       $this->name = $_SESSION['user']->name;
    }
    else {
      $this->acronym = null;
      $this->name = null;
    }
    $this->db = new CDatabase($options);
}

/**
 * Check if user is authenticated
 */
public function IsAuthenticated() {
	$acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;

	if($acronym) {
		$this->IsAuthenticated = true;
	} else {
		$this->IsAuthenticated = false;
	}

	return $this->IsAuthenticated;
}

/**
 * 
 * Show status of the user
 */
public function showStatus() {
	if($this->IsAuthenticated()) {
		return "Du är inloggad som: <b>" . $this->GetAcronym() . "</b> (" . $this->GetName() . ")";
	} else {
		return "Du är <b>utloggad</b>.";
    }
}

/**
 * 
 * Return user acronym
 */
public function GetAcronym() {
	return  $this->acronym;
}

/**
 *  Return user name
 */
public function GetName() {
	return  $this->name;
}

/**
 * Login
 */
public function login($user, $password) {
	//build query and array
	$sql = "SELECT acronym, name FROM USER_RM WHERE acronym = ? AND password = md5(concat(?, salt))";
	$params = array($user, $password);
	
	//run query
	$res = $this->db->ExecuteSelectQueryAndFetchAll($sql,$params);
	
	//login
	if(isset($res[0])) {
		$_SESSION['user'] = $res[0];
		$this->acronym = $res[0]->acronym;
		$this->name = $res[0]->name;
		$this->showStatus();
	}
	header('Location: my_account.php');
}

/**
 * Logout function
 * Sends the user to om.php
 */
public function logout() {
    unset($_SESSION['user']);
    header('Location: index.php');
}

/**
 * Creates a login table
 * @return login table
 */
public function loginTable() {
	$loginTable = <<<EOD
 	<form method=post>
 	<fieldset>
 	<legend>Login</legend>
 	<p><em>Du kan logga in med doe:doe eller admin:admin.</em></p>
 	<p><label>Användare:<br/><input type='text' name='acronym' value=''/></label></p>
 	<p><label>Lösenord:<br/><input type='text' name='password' value=''/></label></p>
 	<p><input type='submit' name='login' value='Login'/></p>
 	</fieldset>
 	</form>
EOD;
	return $loginTable;
}

/**
 * Creates a logout table
 */
public function logoutTable() {
	$logoutTable = <<<EOD
	<form method=post>
	<fieldset>
	<legend>Logga ut</legend>
	<p>Tryck här för att logga ut.</p>
	<p><input type='submit' name='logout' value='Logout'/></p>
	</fieldset>
	</form>
EOD;
	return $logoutTable;
}

/**
 * Show all users
 */
public function showUsers() {
	$sql = "SELECT * FROM USER_RM";
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql);
		if($res) {
			$items = null;
			foreach($res AS $key => $val) {
				$items .= "<li>" . htmlentities($val->name, null, 'UTF-8') . " | <a href='user_update.php?id={$val->id}'>ändra</a>" .  " |<a href='user_show.php?acronym={$val->acronym}'>visa</a> " . " |<a href='user_delete.php?id={$val->id}'>ta bort</a></li>\n";
			}
		}
		return $items;
}


/**
 * Show a user
 */
public function showUser($acronym) {
	$sql = "SELECT * FROM USER_RM WHERE acronym = ?";	
	$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($acronym));
	$items = null;
	foreach($res as $key => $val) {
		$items .= "Acronym: " . $val->acronym . " | Namn: " . $val->name . " | Typ: " . $val->type;
		}
		return $items;	
}

/**
 * Update user
 */
public function updateUser() {
	$acronym = $_POST['acronym'];
	$name = $_POST['name'];
	$type = $_POST['type'];
	$id = $_POST['id'];
	//$salt = "unix_timestamp()";
	
	$sql = 'UPDATE USER_RM SET
                  acronym = ?,
                  name = ?,
			      type = ?
              WHERE
                  id = ?';
	 
	$params = array($acronym, $name, $type, $id);
	$res = $this->db->ExecuteQuery($sql, $params);
	
	$sql = "UPDATE user_rm SET password = md5(concat('{$acronym}', salt)) WHERE acronym = '{$acronym}'";
	$params = array($sql, $acronym);
	$res = $this->db->ExecuteQuery($sql, $params);
	
	if($res) {
		$output = 'Informationen sparades';
	} else {
		$output = 'Informationen sparades ej.<br><pre>' . print_r($this->db->ErrorInfo(), 1) . '</pre>';
	}
	return $output;
}

/**
 * Tar fram innehållet i databasen för ett specifikt id
 */
public function getUser($id) {
	$sql = 'SELECT * FROM USER_RM WHERE id = ?';
	$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($id));
	if(isset($res[0])) {
		$c = $res[0];
	} else {
		die('Misslyckades: det finns inget innehåll med sådant id.');
	}
	return $c;
}

/**
 * Delete user
 */
public function deleteUser($id) {
	$sql = 'DELETE FROM USER_RM WHERE id = ?';
	$params = array($id);
	$res = $this->db->ExecuteQuery($sql, $params);
	return "Användaren är borttagen! Tryck <a href='my_account.php'>här</a> för att gå tillbaks till Hantera innehåll.";
}

/**
 * Add user
 */
public function addUser($acronym, $name, $salt, $type) {
	$sql = 'INSERT INTO USER_RM (acronym, name, salt, type) VALUES(?, ?, unix_timestamp(), ?)';
	$params = array($acronym, $name, $type);
	$res = $this->db->ExecuteQuery($sql, $params);

	$sql = "UPDATE USER_RM SET password = md5(concat('{$acronym}', salt)) WHERE acronym = '{$acronym}'";
	$params = array($sql, $acronym);
	$res = $this->db->ExecuteQuery($sql, $params);
	if($res) {
		$output = 'Användaren har skapats';
	} else {
		$output = "Användaren kunde inte skapas.";
	}
	return $output;
}

/**
 * A table for creating new users
 */
public function createNewUserTable() {
	$create = <<<EOD
	    <form method=post>
        <fieldset>
        <p><label>Acronym:<br/><input type='text' name='acronym'/></label></p>
        <p><label>Namn:<br/><input type='text' name='name'/></label></p>
        <p><select name="type">
            <option value='admin'>Administratör</option>
            <option value='user'>Användare</option>
        </select></p>
        <p class=buttons><input type='submit' name='create' value='Spara'/> <input type='reset' value='Återställ'/></p>
        <p>Tryck <a href='my_account.php'>här</a> för att gå tillbaks till Hantera innehåll.</p>
		</fieldset>
        </form>
EOD;
	return $create;
}

/**
 * Funktion för att skapa den initiala databasen
 */
public function createTable() {
	$sql = "DROP TABLE IF EXISTS user_rm;
            CREATE TABLE user_rm
           (
           id INT AUTO_INCREMENT PRIMARY KEY,
           acronym CHAR(12) UNIQUE NOT NULL,
           name VARCHAR(80),
           password CHAR(32),
           salt INT NOT NULL,
           type VARCHAR(80)
           ) ENGINE INNODB CHARACTER SET utf8;
 
           INSERT INTO user_rm (acronym, name, salt, type) VALUES 
           ('doe', 'John/Jane Doe', unix_timestamp(),'Användare'),
           ('admin', 'Administrator', unix_timestamp(),'Administratör');

   UPDATE USER_RM SET password = md5(concat('doe', salt)) WHERE acronym = 'doe';
   UPDATE USER_RM SET password = md5(concat('admin', salt)) WHERE acronym = 'admin';";
			
	$this->db->executeQuery($sql);
}

}