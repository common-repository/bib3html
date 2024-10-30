<?php

/*
 Plugin Name: bib3html
 Plugin URI: http://wordpress.org/extend/plugins/bib3html/
 Description: bib3html is a refined fork from bib2html (by tango). It provides a multi-purpose feature to add bibtex entries formatted as HTML in wordpress pages and posts. The input data is a local or remote bibtex file and the output is template based, dynamic HTML content.
 Version: 0.9.4
 Author: Alexander Dümont for Digipolis [digipolis.ag-nbi.de]
 */


/*
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

class Bib3Html_HtmlCache {
	private $cacheDir;
	
	public function __construct($cacheDir=false) {
		$this->cacheDir = $cacheDir ? $cacheDir : dirname(__FILE__) . "/../cache";
	}
	
	public function get($key) {
		// name
		// $key = urlencode($key);
		$key = md5($key);

		// check if cached file exists, younger than a week
		$file = $this->cacheDir . "/" . $key . ".cached.html";
		if(file_exists($file) && (filemtime($file) + 604800 > time())) {
			return file_get_contents($file);
		}
		return null;
	}
	
	public function set($key, $val) {
		// name
		// $key = urlencode($key);
		$key = md5($key);

		// check if cached file exists, younger than a week
		$file = $this->cacheDir . "/" . $key . ".cached.html";

		// check if file date exceeds 60 minutes
		if (! (file_exists($file) && (filemtime($file) + 3600 > time())))  {
			// not returned yet, grab new version
			$f=fopen($file,"wb");
			if ($f) {
				fwrite($f, $val);
				fclose($f);
			}
		}
		return $file;
	}
}

?>