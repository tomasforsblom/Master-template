<?php

class CMovieHandler {
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
	public function getMovie($id) {
		$sql = 'SELECT * FROM rm_movie WHERE id = ?';
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($id));
		if(isset($res[0])) {
			$c = $res[0];
		} else {
			die('Misslyckades: det finns inget innehåll med sådant id.');
		}
		return $c;
	}
	
	
	/**
	 * Get all categories that are active
	 */
	public function getAllActiveCategories() {
		$sql = 'SELECT DISTINCT category FROM rm_movie';
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql);
		
		$categories = null;
		
		foreach($res as $val) {
			$categories .= "<a href=?category={$val->category}>{$val->category}</a> ";
	    }
	    return $categories;
	}
	
	/**
	 * Shows the chosen category
	 */
	public function showChosenCategory($category) {
		
		$tr = null;
		if($category) {
			//find all blogs that matches a specific category and put them i $res
			$sql = 'SELECT * from rm_movie WHERE category=?';
			
			$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($category));
			
		    $tr .= "<table><tr><th></th><th>Titel</th><th>År</th><th>Kategori</th><th>Pris</th></tr>";
 		    foreach($res as $key => $val) {
 			    $tr .= '<tr><td><img src="img.php?src=movie/'. $val->image .'.jpg&width=150&height=150" ></td>';
 			    $tr .= "<td>" . "<a href='showmovie.php?title={$val->title}'>{$val->title}</a>" . "</td>";
 			    $tr .= "<td>" . $val->YEAR . "</td>";
 			    $tr .= "<td>" . $val->category . "</td>";
 			    $tr .= "<td>" . $val->price . "</td></tr>";
 		    }
 		    $tr .= "</table>";
		}
		return $tr;
		
	}
	
    /**
     * A table for creating new content
     */
	public function createNewMovieTable() {
		$create = <<<EOD
	    <form method="post">
        <fieldset>
        <legend>Lägg till nytt innehåll</legend>
        <p><label>Titel:<br/><input type='text' name='title'/></label></p>
        <p><label>Längd:<br/><input type='text' name='LENGTH'/></label></p>
        <p><label>År:<br/><input type='text' name='YEAR'/></label></p>
        <p><label>Plot:<br/><textarea name='plot'></textarea></label></p>
        <p><label>Bild:<br/><input type='text' name='image'/></label></p>
		<p><label>Kategori:</label></br>
        <input type='checkbox' name='category[]' value="Romance"><label>Romance</label>
        <input type='checkbox' name='category[]' value="Action"><label>Action</label>
        <input type='checkbox' name='category[]' value="Adventure"><label>Adventure</label>
        <input type='checkbox' name='category[]' value="Comedy"><label>Comedy</label>
        <input type='checkbox' name='category[]' value="Drama"><label>Drama</label>
        <input type='checkbox' name='category[]' value="Animation"><label>Animation</label></p>
		<p><label>Pris:<br/><input type='text' name='price'/></label></p>
		<p><label>Trailer:<br/><input type='text' name='trailer'/></label></p>
		<p><label>Imdb:<br/><input type='text' name='imdb'/></label></p>
        <p class=buttons><input type='submit' name='create' value='Spara'/> <input type='reset' value='Återställ'/></p>
        </fieldset>
        </form>
EOD;
	return $create;
	}

	/**
	 * Visa allt innehåll i databasen
	 */
	public function showAllContent() {
		$sql = "SELECT * FROM rm_movie";
		
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql);
		if($res) {
			$items = null;
			foreach($res AS $key => $val) {
				$items .= "<li>" . htmlentities($val->title, null, 'UTF-8') . " |<a href='movie_edit.php?id={$val->id}'>ändra</a>" .  " |<a href='showmovie.php?title={$val->title}'>visa</a> " . " |<a href='movie_delete.php?id={$val->id}'>ta bort</a>)</li>\n";
			} 
		} 
		return $items;
	}
	
	/**
	 * Uppdatera innehållet i databasen
	 */
	public function updateMovie() {
		$title = $_POST['title'];
		$length = $_POST['LENGTH'];
		$year = $_POST['YEAR'];
		$plot = $_POST['plot'];
		$image = $_POST['image'];		
		$category = $_POST['category'];
		$price = $_POST['price'];
		$trailer = $_POST['trailer'];
		$imdb = $_POST['imdb'];
		$id = strip_tags($_POST['id']);
		
		$sql = 'UPDATE rm_movie SET
                  title = ?,
                  LENGTH = ?,
                  YEAR = ?,
                  plot = ?,
                  image = ?,
                  category = ?,
                  price = ?,
				  trailer = ?,
				  imdb = ?,
				  published = NOW()
              WHERE
                  id = ?';
       
       $params = array($title, $length, $year, $plot, $image, $category, $price, $trailer, $imdb, $id);
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
	 * Funktion för att ta bort innehåll ur databasen
	 */
	public function deleteMovie($id) {
		$sql = 'DELETE FROM rm_movie WHERE id = ?';		
                  $params = array($id);
                  $res = $this->db->ExecuteQuery($sql, $params);
                  return "Filmen är borttagen! Tryck <a href='my_account.php'>här</a> för att gå tillbaks till Hantera innehåll.";
	}
	
	/**
	 * 
	 * Lägger till innehåll i databasen.
	 */
	public function createMovieContent($title, $length, $year, $plot, $image, $category, $price, $trailer, $imdb) {
		
		$category_array = "";
		foreach ($_POST['category'] as $value) {
			$category_array .= $value . ",";
		}
		$category_array = substr($category_array,0,-1); //to remove the last comma		
		
		$sql = 'INSERT INTO rm_movie (title, LENGTH, YEAR, plot, image, category, price, trailer, imdb, published, created) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())';
		$params = array($title, $length, $year, $plot, $image, $category_array, $price, $trailer, $imdb);
		$res = $this->db->ExecuteQuery($sql, $params);
		    if($res) {
		    	$output = '"' . htmlentities($title) . '" har skapats';
		    } else {
		    	$output = "Sidan kunde inte skapas.";
		    }
		return $output;
	    //header('Location: content_view.php');
	}
	
	/**
	 * Showing the most popular movies
	 */
	public function mostPopularMovie() {
		$create = <<<EOD
 		<table class='mostpopularmovieandlastrentedmovie'>
 		<tr>
 				<th colspan='3'><h3 class='centertitle'>Hyr våra populäraste filmer!</h3></th>
 		</tr>
		<tr>
 				<td><img class='centerimage' src="img.php?src=movie/Star_Wars_7.jpg&height=150&width=200"</td>
 				<td><img class='centerimage' src="img.php?src=movie/Azumi.jpg&height=150&width=200"</td>
				<td><img class='centerimage' src="img.php?src=movie/7_samurai.jpg&height=150&width=200"</td>
 		</tr>
		</table>
EOD;
		return $create;
	}
	
	/**
	 * Showing the last rented movies
	 */
	public function lastRentedMovie() {
		$create = <<<EOD
 		<table class='mostpopularmovieandlastrentedmovie'>
 		<tr>
 				<th colspan='3'><h3 class='centertitle'>Senast hyrda filmer</h3></th>
 		</tr>
		<tr>
 				<td><img class='centerimage' src="img.php?src=movie/The_Godfather.jpg&height=150&width=200"></td>
 				<td><img class='centerimage' src="img.php?src=movie/Azumi.jpg&height=150&width=200"></td>
				<td><img class='centerimage' src="img.php?src=movie/American_Pie.jpg&height=150&width=200"></td>
 		</tr>
		</table>
EOD;
		return $create;
	}
	
	/**
	 * Publish the three newest movies on home page
	 */
	public function showThreeMoviesOnFirstPage($db) {
		$sql = 'SELECT * FROM rm_movie ORDER BY published DESC LIMIT 3';
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql);
		
		if($res) {
			$tr = null;
			$tr .= "<table class='newmoviesonfirstpage'><tr><th><h2 class='centertitle'>Nyheter</h2></th></tr><tr>";
			foreach($res AS $key => $val) {
				//$tr .= "<tr><td><a href='showmovie.php?title={$val->title}'>";
				$tr .= '<td><img class="centerimage" src="img.php?src=movie/'. $val->image .'.jpg&width=100&height=150"></td>';
				//$tr .= '<tr><td><img src="img.php?src=movie/'. $val->image .'.jpg&width=150&height=150"</tr></td>";
			}
			$tr .= "</tr></table>";
		}
		return $tr;
	
	}
	
	public function bestMoviesoftheMonth() {
		$create = <<<EOD
 		<table class='bestmovies'>
		<tr>
 				<th><h3 class='centertitle'>Första plats</h3></th>
				<th><h3 class='centertitle'>Andra plats</h3></th>
				<th><h3 class='centertitle'>Tredje plats</h3></th>
 		</tr>
		<tr>
 				<td><img class='centerimage' src="img.php?src=movie/The_Godfather.jpg&height=350&width=500"></td>
 				<td><img class='centerimage' src="img.php?src=movie/Azumi.jpg&height=350&width=500"></td>
				<td><img class='centerimage' src="img.php?src=movie/American_Pie.jpg&height=350&width=500"></td>
 		</tr>
		</table>
EOD;
		return $create;
	}
}