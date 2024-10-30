<?php

/*
 Plugin Name: bib3html
 Plugin URI: http://wordpress.org/extend/plugins/bib3html/
 Description: bib3html is a refined fork from bib2html (by tango). It provides a multi-purpose feature to add bibtex entries formatted as HTML in wordpress pages and posts. The input data is a local or remote bibtex file and the output is template based, dynamic HTML content.
 Version: 0.9.4
 Author: Alexander Dümont for Digipolis, Sergio Andreozzi
 */


/*
	Copyright 2006-2007	Sergio Andreozzi  (email : sergio <DOT> andreozzi <AT> gmail <DOT> com)
	Copyright 2011		Alexander Dümont for Digipolis (email : alexander <DOT> duemont <AT> googlemail <DOT> com)

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

include_once(dirname(__FILE__) . '/Bib3Html.FilterHandler.php');
include_once(dirname(__FILE__) . '/Bib3Html.EntrySetHandler.php');


class Bib3Html {
	public $allowedOptionKeys = array("file","allow","deny","key","value","sort","contains");
	public $filterHandler = array();
	private $templateFile;


	private static function parseEntries($data) {
		$parse = NEW PARSEENTRIES();
		$parse->expandMacro = TRUE;
		$parse->fieldExtract = TRUE;
		$parse->removeDelimit = TRUE;
		$parse->loadBibtexString($data);
		$parse->extractEntries();
		list($preamble, $strings, $entries) = $parse->returnArrays();
		return $entries;
	}


	public function generateHtml($data, $options=array()) {
		$OSBiBPath = dirname(__FILE__) . '/OSBiB/';
		include_once($OSBiBPath.'format/bibtexParse/PARSEENTRIES.php');
		include_once($OSBiBPath.'format/BIBFORMAT.php');
		include_once(dirname(__FILE__) . '/TemplatePower/class.TemplatePower.inc.php');
	
		// parse the content of bib string and generate associative array with valid entries
		$entries = $this->parseEntries($data);
	
		/* Format the entries array  for html output */
		$bibformat = NEW BIBFORMAT($OSBiBPath, TRUE); // TRUE implies that the input data is in bibtex format
		$bibformat->cleanEntry=TRUE; // convert BibTeX (and LaTeX) special characters to UTF-8
		list($info, $citation, $styleCommon, $styleTypes) = $bibformat->loadStyle($OSBiBPath."styles/bibliography/", "IEEE");
		$bibformat->getStyle($styleCommon, $styleTypes);
	
		
		
		// apply filters
		$entries = $this->filterEntries($entries, $options);
	
		//   $citations = '<dl>';
		$tpl = new TemplatePower($this->templateFile);
		$tpl->prepare();
		foreach ($entries as $entry) {
			// Get the resource type ('book', 'article', 'inbook' etc.)
			$resourceType = $entry['bibtexEntryType'];
	
			//  adds all the resource elements automatically to the BIBFORMAT::item array
			$bibformat->preProcess($resourceType, $entry);
	
	
			// key
			$bibkey = $entry['bibtexCitation'];
			
			$biblogo = '<a href="#' . $bibkey . '" onclick="Effect.toggle(\''. $bibkey . "','appear'); return false\">bibtex</a>";
				
			// get the formatted resource string ready for printing to the web browser
			// the str_replace is used to remove the { } parentheses possibly present in title
			// to enforce uppercase, TODO: check if it can be done only on title
			$tpl->newBlock("bibtex_entry");
			$tpl->assign("year", $entry['year']);
			$tpl->assign("type", $entry['bibtexEntryType']);
			$tpl->assign("pdf", $this->toDownload($entry));
			$tpl->assign("key", substr(md5($bibkey.rand()), 0, 8));
			$tpl->assign("entry", str_replace(array('{', '}'), '', $bibformat->map()));
			$tpl->assign("bibtex", $this->formatBibtex($entry['bibtexEntry']));
		}
		 
		return $tpl->getOutputContent();
	}


	private function filterEntries($entries, $options) {
		$out = array();
		
		// select legit entries
		foreach($entries as $entry) {
			$keep = true;
			foreach($options as $key=>$value) {
				if(
					in_array($key, $this->allowedOptionKeys)
					&& ($handler = $this->filterHandler[$key])
					&& ($handler instanceof Bib3Html_FilterHandler)
					&& !($handler->isLegit($entry, $value))
				) {
					$keep = false;
					break;
				}
			}
			if($keep)
			 $out[] = $entry;
		}
		
		// manipulate entry-set
		foreach($options as $key=>$value) {
			if(
				in_array($key, $this->allowedOptionKeys)
				&& ($handler = $this->filterHandler[$key])
				&& ($handler instanceof Bib3Html_EntrySetHandler)
			) {
				$out = $handler->change($out, $value);
			}
		}
		return $out;
	}
	
	
	public function __construct($templateFile) {
		$this->templateFile = $templateFile;
		$this->filterHandler["allow"] = new Bib3Html_FilterHandler_Allow();
		$this->filterHandler["deny"] = new Bib3Html_FilterHandler_Deny();
		$this->filterHandler["value"] = new Bib3Html_FilterHandler_Value();
		$this->filterHandler["contains"] = new Bib3Html_FilterHandler_Contains();
		$this->filterHandler["key"] = new Bib3Html_FilterHandler_Key();
		$this->filterHandler["sort"] = new Bib3Html_EntrySetHandler_AlphaNumSort();
	}

	private static function toDownload($entry) {
		if(array_key_exists('url',$entry)){
			$string = " <a href='" . $entry['url'] . "' title='Go to document'><img src='" . get_bloginfo('wpurl') . "/wp-content/plugins/bib3html/img/external.png' width='10' height='10' alt='Go to document' /></a>";
			return $string;
		}
		return '';
	}


	
	public function findBibtexTags($myContent) {
		$out = array();
		preg_match_all("/\[\s*bibtex\s+(.*?)]/U", $myContent, $bibtags, PREG_SET_ORDER);
		foreach($bibtags as $bibtag) {
			$entry = array();
			preg_match_all("/(".implode("|", $this->allowedOptionKeys).")=[\"']([^\"']+)[\"']/U", $bibtag[1], $arrOptions, PREG_SET_ORDER);
			foreach($arrOptions as $option) {
				list(,$key,$val) = $option;
				$entry[$key] = $val;
			}
			if($entry["file"])
			 $out[$bibtag[0]] = $entry;
		}
		return $out;
	}
	
	// this function formats a bibtex code in order to be readable
	// when appearing in the modal window
	private static function formatBibtex($entry){
		$order = array("},");
		$replace = "},<br />&nbsp;";
	
		$entry = preg_replace('/\s\s+/', ' ', trim($entry));
		$new_entry = str_replace($order, $replace, $entry);
		$new_entry = preg_replace('/\},?\s*\}$/', "}\n}", $new_entry);
		return $new_entry;
	}
}
?>