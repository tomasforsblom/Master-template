<?php

class CContent { 
	private $db;
	
	public function __construct($db) {
		$this->db = $db;
	}
	
	/**
	 * Create a link to the content, based on its type.
	 *
	 * @param object $content to link to.
	 * @return string with url to display content.
	 */
	public function getUrlToContent($content) {
		switch($content->TYPE) {
			case 'page': return "content_page.php?url={$content->url}";
			    break;
			case 'post': return "content_blog.php?slug={$content->slug}";
			    break;
			default: return null;
			    break;
		} 
	}
	
	/**
	 * Create a slug of a string, to be used as url.
	 *
	 * @param string $str the string to format as slug.
	 * @returns str the formatted slug.
	 */
	function slugify($str) {
		$str = mb_strtolower(trim($str));
		$str = str_replace(array('å','ä','ö'), array('a','a','o'), $str);
		$str = preg_replace('/[^a-z0-9-]/', '-', $str);
		$str = trim(preg_replace('/-+/', '-', $str), '-');
		return $str;
	}
	
	/**
	 * Tar fram innehållet i databasen för ett specifikt id
	 */
	public function getContent($id) {
		$sql = 'SELECT * FROM content_rm WHERE id = ?';
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($id));
		if(isset($res[0])) {
			$c = $res[0];
		} else {
			die('Misslyckades: det finns inget innehåll med sådant id.');
		}
		return $c;
	}
	
    /**
     * A table for creating new content
     */
	public function createNewContentTable() {
		$create = <<<EOD
	    <form method=post>
        <fieldset>
        <legend>Lägg till nytt innehåll</legend>
        <p><label>Titel:<br/><input type='text' name='title'/></label></p>
        <p><label>Slug:<br/><input type='text' name='slug'/></label></p>
        <p><label>Url:<br/><input type='text' name='url'/></label></p>
        <p><label>Text:<br/><textarea name='DATA'></textarea></label></p>
        <p>Typ:<br>
		<input type="radio" name="TYPE" value="page">Sida<br>
		<input type="radio" name="TYPE" value="post">Blogg<br></p>
        <p>Välj filter genom att trycka på valda filter med Ctrl<br>
		<select multiple="multiple" name="FILTER[]">
        <option value="bbcode">bbcode2html</option>
        <option value="clickable">clickable</option>
        <option value="markdown">markdown</option>
        <option value="nl2br">nl2br</option>
        <option value="shortcode">shortcode</option>
        </select></p>
		<p>Kategori:<br>
		<input type="radio" name="category" value="Nya filmer">Nya filmer<br>
		<input type="radio" name="category" value="Filmvärlden">Filmvärlden<br>
        <input type="radio" name="category" value="RM Movie Rental">RM Movie Rental<br></p>
		<p class=buttons><input type='submit' name='create' value='Spara'/> <input type='reset' value='Återställ'/></p>
        <p>Tryck <a href='my_account.php'>här</a> för att gå tillbaks till Hantera innehåll.</p>
		</fieldset>
        </form>
EOD;
	return $create;
	}

	/**
	 * Visa allt innehåll i databasen, dvs. alla texter förutom filmerna
	 */
	public function showAllContent() {
		
		$sql = "SELECT *, (published <= NOW()) AS available FROM content_rm WHERE deleted IS NULL";
		
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql);
		if($res) {
			$items = null;
			foreach($res AS $key => $val) {
				$items .= "<li>{$val->TYPE} (" . (!$val->available ? 'inte ' : null) . "publicerad): " . htmlentities($val->title, null, 'UTF-8') . " (<a href='content_edit.php?id={$val->id}'>ändra</a> <a href='" . $this->getUrlToContent($val) . "'>visa</a> <a href='content_delete.php?id={$val->id}'>ta bort</a>)</li>\n";
			} 
		} 
		return $items;
	}
	
	
	//$published = strip_tags($_POST['published']);
	//$filter = strip_tags($_POST['filter']);
	//$id = strip_tags($_POST['id']);
	//$url = strip_tags($_POST['url']);
	/**
	 * Uppdatera innehållet i databasen
	 */
	public function updateContent() {
		$url = $_POST['url'];
		$type = $_POST['type'];
		$published = $_POST['published'];
		$filter = $_POST['filter'];
		$title = $_POST['title'];
		$data = $_POST['data'];
		$slug = $_POST['slug'];
		$category = $_POST['category'];
		$id = $_POST['id'];
		
		$sql = 'UPDATE content_rm SET
                  slug = ?,
				  url = ?,
				  TYPE = ?,
				  title = ?,
                  DATA = ?,
                  FILTER = ?,
                  category = ?,
				  published = ?,
                  updated = NOW()
              WHERE
                  id = ?';
       
       $url = empty($url) ? null : $url;
       $filter = empty($filter) ? 'markdown' : $filter;
       $params = array($slug, $url, $type, $title, $data, $filter, $category, $published, $id);
       $res = $this->db->ExecuteQuery($sql, $params);
       if($res) {
       	   $output = 'Informationen sparades';
       } else {
       	$output = 'Informationen sparades ej.<br><pre>' . print_r($this->db->ErrorInfo(), 1) . '</pre>';
       }
       return $output;
	}
	
	/**
	 * 
	 * Funktion för att to bort innehåll ur databasen
	 */
	public function deleteContent($id) {
		$sql = 'UPDATE content_rm SET
                  deleted = NOW(),
                  published = null
              WHERE
                  id = ?';
		
                  $params = array($id);
                  $res = $this->db->ExecuteQuery($sql, $params);
                  return "Den är borttagen! Tryck <a href='my_account.php'>här</a> för att gå tillbaks till Hantera innehåll.";
	}
	
	/**
	 * 
	 * Lägger till innehåll i databasen.
	 */
	public function createContent($slug, $url, $TYPE, $title, $DATA, $FILTER, $category) {
		$filters = "";
		foreach ($_POST['FILTER'] as $value) {
		    $filters .= $value . ",";
		}
		$filters = substr($filters,0,-1); //to remove the last comma
		$sql = 'INSERT INTO content_rm (slug, url, TYPE, title, DATA, FILTER, category, published, created) VALUES(?, ?, ?, ?, ?, ?, ?, NOW(), NOW())';
		$params = array($slug, $url, $TYPE, $title, $DATA, $filters, $category);
		$res = $this->db->ExecuteQuery($sql, $params);
		    if($res) {
		  	$output = '"' . htmlentities($title) . '" har skapats';
		    } else {
			    $output = "Sidan kunde inte skapas.";
		    }
		return $output;
		}
	
	
	/**
	 * Publish the last three blogs on home page
	 */
	public function showBlogsOnFirstPage($db) {
		$sql = 'SELECT * FROM content_rm WHERE TYPE="post" ORDER BY published DESC LIMIT 3';
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql);
		
		$showBlogs = new CBlog($db);
		
		$blogs = $showBlogs->showSubsetofBlogs($res);
		return $blogs;
	}
	
	/**
	 * Funktion för att skapa den initiala databasen
	 */
	public function createTable() {
		$sql = "DROP TABLE IF EXISTS content_rm;
                    CREATE TABLE content_rm
                    (
                      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
                      slug CHAR(80) UNIQUE,
                      url CHAR(80) UNIQUE,
                      TYPE CHAR(80),
                      title VARCHAR(80),
                      DATA TEXT,
                      FILTER CHAR(80),
				      category CHAR(80),
                      published DATETIME,
                      created DATETIME,
                      updated DATETIME,
                      deleted DATETIME

                    ) ENGINE INNODB CHARACTER SET utf8;";
	$this->db->executeQuery($sql);
	}

}