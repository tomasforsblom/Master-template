<?php

class CBlog {
    
	private $db;
	private $filter;
	
	public function __construct($db) {
		$this->db = $db;
		$this->filter = new CTextFilter();
	}
	
    /**
     * Get ONE blog
     */
	public function getBlog($slug) {
		$slugSql = $slug ? 'slug = ?' : '1';
		$sql = "
		SELECT *
		FROM content_rm
		WHERE
		type = 'post' AND
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
	public function printBlog($res) {
		$theBlogs = null;
		
		if(isset($res[0])) {
			foreach($res as $c) {
				// Sanitize content before using it.
				$title  = htmlentities($c->title, null, 'UTF-8');
				$DATA   = $this->filter->doFilter(htmlentities($c->DATA, null, 'UTF-8'), $c->FILTER);
	            
				//Print
				$theBlogs .= <<<EOD
                <section>
                <article>
                <header>
                <h1><a href='content_blog_full.php?slug={$c->slug}'>{$title}</a></h1>
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
		return $theBlogs;
		}
	
	/**
	 * Show a subset of all blogs
	 */
	public function showSubsetofBlogs($res) {
		$theBlogs = null;
		
		if(isset($res[0])) {
			foreach($res as $c) {
				// Sanitize content before using it.
				$title  = htmlentities($c->title, null, 'UTF-8');
				$DATA   = $this->filter->doFilter(htmlentities($c->DATA, null, 'UTF-8'), $c->FILTER);
				
				if (strlen($DATA) > 300) {
					$trimstring = substr($DATA, 0, 300). "...<a href='content_blog_full.php?slug=$c->slug'><br> Läs mer...</a>";
				} else {
					$trimstring = $DATA;
				}
				
				//Print
				$theBlogs .= <<<EOD
                <section>
                <article>
                <header>
                <h1><a href='content_blog_full.php?slug={$c->slug}'>{$title}</a></h1>
                <p>{$c->published} | Kategori: "<a href='content_blog_category.php?category={$c->category}'>{$c->category}</a>"
                </header>
				{$trimstring}
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
		return $theBlogs;
   }
   
   /**
    * Shows the chosen category
    */
   public function showChosenCategory($category) {
   
   	$tr = null;
   	if($category) {
   		//find all blogs that matches a specific category and put them i $res
   		$sql = 'SELECT * from content_rm WHERE category=?';	
   		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($category));
   			
   	    $theBlogs = null;
		if(isset($res[0])) {
			foreach($res as $c) {
				// Sanitize content before using it.
				$title  = htmlentities($c->title, null, 'UTF-8');
				$DATA   = $this->filter->doFilter(htmlentities($c->DATA, null, 'UTF-8'), $c->FILTER);
				
				if (strlen($DATA) > 300) {
					$trimstring = substr($DATA, 0, 300). "...<a href='content_blog_full.php?slug=$c->slug'><br> Läs mer...</a>";
				} else {
					$trimstring = $DATA;
				}
				
				//Print
				$theBlogs .= <<<EOD
                <section>
                <article>
                <header>
                <h1><a href='content_blog_full.php?slug={$c->slug}'>{$title}</a></h1>
                <p>{$c->published} | Kategori: {$c->category}</p>
                </header>
				{$trimstring}
				<br><br>
                <footer>
                </footer
                </article>
                </section>
EOD;
			}
			return $theBlogs;
		}
   	
   }
   }
}