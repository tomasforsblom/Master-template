 <?php

 class CHTMLTable
 {
 	private $rows;
 	private $max;
 	private $hits;
 	private $page;
 
 	public function __construct($hits, $page) {
 		$this->hits = $hits;
 		$this->page = $page;
 	}
	
 	/**
 	 * Shows the chosen content into a row where the image and the title is clickable
 	 */
 	public function createHTMLTable($res, $rows) {
 		$this->rows = $rows;
 
 		// Calculate max
 		$this->max = ceil($this->rows / $this->hits);
 		
 		// Put results into a HTML-table
 		$tr = "<p>" . $this->getHitsPerPage(array(2, 4, 8)) . "</p>";
 		$tr .= "<table><tr><th></th><th>Titel " . $this->orderby('title') . "</th><th>År " . $this->orderby('year') . "</th><th>Kategori " . $this->orderby('category') . "</th><th>Pris " . $this->orderby('price') . "</th></tr>";
 		foreach($res AS $key => $val) {
 			$tr .= '<tr><td><img src="img.php?src=movie/'. $val->image .'.jpg&width=150&height=150" ></td>';
 			$tr .= "<td>" . "<a href='showmovie.php?title={$val->title}'>{$val->title}</a>" . "</td>";
 			$tr .= "<td>" . $val->YEAR . "</td>";
 			$tr .= "<td>" . $val->category . "</td>";
 			$tr .= "<td>" . $val->price . "</td></tr>";
 		}
 		$tr .= "</table>";
 
 		$tr .= "<p>" . $this->getPageNavigation($this->hits, $this->page, $this->max, $min=1) . "</p>";
 		return $tr;
 	}
	
 	/**
 	 * Shows the movie, including the plot
 	 */
 	public function createLargeHTMLTable($res, $rows) {
 		$tr = null; 				

 		$tr = "<table>";
 		foreach($res AS $key => $val) {
 			$tr .= '<tr><td><img src="img.php?src=movie/'. $val->image .'.jpg&width=350&height=350" ></td>';
 			$tr .= "<td>" . $val->plot . "</td>";
 			$tr .= "<td><iframe width='300' height='169' src=" . $val->trailer . " frameborder='0' allowfullscreen></iframe></td>"; 
 		}
 		$tr .="</table>";
 		 		
 		$tr .= "<table><tr><th>Titel</th><th>År</th><th>Kategori</th><th>Pris</th><th>imdb</th></tr>";
 		foreach($res AS $key => $val) {
 			$tr .= "<td>" . $val->title . "</td>";
 			$tr .= "<td>" . $val->YEAR . "</td>";
 			$tr .= "<td>" . $val->category . "</td>";
 			$tr .= "<td>" . $val->price . "</td>";
 			$tr .= "<td>" . "<a href='{$val->imdb}'>Länk till imdb</a>" . "</td></tr>";
 		}
 		$tr .= "</table>";
 	
 		return $tr;
 	}
 	
 	
 	/**
 	 * Function to create links for sorting
 	 *
 	 * @param string $column the name of the database column to sort by
 	 * @return string with links to order by column.
 	 */
 	public function orderby($column)
 	{
 		$nav  = "<a href='" . $this->getQueryString(array('orderby'=>$column, 'order'=>'asc')) . "'>&darr;</a>";
 		$nav .= "<a href='" . $this->getQueryString(array('orderby'=>$column, 'order'=>'desc')) . "'>&uarr;</a>";
 		return "<span class='orderby'>" . $nav . "</span>";
 	}
 
 	/**
 	 * Create navigation among pages.
 	 *
 	 * @param integer $hits per page.
 	 * @param integer $page current page.
 	 * @param integer $max number of pages.
 	 * @param integer $min is the first page number, usually 0 or 1.
 	 * @return string as a link to this page.
 	 */
 	public function getPageNavigation($hits, $page, $max, $min=1)
 	{
 		$nav  = "<a href='" . $this->getQueryString(array('page' => $min)) . "'>&lt;&lt;</a> ";
 		$nav .= "<a href='" . $this->getQueryString(array('page' => ($page > $min ? $page - 1 : $min) )) . "'>&lt;</a> ";
 
 		for($i=$min; $i<=$max; $i++)
 		{
 			$nav .= "<a href='" . $this->getQueryString(array('page' => $i)) . "'>$i</a> ";
 		}
 
 		$nav .= "<a href='" . $this->getQueryString(array('page' => ($page < $max ? $page + 1 : $max) )) . "'>&gt;</a> ";
 		$nav .= "<a href='" . $this->getQueryString(array('page' => $max)) . "'>&gt;&gt;</a> ";
 		return $nav;
 	}
 
 	/**
 	 * Create links for hits per page.
 	 *
 	 * @param array $hits a list of hits-options to display.
 	 * @return string as a link to this page.
 	 */
 	public function getHitsPerPage($hits)
 	{
 		$nav = "Träffar per sida: ";
 		foreach($hits AS $val)
 		{
 			$nav .= "<a href='" . $this->getQueryString(array('hits' => $val, 'page' => 1)) . "'>$val</a> ";
 		}
 		return $nav;
 	}
 
 	/**
 	 * Use the current querystring as base, modify it according to $options and return the modified query string.
 	 *
 	 * @param array $options to set/change.
 	 * @param string $prepend this to the resulting query string
 	 * @return string with an updated query string.
 	 */
 	public function getQueryString($options, $prepend='?')	{
 		// parse query string into array
 		$query = array();
 		parse_str($_SERVER['QUERY_STRING'], $query);
 		 
 		// Modify the existing query string with new options
 		$query = array_merge($query, $options);
 		 
 		// Return the modified querystring
 		return $prepend . http_build_query($query);
 	}
 	
 }