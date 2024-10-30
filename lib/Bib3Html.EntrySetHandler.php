<?php

/*
 Plugin Name: bib3html
 Plugin URI: http://wordpress.org/extend/plugins/bib3html/
 Description: bib3html is a refined fork from bib2html (by tango). It provides a multi-purpose feature to add bibtex entries formatted as HTML in wordpress pages and posts. The input data is a local or remote bibtex file and the output is template based, dynamic HTML content.
 Version: 0.9.4
 Author: Alexander Dümont
 */


/*	Copyright 2011		Alexander Dümont  (email : alexander <DOT> duemont <AT> googlemail <DOT> com)

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

interface Bib3Html_EntrySetHandler {
	public function change($entries, $processingValue);
}

	
class Bib3Html_EntrySetHandler_AlphaNumSort implements Bib3Html_EntrySetHandler {
	private $sortKey;
	
	function sort($a, $b) {
		$f1 = $a[$this->sortKey];
		$f2 = $b[$this->sortKey];
		return ($f1 == $f2) ? 0 : ($f1 < $f2) ? -1 : 1;
	}
	
	public function change($entries, $filterValue) {
		$filterValArr = explode(",", $filterValue);
		$this->sortKey = trim($filterValArr[0]);
		usort($entries, array($this, "sort"));
		if($filterValArr[1]=="desc") {
			$entries = array_reverse($entries);
		}
		return $entries;
	}
}