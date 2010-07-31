#!/usr/bin/php5
<?php

class purpleLogAnalyzer {

	const GREP_FOR_CHATLINES = 'grep -E -v "hat sich a[bn]gemeldet|ist (nicht mehr )?inaktiv|Conversation with"';
	protected $allChatPartners = null;
	protected $daysOfPartner = array();
	protected $monthsOfPartner = array();
	
	public function keysLinesPerMonthPerPartner() {
		$allMonths = $this->getCumulatedMonths();
		$headlines = array_merge(array('Alias', 'Gesamt', 'Anzahl Monate', 'Zeilen pro Monat'), $allMonths);
		return $headlines;
	}

	public function getLinesPerMonthPerPartner() {
		$monthsPerChatPartner = $this->getMonthsOfChatpartners();
		$allMonths = $this->getCumulatedMonths();
		$result = array();
		foreach ($monthsPerChatPartner as $chatpartner => $chatPartnerMonths) {
			$monthCount = $this->getMonthCountForUser($chatpartner);
			$totalLines = $this->getTotalLinesWithPartner($chatpartner);
			$result[$chatpartner] = array(
				'name' => $this->getChatpartnerName($chatpartner),
				'total' => $totalLines,
				'monthCount' => $monthCount,
				'linesPerMonth' => round($totalLines/$monthCount)
			);
			foreach ($allMonths as $month) {
				if (array_search($month, $chatPartnerMonths) === false) {
					$result[$chatpartner][$month] = 0;
				} else {
					$result[$chatpartner][$month] = $this->getLinesPerPartnerAndMonth($chatpartner, $month);
				}
			}
		}
		foreach($result as $key => $row) {
			$total[$key] = $row['total'];
		}
		array_multisort($total, SORT_DESC, $result);
		return $result;
	}

	protected function getMonthCountForUser($partner) {
		$months = $this->getMonthsOfPartner($partner);
		return count($months);
	}

	protected function getLinesPerPartnerAndMonth($chatpartner, $month) {
		if (isset($this->linesPerPartnerAndMonth[$chatpartner][$month]) === false) {
			$cmd = "cat " . $chatpartner . "/" . $month . "* | " . self::GREP_FOR_CHATLINES . " | wc -l";
			exec($cmd, $result);
			$this->linesPerPartnerAndMonth[$chatpartner][$month] = $result[0];
		}
		return $this->linesPerPartnerAndMonth[$chatpartner][$month];
	}

	protected function getTotalLinesWithPartner($chatpartner) {
		$months = $this->getMonthsOfPartner($chatpartner);
		$result = 0;
		foreach ($months as $month) {
			$result += $this->getLinesPerPartnerAndMonth($chatpartner, $month);
		}
		return $result;
	}

	protected function getChatpartnerName($chatpartner) {
		$cmd = "cat ../../../blist.xml | grep " . $chatpartner . " -A 1 | grep alias";
		exec($cmd, $result);
		preg_match('/<alias>(.*)<\/alias>/', $result[0], $matches);
		if ($matches[1] === null) {
			return $chatpartner;
		}
		return $matches[1];
	}

	protected function getCumulatedMonths() {
		$return = array();
		$monthsPerPartner = $this->getMonthsOfChatpartners();
		foreach ($monthsPerPartner as $months) {
			$return = array_merge($return, array_flip($months));
		}
		$return = array_keys($return);
		sort($return);
		return $return;
	}

	protected function getMonthsOfChatpartners(array $chatPartners = array()) {
		if (count($chatPartners) === 0) {
			$chatPartners = $this->getChatPartners();
		}
		$return = array();
		foreach($chatPartners as $chatPartner) {
			$return[$chatPartner] = $this->getMonthsOfPartner($chatPartner);
		}
		return $return;
	}

	protected function getMonthsOfPartner($chatPartner) {
		if (isset($this->monthsOfPartner[$chatPartner]) === false) {
			$days = $this->getDaysOfChatPartner($chatPartner);
			$months = array();
			foreach ($days as $day) {
				$months[] = substr($day, 0, 7);
			}
			$this->monthsOfPartner[$chatPartner] = array_unique($months);
		}
		return $this->monthsOfPartner[$chatPartner];
	}

	protected function getDaysOfChatPartner($partner) {
		if ($this->daysOfPartner[$partner] === null) {
			$files = scandir($partner);
			$days = array();
			foreach ($files as $fileName) {
				if (
					$fileName === '.' 
					|| $fileName === '..'
				) {
					continue;
				}
				$days[] = substr($fileName, 0, 10);
			}
			$this->daysOfPartner[$partner] = array_unique($days);
		}
		return $this->daysOfPartner[$partner];
	}

	protected function getChatPartners() {
		if ($this->allChatPartners === null) {
			$allChatPartners = scandir('.');
			$information = array();
			foreach ($allChatPartners as $chatPartner) {
				if (
					$chatPartner === '.' 
					|| $chatPartner === '..'
					|| $chatPartner === '.system'
					|| strpos($chatPartner, 'chat') !== false
				) {
					continue;
				}
				$this->allChatPartners[] = $chatPartner;
			}
		}
		return $this->allChatPartners;
	}

}

$analyzer = new purpleLogAnalyzer();
$result = $analyzer->getLinesPerMonthPerPartner();
$headlines = $analyzer->keysLinesPerMonthPerPartner();
foreach ($headlines as $headline) {
	echo $headline . ";";
}
echo "\n";
foreach ($result as $line) {
	foreach($line as $field) {
		echo $field . ';';
	}
	echo "\n";
}
