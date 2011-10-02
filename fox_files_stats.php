<?php

// This is a PLUGIN TEMPLATE.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Plugin names should start with a three letter prefix which is
// unique and reserved for each plugin author ("abc" is just an example).
// Uncomment and edit this line to override:
$plugin['name'] = 'fox_files_stats';

// Allow raw HTML help, as opposed to Textile.
// 0 = Plugin help is in Textile format, no raw HTML allowed (default).
// 1 = Plugin help is in raw HTML.  Not recommended.
# $plugin['allow_html_help'] = 1;

$plugin['version'] = '0.1';
$plugin['author'] = 'Riccardo Traverso';
$plugin['author_uri'] = 'http://www.riccardotraverso.it';
$plugin['description'] = 'Print out some statistics about files, like files count, files total size, total downloaded size and total downloaded files.';

// Plugin load order:
// The default value of 5 would fit most plugins, while for instance comment
// spam evaluators or URL redirectors would probably want to run earlier
// (1...4) to prepare the environment for everything else that follows.
// Values 6...9 should be considered for plugins which would work late.
// This order is user-overrideable.
$plugin['order'] = '5';

// Plugin 'type' defines where the plugin is loaded
// 0 = public       : only on the public side of the website (default)
// 1 = public+admin : on both the public and admin side
// 2 = library      : only when include_plugin() or require_plugin() is called
// 3 = admin        : only on the admin side
$plugin['type'] = '0';

// Plugin "flags" signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

$plugin['flags'] = '0';

if (!defined('txpinterface'))
        @include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---
/*
  fox_files_stats is a TextPattern plugin written by Riccardo Traverso (GreyFox) and it's released under
  the terms of the GNU GPL 2.0 license
*/

function fox_files_stats($atts) {
	extract(lAtts(array(
		'downloaded'             =>  '0',
		'category'               =>  '',
		'format'                 =>  'M',
		'size'                   =>  '0'
	), $atts));

	if ($size=='1') {
		if ($downloaded=='0')
			$things = 'sum(size)';
		else
			$things = 'sum(downloads*size)';
	} else {
		if ($downloaded=='0')
			$things = 'count(*)';
		else
			$things = 'sum(downloads)';
	}
	
	if ($category!='')
		$where = 'category=\''.mysql_escape_string($category).'\'';
	else $where='1';

	$stats = safe_row("$things as s", 'txp_file', $where);
	$stats = $stats['s'];

	if ($size=='1')
		switch (strtoupper($format)) {
			case 'G':
				$stats /= 1024;
			case 'M':
				$stats /= 1024;
			case 'K':
				$stats /= 1024;
			default:
				$stats = round($stats,2);
		}

	return $stats;
}

# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---
<h1>Plugin description</h1>
<p>Enabling this plugin you will be able to print out some statistics about the files. Here is the tag list:</p>
<ul><li>fox_files_stats</li></ul>
<h1>&lt;fox_files_stats&gt;</h1>
<h2>description</h2>
<p>This tag is able to print out a few statistics about the files, like the number of files, the number of downloads and so on.</p>
<h2>usage</h2>
<ul>
<li>&lt;txp:fox_files_stats /&gt;<br/>This prints out how many files are available into the website.</li>
<li>&lt;txp:fox_files_stats downloaded="1"/&gt;<br/>This prints out how many downloads have been made.</li>
<li>&lt;txp:fox_files_stats size="1"/&gt;<br/>This prints out the total size (in MB) of the files.</li>
<li>&lt;txp:fox_files_stats size="1" downloaded="1"/&gt;<br/>This prints out how many MB have been downloaded.</li>
</ul>
<h2>arguments</h2>
<ul>
<li><strong>category</strong> - optional, the file category you wish to show statistics about (default is empty, which means "all categories")</li>
<li><strong>downloaded</strong> - optional, tells if to count each file's download or not (default is "0")</li>
<li><strong>format</strong> - optional, specify the format (G for GB, M for MB, K for KB, B for bytes) when size="1" (default is "M")</li>
<li><strong>size</strong> - optional, turns size counting on and off (default is "0")</li>
</ul>
# --- END PLUGIN HELP ---
-->
<?php
}
?>
