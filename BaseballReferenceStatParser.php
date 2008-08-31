<?php

class PlayerStats {
	var $time;
	var $year, $age, $games, $ab, $runs, $hits, $doubles, $triples, $hr, $rbi, $sb, $cs, $bb, $so, $ba, $obp, $slg, $opsPlus;
}

/*
StatFetcher

Downloads a hitter's stats from baseball-reference.com and parses them out.

usage:
$fetcher = new StatFetcher('puntoni01', 2008);
$stats = $fetcher->getStats();

*/
class StatFetcher {
	var $url;
	var $htmlLines = null;

	function StatFetcher($playerCode, $year) {
		$this->playerCode = $playerCode;
		$this->year = $year;

		// build the URL based on the format of the baseball-reference URLs
		$this->url = 'http://www.baseball-reference.com/' . $playerCode[0] . '/' . $playerCode . '.shtml';
	}

	/**
	Download the content from the page and split it into an array of lines.
	*/
	function loadLines() {
		if ($this->htmlLines == null) {
			// use curl because Dreamhost doesn't allow URL file-access
			$c = curl_init();
			curl_setopt($c, CURLOPT_URL, $this->url);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			$contents = curl_exec($c);
			curl_close($c);
			$this->htmlLines = explode("\n", $contents);
		}
	}

	/**
	Given a line containing a year's stats, parse them into a PlayerStats object.
	*/
	function parsePlayerStats($line) {
		$arr = split(' ', $line);
		$numbers = array();
		foreach ($arr as $elem) {
			if (is_numeric($elem)) {
				$numbers[] = $elem;
			}
		}

		$stats = new PlayerStats();
		$stats->time = time();

		$stats->year = $numbers[0];
		$stats->age = $numbers[1];
		$stats->games = $numbers[2];
		$stats->ab = $numbers[3];
		$stats->runs = $numbers[4];
		$stats->hits = $numbers[5];
		$stats->doubles = $numbers[6];
		$stats->triples = $numbers[7];
		$stats->hr = $numbers[8];
		$stats->rbi = $numbers[9];
		$stats->sb = $numbers[10];
		$stats->cs = $numbers[11];
		$stats->bb = $numbers[12];
		$stats->so = $numbers[13];
		$stats->ba = $numbers[14];
		$stats->obp = $numbers[15];
		$stats->slg = $numbers[16];
		$stats->opsPlus = $numbers[17];

		return $stats;
	}

	/**
	Look through the baseball-reference page for the stats for the proper year and return them.
	*/
	function getStats() {
		$this->loadLines();

		$batStat = false;
		foreach ($this->htmlLines as $line) {
			if (strcasecmp('<div id="batStats">', trim($line)) == 0) {
				$batStat = true;
				continue;
			}

			if ($batStat) {
				if (preg_match("/year=\"{$this->year}\"/", $line)) {
					return $this->parsePlayerStats($line);
				}
			}
		}
	}
}

?>
