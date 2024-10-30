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



/*
 * interface Bib3Html_FilterHandler
 */
interface Bib3Html_FilterHandler {
	public function isLegit($entry, $filterValue);
}


/*
 * implementations below
 */

class Bib3Html_FilterHandler_Allow implements Bib3Html_FilterHandler {
	public function isLegit($entry, $filterValue) {
		$filterValArr = explode(",", $filterValue);
		$legit = false;
		foreach($filterValArr as $val) {
			if(stristr($entry['bibtexEntryType'], trim($val)) !== FALSE) {
				$legit = true;
				break;
			}
		}
		return $legit;
	}
}

class Bib3Html_FilterHandler_Deny implements Bib3Html_FilterHandler {
	public function isLegit($entry, $filterValue) {
		$filterValArr = explode(",", $filterValue);
		foreach($filterValArr as $val) {
			if(stristr($entry['bibtexEntryType'], trim($val)) !== FALSE)
			 return false;
		}
		return true;
	}
}

class Bib3Html_FilterHandler_Value implements Bib3Html_FilterHandler {
	public function isLegit($entry, $filterValue) {
		$filterValPair = explode(",", $filterValue);
		return $filterValPair && strcmp($entry[trim($filterValPair[0])], trim($filterValPair[1])) == 0;
	}
}

class Bib3Html_FilterHandler_Contains implements Bib3Html_FilterHandler {
	public function isLegit($entry, $filterValue) {
		$filterValPair = explode(",", $filterValue);
		
		// no string input
		if(!$filterValPair)
		 return true;

		// input: key,value
		if(count($filterValPair)>1)
		 return $filterValPair && strpos($entry[trim($filterValPair[0])], trim($filterValPair[1])) !== FALSE;
		
		// input: value
		$val = trim($filterValPair[0]);
		foreach($entry as $row) {
			if(strpos($row, $val) !== FALSE) {
				return true;
			}
		}
		return false;
	}
}

class Bib3Html_FilterHandler_Key implements Bib3Html_FilterHandler {
	public function isLegit($entry, $filterValue) {
		return strcmp(trim($filterValue), $entry['bibtexCitation']) == 0;
	}
}