<?php

class PlayerStats {
	var $time;
	var $year, $age, $games;
}

class PitcherStats extends PlayerStats {
    var $w, $l, $winPerc, $era, $gs, $gf, $cg, $sho, $sv, $ip, $h, $r, $er, $hr, $bb, $k, $hbp, $bf, $eraPlus, $whip, $h9, $bb9, $k9, $kbb, $hr9;

	/**
	Translate a given stat string into a printable string.
	Acceptable inputs:
		year, age, g, w, l, gs, cg, sho, gf, sv, ip, h, r, er, hr, bb, so, hbp, wp, bfp, ibb, bk, era, eraPlus, whip;
	*/
	function getString($stat) {
		switch($stat) {
			case 'year':
				return $this->year;
			case 'age':
				return $this->age . ' years';
			case 'g':
				return $this->games . ' G';
			case 'gs':
				return $this->gs . ' GS';
			case 'w':
				return $this->w . ' W';
			case 'l':
				return $this->l . ' L';
			case 'winLoss':
				return $this->w . '-' . $this->l;
			case 'cg':
				return $this->cg . ' CG';
			case 'sho':
				return $this->sho . ' SHO';
			case 'gf':
				return $this->gf . ' GF';
			case 'sv':
				return $this->sv . ' SV';
			case 'ip':
				return $this->ip . ' IP';
			case 'h':
				return $this->h . ' H';
			case 'r':
				return $this->r . ' R';
			case 'er':
				return $this->er . ' ER';
			case 'hr':
				return $this->hr . ' HR';
			case 'bb':
				return $this->bb . ' BB';
			case 'k':
				return $this->k . ' K';
			case 'hbp':
				return $this->hbp . ' HBP';
			case 'bp':
				return $this->bp . ' BFP';
			case 'era':
				return $this->era . ' ERA';
			case 'eraPlus':
				return $this->eraPlus . ' ERA+';
			case 'whip':
				return $this->whip . ' WHIP';
			default:
				return '';
		}
	}
}

class HitterStats extends PlayerStats {
	var $ab, $runs, $hits, $doubles, $triples, $hr, $rbi, $sb, $cs, $bb, $so, $ba, $obp, $slg, $opsPlus;

	/**
	Translate a given stat name into a printable string
	Accepted stats:
		tripleSlash, ba, obp, slg, ops, ab, hr, opsPlus, age, year, g, r, h, 2B, 3B, rbi, sb, cs, bb, k
	*/
	function getString($stat) {
		switch($stat) {
			case 'tripleSlash':
				return $this->ba . '/' . $this->obp . '/' . $this->slg;
			case 'ba':
				return $this->ba . ' BA';
			case 'obp':
				return $this->obp . ' OBP';
			case 'slg':
				return $this->slg . ' SLG';
			case 'ops':
				return ($this->obp + $this->slg) . ' OPS';
			case 'ab':
				return $this->ab . ' AB';
			case 'hr':
				return $this->hr . ' HR';
			case 'opsPlus':
				return $this->opsPlus . ' OPS+';
			case 'age':
				return $this->age . ' years';
			case 'year':
				return $this->year;
			case 'g':
				return $this->games . ' G';
			case 'r':
				return $this->runs . ' R';
			case 'h':
				return $this->hits . ' H';
			case '2b':
				return $this->doubles . ' 2B';
			case '3b':
				return $this->triples . ' 3B';
			case 'rbi':
				return $this->rbi . ' RBI';
			case 'sb':
				return $this->sb . ' SB';
			case 'cs':
				return $this->cs . ' CS';
			case 'bb':
				return $this->bb . ' BB';
			case 'k':
				return $this->so . ' K';
			default:
				return '';
		}
	}
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
	var $playerType;

	function StatFetcher($playerCode, $year, $playerType='hitter') {
		$this->playerCode = $playerCode;
		$this->year = $year;

		// build the URL based on the format of the baseball-reference URLs
		$this->url = 'http://www.baseball-reference.com/players/' . $playerCode[0] . '/' . $playerCode . '.shtml';

		$this->playerType = $playerType;
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
	Given a line containing a year's stats, parse it into a PitcherStats object.
	*/
	function parsePitcherStats($numbers) {
		$stats = new PitcherStats();
		$stats->time = time();

		$stats->year = $numbers[0];
		$stats->age = $numbers[1];
		$stats->w = $numbers[2];
		$stats->l = $numbers[3];
		$stats->winPerc = $numbers[4];
		$stats->era = $numbers[5];
		$stats->games = $numbers[6];
		$stats->gs = $numbers[7];
		$stats->gf = $numbers[8];
		$stats->cg = $numbers[9];
		$stats->sho = $numbers[10];
		$stats->sv = $numbers[11];
		$stats->ip = $numbers[12];
		$stats->h = $numbers[13];
		$stats->r = $numbers[14];
        $stats->er = $numbers[15];
		$stats->hr = $numbers[16];
		$stats->bb = $numbers[17];
		$stats->k = $numbers[18];
		$stats->hbp = $numbers[19];
		$stats->bf = $numbers[20];
		$stats->eraPlus = $numbers[21];
		$stats->whip = $numbers[22];
		$stats->h9 = $numbers[23];
		$stats->bb9 = $numbers[24];
		$stats->k9 = $numbers[25];
        $stats->kbb = $numbers[26];
        $stats->hr9 = $numbers[27];

		return $stats;
	}

	/**
	Given a line containing a year's stats, parse them into a HitterStats object.
	*/
	function parseHitterStats($numbers) {
		$stats = new HitterStats();
		$stats->time = time();

		$stats->year = $numbers[0];
		$stats->age = $numbers[1];
		$stats->games = $numbers[2];
        $stats->pa = $numbers[3];
		$stats->ab = $numbers[4];
		$stats->runs = $numbers[5];
		$stats->hits = $numbers[6];
		$stats->doubles = $numbers[7];
		$stats->triples = $numbers[8];
		$stats->hr = $numbers[9];
		$stats->rbi = $numbers[10];
		$stats->sb = $numbers[11];
		$stats->cs = $numbers[12];
		$stats->bb = $numbers[13];
		$stats->so = $numbers[14];
		$stats->ba = $numbers[15];
		$stats->obp = $numbers[16];
		$stats->slg = $numbers[17];
        $stats->ops = $numbers[18];
		$stats->opsPlus = $numbers[19];
        $stats->tb = $numbers[20];
        $stats->gidp = $numbers[21];
        $stats->hbp = $numbers[22];
        $stats->sh = $numbers[23];
        $stats->sf = $numbers[24];
        $stats->ibb = $numbers[25];

		return $stats;
	}

	/**
	Look through the baseball-reference page for the stats for the proper year and return them.
	*/
	function getStats() {
		$this->loadLines();

		$batStat = false;
        $stats = array();
		foreach ($this->htmlLines as $line) {
            if (($this->playerType == 'hitter') && preg_match("/id=\"batting_standard\.{$this->year}\"/", $line)) {
				$batStat = true;
				continue;
			} else if (($this->playerType == 'pitcher') && preg_match("/id=\"pitching_simple\.{$this->year}\"/", $line)) {
				$pitchStat = true;
				continue;
			}

			if ($batStat) {
                if (strcasecmp('</tr>', trim($line)) == 0) {
                    $batStat = false;
                    $hitterStats = $this->parseHitterStats($stats);
                    return $hitterStats;
                } else {
                    $res = preg_match("/<td [^\>]*\><?s?t?r?o?n?g?>?<?e?m?>?([\.\d]+)<?\/?e?m?>?<?\/?s?t?r?o?n?g?>?\<\/td\>/", $line, $matches);
                    if ($res) {
                        array_push($stats, $matches[1]);
                    }
                }
			}
			if ($pitchStat) {
                if (strcasecmp('</tr>', trim($line)) == 0) {
                    $pitchStat = false;
                    $pitcherStats = $this->parsePitcherStats($stats);
                    return $pitcherStats;
                } else {
                    $res = preg_match("/<td [^>]*><?s?t?r?o?n?g?>?<?e?m?>?([\.\d]+)<?\/?e?m?>?<?\/?s?t?r?o?n?g?>?<\/td>/", $line, $matches);
                    if ($res) {
                        array_push($stats, $matches[1]);
                    }
                }
			}
		}
	}
}

?>
