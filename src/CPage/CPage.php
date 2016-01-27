<?php

class CPage {

	private $db;
	private $filter;
	
	public function __construct($db) {
		$this->db = $db;
		$this->filter = new CTextFilter();
	}
		
	/**
	 * Get ONE page
	 */
	public function getPage($slug) {
		$slugSql = $slug ? 'slug = ?' : '1';
		$sql = "
		SELECT *
		FROM content_rm
		WHERE
		type = 'page' AND
		$slugSql AND
		published <= NOW()
		ORDER BY updated DESC
		;
		";
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($slug));
	
		return $res;
	}
	
	/**
	 *
	 * Skriver ut vald blog eller valda bloggar
	 */
	public function printPage($res) {
		$thePage = null;
	
		if(isset($res[0])) {
			foreach($res as $c) {
				// Sanitize content before using it.
				$title  = htmlentities($c->title, null, 'UTF-8');
				$DATA   = $this->filter->doFilter(htmlentities($c->DATA, null, 'UTF-8'), $c->FILTER);
				 
				//Print
				$thePage .= <<<EOD
                <section>
                <article>
                <header>
                <h1>{$title}</h1>
                <p>{$c->published}</p>
                </header>
				{$DATA}
				<br><br>
                <footer>
                </footer
                </article>
                </section>
EOD;
			}
		} else {
			die('Det finns inget innehåll med en sådan slug.');
		}
		return $thePage;
	}
}
