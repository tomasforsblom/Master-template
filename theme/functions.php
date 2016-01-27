<?php
/**
 * Theme related functions. 
 *
 */

/**
 * Get title for the webpage by concatenating page specific title with site-wide title.
 *
 * @param string $title for this page.
 * @return string/null wether the favicon is defined or not.
 */
function get_title($title) {
  global $master;
  return $title . (isset($master['title_append']) ? $master['title_append'] : null);
}

/**
 * 
 * @param $menu - menyn som ska skapas genom funktionen
 * @return - det som ska skrivas ut på webbplatsen
 */
function generateMenu($menu)
{
	$html = "<nav class='navbar'>\n";
	foreach($menu as $key => $item) {
		$selected = (isset($_GET['p'])) && $_GET['p'] == $key ? 'selected' : null;
		$html .= "<a href='{$item['url']}' class='{$selected}'>{$item['text']}</a>\n";
	}
	$html .= "</nav>\n";
	return $html;
};
