<?php

class CMovieSearch {
	
	private $db = null;
	private $sql;
	private $sqlMax;
	private $res;
	private $params = array();
	private $title;
	private $hits;
	private $page;
	private $year1;
	private $year2;
	private $orderby;
	private $order;
	private $where;

	/**
	 * 
	 * Constructor
	 */
	public function __construct($options, $title, $hits, $page, $year1, $year2, $orderby, $order)
	{
		$this->db = new CDatabase($options);
		$this->title = $title;
		$this->hits = $hits;
		$this->page = $page;
		$this->year1 = $year1;
		$this->year2 = $year2;
		$this->orderby = $orderby;
		$this->order = $order;

		$this->prepareSQL();
		$this->res = $this->db->ExecuteSelectQueryAndFetchAll($this->sql, $this->params);
	}
	
	/**
	 * Searchfield
	 */
	public function createSearchField() {
		$searchField = <<<EOD
        <form>
        <fieldset>
        <legend>Sök</legend>
        <p><label>Titel (delsträng, använd % som *): <input type='search' name='title' value='{$this->title}'/></label></p>
        <p><label>Skapad mellan åren: <input type='text' name='year1' value='{$this->year1}'/> - <input type='text' name='year2' value='{$this->year2}'/></label></p>
        <p><input type='submit' name='submit' value='Sök'/></p>
        <p><a href='?'>Visa alla</a></p>
        </fieldset>
        </form>
EOD;
		return $searchField;
	}

	
	public function prepareSQL() {
		$this->sql = "SELECT * FROM rm_movie";
		$this->params = null;

		// Do a SELECT from a table
		if($this->title) {
			$this->where .= "AND title LIKE ? ";
			$this->params[] = $this->title;
		}

		if($this->year1 && $this->year2) {
			$this->where .= "AND year >= ? AND year <= ? ";
			$this->params[] = $this->year1;
			$this->params[] = $this->year2;
		} elseif($this->year1) {
			$this->where .= "AND year >= ? ";
			$this->params[] = $this->year1;
		} elseif($this->year2) {
			$this->where .= "AND year <= ? ";
			$this->params[] = $this->year2;
		}

		// Conditions
		if($this->where != "")
		{
			$this->sql .= " WHERE 1 " . $this->where;
		}

		$this->sqlMax = $this->sql;

		// Do SELECT from a table
		$this->sql .= " ORDER BY $this->orderby $this->order";

		// Numbers of rows to show
		$this->sql .= " LIMIT $this->hits OFFSET " . (($this->page - 1) * $this->hits);
	}

	public function getRes() {
		return $this->res;
	}
	
	public function getMaxRows() {
		return count($this->db->ExecuteSelectQueryAndFetchAll($this->sqlMax, $this->params));
	}
}
