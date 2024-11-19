<?php

/**
 * 
 */
class SnWorkTimetable extends CBitrixComponent {
	
	public static function getDatesList ($filter) {
		$period = new DatePeriod(
			new DateTime($filter['DATE_from']),
			new DateInterval('P1D'),
			new DateTime($filter['DATE_to'])
		);

		$dates = array();
		foreach ($period as $key => $value) {
			$dates[] = $value->format('d.m.Y');     
		}
		return array_reverse($dates);
	}

}