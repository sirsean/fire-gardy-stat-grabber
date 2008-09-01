<?php
/*
Plugin Name: FireGardy Stat Grabber
Plugin URI: http://stat-grabber.firegardy.com
Description: Download a player's stats from Baseball-Reference.com and display them on the page.
Version: 0.1.2a
Author: Sean Schulte
Author URI: http://seancode.blogspot.com
*/

// we need to use the parser
require_once(dirname(__FILE__) . '/BaseballReferenceStatParser.php');

/**
Take a player name, player code, year, the player type, and an optional set of stats to display, and show the stats for that player.
The playerType parameter must be either 'hitter' or 'pitcher' (if it's omitted, it defaults to 'hitter').
The statsToDisplay parameter is an array, whose contents are either strings or arrays of strings. Each element in the array is displayed on a single line.
The default for hitters is:
	array(
		'tripleSlash',
		array('ab', 'hr'),
		'opsPlus'
	);
The default for pitchers is:
	$statsToDisplay = array(
		'winLoss',
		array('ip', 'k', 'bb'),
		array('era', 'eraPlus')
		);
*/
function fire_gardy_stat_grabber($playerName, $playerId, $year, $playerType='hitter', $statsToDisplay=null) {
	// build the player db code
	$playerDbCode = $playerId . '_' . $year . '_' . date('m-d-Y');

	// check to see if the stat line already exists
	$playerStats = get_option($playerDbCode);

	// download a new one if this is the first of the day or it hasn't been downloaded for over 4 hours
	if (!$playerStats || (time() - $playerStats->time > 4*3600)) {
		$fetcher = new StatFetcher($playerId, $year, $playerType);
		$playerStats = $fetcher->getStats();

		// save it to the db
		update_option($playerDbCode, $playerStats);
	}

	// set up the default stats
	if (!$statsToDisplay) {
		if ($playerType == 'hitter') {
			$statsToDisplay = array(
				'tripleSlash',
				array('ab', 'hr'),
				'opsPlus'
				);
		} else if ($playerType == 'pitcher') {
			$statsToDisplay = array(
				'winLoss',
				array('ip', 'k', 'bb'),
				array('era', 'eraPlus')
				);
		}
	}

	// display the stats
	echo '<div class="fg_br_grabber">';
	echo '<strong>' . $playerName . ', ' . $year . '</strong>';
	echo '<br />';
	foreach ($statsToDisplay as $stat) {
		if (is_array($stat)) {
			$comma = false;
			foreach ($stat as $s) {
				if ($comma) {
					echo ', ';
				} else {
					$comma = true;
				}
				echo $playerStats->getString($s);
			}
		} else {
			echo $playerStats->getString($stat);
		}
		echo '<br />';
	}
	echo '</div>';
}

function fg_baseball_reference_css() {
	echo "
	<style type='text/css'>
		div.fg_br_grabber {
			padding-bottom: 5px;
			padding-top: 5px;
		}
	</style>
	";
}

add_action('wp_footer', 'fg_baseball_reference_css');

?>
