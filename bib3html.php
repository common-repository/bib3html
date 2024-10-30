<?php

/*
 Plugin Name: bib3html
 Plugin URI: http://wordpress.org/extend/plugins/bib3html/
 Description: bib3html is a refined fork from bib2html (by tango). It provides a multi-purpose feature to add bibtex entries formatted as HTML in wordpress pages and posts. The input data is a local or remote bibtex file and the output is template based, dynamic HTML content.
 Version: 0.9.4
 Author: Alexander Dümont for Digipolis [digipolis.ag-nbi.de], Sergio Andreozzi
 */


/*
	Copyright 2006-2007	Sergio Andreozzi  (email : sergio <DOT> andreozzi <AT> gmail <DOT> com)
	Copyright 2011		Alexander Dümont for Digipolis [digipolis.ag-nbi.de] (email : alexander <DOT> duemont <AT> googlemail <DOT> com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


include_once("lib/Bib3Html.inc.php");
include_once("lib/Bib3Html.HtmlCache.php");

function bib3html($myContent) {

	// search for all [bibtex filename] tags and extract the filename
	$templateFile = dirname(__FILE__) . '/bibentry-html.tpl'; 
	$bib3Html = new Bib3Html($templateFile);
	$htmlCache = new Bib3Html_HtmlCache();
	
	$bibtex = $bib3Html->findBibtexTags($myContent);
	if ($bibtex) {
		foreach ($bibtex as $raw=>$options) {
			$cachedHtml = $htmlCache->get($raw);
			if($cachedHtml) {
				$myContent = str_replace($raw, $cachedHtml, $myContent);
			}
			else {
				// check if bib file is URL
				$isUrl = strpos($options["file"], "http://");
				if ($isUrl !== false) $options["file"] = getCachedBibtex($options["file"]);
				$bibFile = dirname(__FILE__)."/data/".$options["file"];
	
				if (file_exists($bibFile)) {
					$bibContents = file_get_contents($bibFile);
					if (!empty($bibContents)) {
						// if bibtex file identified and opened, then convert to html
						$htmlbib = $bib3Html->generateHtml($bibContents, $options);
						$htmlCache->set($raw, $htmlbib);
						$myContent = str_replace($raw, $htmlbib, $myContent);
					} else {
						$myContent = str_replace($raw, $options["file"] . ' bibtex file empty', $myContent);
					}
				} else {
					$myContent = str_replace($raw, $options["file"] . ' bibtex file not found', $myContent);
				}
			}
		}
	}

	return $myContent;
}



/* Returns filename of cached version of given url  */
function getCachedBibtex($url) {
	// check if cached file exists
	$name = substr($url, strrpos($url, "/")+1);
	$file = dirname(__FILE__) . "/data/" . $name . ".cached.bib";

	// check if file date exceeds 60 minutes
	if (! (file_exists($file) && (filemtime($file) + 3600 > time())))  {
		// not returned yet, grab new version
		$f=fopen($file,"wb");
		if ($f) {
			fwrite($f,file_get_contents($url));
			fclose($f);
		} else echo "Failed to write file" . $file . " - check directory permission according to your Web server privileges.";
	}
	 
	return $name.".cached.bib";
}


/*
 * not used anymore?
 */
function bib3html_head()
{
	if (!function_exists('wp_enqueue_script')) {
		echo "\n" . '<script src="'.  get_bloginfo('wpurl') . '/wp-content/plugins/bib3html/js/jquery.js"  type="text/javascript"></script>' . "\n";
		echo '<script src="'.  get_bloginfo('wpurl') . '/wp-content/plugins/bib3html/js/bib3html.js"  type="text/javascript"></script>' . "\n";
	}
	echo "<style type=\"text/css\">
a.bibtex_toggle {
	font-size:0px;
	width:16px;
	height:16px;
	padding:10px;
	background-image:url('".  get_bloginfo('wpurl') . "/wp-content/plugins/bib3html/img/bibtex.png');
	background-repeat:no-repeat;
}
div.bibtex {
    display: none;
    border: 1px dotted grey;
    background-color:#f0f0f0;
    margin: 10px;
    padding: 5px;
}</style>";

}
add_action('wp_head', 'bib3html_head');

/*
 * not used anymore?
 */
function bib3html_init() {
	if (function_exists('wp_enqueue_script')) {
		wp_register_script('bib3html',  get_bloginfo('wpurl') . '/wp-content/plugins/bib3html/js/bib3html.js', array('jquery'), '0.7');
		wp_enqueue_script('bib3html');
	}
}

add_action('init', 'bib3html_init');
add_filter('the_content', 'bib3html', 1);
?>
