=== Plugin Name ===
Contributors: tango, alexurus
Donate link: 
Tags: formatting, bibtex
Requires at least: 1.5
Tested up to: 3.2
Stable tag: 0.9.4

bib3html is a refined fork from bib2html written by tango. It enables to add bibtex entries formatted as HTML in wordpress pages and posts. The input data is a local or remote bibtex file and the output is HTML.

== Description ==

If you need to maintain a bibliography in bibtex format and also write a Web page to publish the list of your publications, then bib3html is the right solution for you. 

bib3html is a refined fork from bib2html written by tango. It enables to add bibtex entries formatted as HTML in wordpress pages and posts. The input data is a local or remote bibtex file and the output is HTML.
The entries are formatted by default using the IEEE style. Bibtex source file and a link to the publication are also available from the HTML.
In order to reduce site loading the generated HTML Contents are cached in the cache directory for a defined time. If the cached HTML is out of date it gets reloaded automatically.


Features:

* input data directly from the bibtex text file
* source files can be URL
* automatic HTML generation
* template and cache usage
* easy inclusion in wordpress pages/posts by means of a dedicated tag
* possibility of filtering the bibtex entries based on their type (e.g. "allow", "deny")
* possibility to access the single bibtex entry source code to enable copy&paste (toggle-enabled visualization)
* expose URL of each document (if network-reachable)
* possibility of editing the bibtex file directly from the wordpress administration page

The bib3html plugin has been developed and tested under Wordpress 3.2.0

== Installation ==

1. download the zip file and extract the content of the zip file into a local folder
2. local bibtex files should be copied in bib3html/data/ directory
3. upload the folder bib3html into your wp-content/plugins/ directory
4. if using a unix based operation system set chmod to an appropriate state (esp. /cache, /data)
5. log in the wordpress administration page and access the Plugins menu
6. activate bib3html



== Frequently Asked Questions ==

= How can I edit tango's bibtex files? =

If your file is local to the blog installation, you have two options:
- via FTP client with text editor
- via Wordpress Admin interface: Manage->Files->Other Files
-- use wp-content/plugins/bib3html/data/mybibfile.bib as a path

Alternatively, you can maintain your updated biblilography by using systems such as citeulike.org and bibsonomy.org; 
specify the bib file using as a URL (e.g., in citeulike, you should use http://www.citeulike.org/bibtex/user/username)

= How are the entries sorted? =

Since version 0.9, the entries are sorted by year starting from the most
recent; in future revision, I plan to make this configurable by the user

= How can I personlize the HTML rendering? =
The HTML rendering is isolated in a template file called bibentry-html.tpl.
Just change it.

== Screenshots ==

1. This is an example on how to use the tag into a page bib2html-tag.png
2. This is an example of the output from tango's blog bib2html-output.png


== A brief Markdown Example ==

When writing a page/post, you can use the tag [bibtex] as follows:

This is tango's whole list of publications: [bibtex file="mypub.bib"]
If you want to filter the type of bibtex items, you can use one of the attributes allow, deny and key as follows:

This is tango's list of journal articles:
[bibtex file="mypub.bib" allow="article"]

This is tango's list of conference articles and technical reports:
[bibtex file="mypub.bib" allow="inproceedings,techreport"]

This is the rest of tango's publications:
[bibtex file="mypub.bib" deny="article,inproceedings,techreport"]

This is tango's latest conference paper:
[bibtex file="mypub.bib" key="CGW2006"]

This is tango's bibliography maintained at citeulike.org
[bibtex file="http://www.citeulike.org/bibtex/user/username"]

This is tango's bibliography maintained at bibsonomy.org
[bibtex file="http://bibsonomy.org/bib/user/username?items=1000"]
