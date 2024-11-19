<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Engine\Controller;
\Bitrix\Main\Loader::includeModule('sn.rest');


class SnCheckinCheckoutAjaxController extends Controller {

	public static function newWorktimeSaveAction ($work) {
		$add = \Bitrix\Sn\WorkTable::add([
			'DATE_CREATE' => (new \Bitrix\Main\Type\DateTime($work['date'].' '.$work['beginWorktime'], 'd.m.Y H:i:s')),
			'PLAN_ID' => $work['planId'],
			'STATUS' => 'B',
		]);
		\Bitrix\Sn\WorkTable::add([
			'DATE_CREATE' => (new \Bitrix\Main\Type\DateTime($work['date'].' '.$work['closeWorktime'], 'd.m.Y H:i:s')),
			'PLAN_ID' => $work['planId'],
			'STATUS' => 'C',
		]);
		return $add->getErrorMessages();
	}

	public static function getNewWorktimeDataAction ($date, $user) {
		$plan = \Bitrix\Sn\PlanTable::getList([
			'filter' => [
				'USER_ID' => $user['ID'],
				'<=BEGIN_DATE' => (new \Bitrix\Main\Type\DateTime($date, 'd.m.Y')),
				'>=CLOSE_DATE' => (new \Bitrix\Main\Type\DateTime($date, 'd.m.Y')),
			],
			'runtime' => [
				'DEAL' => [
					'data_type' => 'Bitrix\Crm\DealTable',
					'reference' => ['this.DEAL_ID' => 'ref.ID'],
				],
			],
			'select' => [
				'*',
				'DEAL_TITLE' => 'DEAL.TITLE',
			],
		])->fetch();
		return $plan;
	}

	public static function worktimeCheckoutAction ($PLAN_ID, $DATE_CREATE) {
		$re = Bitrix\Sn\WorkTable::add([
			'DATE_CREATE' => new \Bitrix\Main\Type\DateTime($DATE_CREATE, 'd.m.Y H:i:s'),
			'PLAN_ID' => $PLAN_ID,
			'STATUS' => 'C',
		]);
		if($re->isSuccess()) {
			$ID = $re->getId();
			return 'success';
		} else {
			$error = $re->getErrorMessages();
			return var_export($error, true);
		}
	}

	public static function csvGenerationAction ($rows, $datesList, $totalHours, $vocations) {
		$url = '/upload/tmp/checkin_checkout-'.rand().'.csv';
		$path = $_SERVER['DOCUMENT_ROOT'].$url;
		$str = 'ID;PROJECT;USER;';
		
		$str = 'ID;Benutzer;Projekt;Gesamt;';
		foreach ($datesList as $key => $value) {
			$str = $str.$value.';';
		}
		$str = $str.PHP_EOL;
		foreach ($rows as $key => $value) {
			$str = $str.$value['ID'].';';
			$str = $str.$value['NAME'].' '.$value['LAST_NAME'].' '.$value['SECOND_NAME'].';';
			if ($value['current_project']) {
				foreach ($value['current_project'] as $key1 => $value1) {
					$str = $str.$value1['title'].' (id: '.$value1['id'].')';
				}
			} 
			$str = $str.';';
			$str = $str.$totalHours[$key]['total'].';';
			foreach ($datesList as $index => $date) {
				if ($vocations[$key][$date]!='false') {
					$str = $str.$vocations[$key][$date].';';
				} else {
					$str = $str.$totalHours[$key][$date].';';
				}
			}
			$str = $str.PHP_EOL;
		}
		file_put_contents($path, $str);
		return $url;
	}

	public static function getDatesListAction ($DATE_to=false, $DATE_from=false) {

		self::setDateFilter($DATE_to, $DATE_from);
		$period = new DatePeriod(
			new DateTime($DATE_from),
			new DateInterval('P1D'),
			// new DateTime($DATE_to)
			(new DateTime($DATE_to))->modify('+1 day')
		);

		$dates = array();
		foreach ($period as $key => $value) {
			$dates[] = $value->format('d.m.Y');     
		}
		return array_reverse($dates);
	}

	public static function setDateFilter (&$DATE_to, &$DATE_from) {
		if (!$DATE_to) {
			$DATE_to = (new DateTime())->format('d.m.Y');
			// $DATE_to = (new DateTime(date('d.m.Y', strtotime('last day of this month 00:00'))))->format('d.m.Y');
		} 
		if (!$DATE_from) {
			$DATE_from = (new DateTime(date('d.m.Y', strtotime('-1 week'))))->format('d.m.Y');
			// $DATE_from = (new DateTime(date('d.m.Y', strtotime('first day of this month 23:59'))))->format('d.m.Y');
		}
	}

	public static function filterUsersByProjects ($dealsFilter) {
		$arr = [];
		$plans = \Bitrix\Sn\PlanTable::getList([
			'filter' => [
				'DEAL_ID' => $dealsFilter,
				'<=BEGIN_DATE' => (new DateTime())->format('d.m.Y H:i:s'),
				'>=CLOSE_DATE' => (new DateTime())->format('d.m.Y H:i:s'),
			],
		])->fetchAll();
		foreach ($plans as $key => $value) {
			$arr[] = $value['USER_ID'];
		}
		return array_unique($arr);
	}

	public static function getUsersList ($limit, $offset, $userFilterData, $order, $dealsFilter, $DATE_to, $DATE_from) {

		$userProjectFilter = self::filterUsersByProjects($dealsFilter);

		if ($userFilterData and $dealsFilter) {
			$filter = [
				'ID' => array_intersect($userFilterData, $userProjectFilter),
				'UF_USER_SN_AUTHORIZATION_IS_ALLOWED' => 1,
			];
		} elseif (!$userFilterData and $dealsFilter) {
			$filter = [
				'ID' => $userProjectFilter,
				'UF_USER_SN_AUTHORIZATION_IS_ALLOWED' => 1,
			];
		} elseif ($userFilterData and !$dealsFilter) {
			$filter = [
				'ID' => $userFilterData,
				'UF_USER_SN_AUTHORIZATION_IS_ALLOWED' => 1,
			];
		} else {
			$filter = [
				'UF_USER_SN_AUTHORIZATION_IS_ALLOWED' => 1,
			];
		}

		if (!$order or !sizeof($order)) {
			$order = [];
		}

		CUserOptions::SetOption('sn', 'checkin_checkout', $limit);
		$src = \Bitrix\Main\UserTable::getList([
			'order' => $order,
			'filter' => $filter,
			'select' => [
				'ID',
				'NAME',
				'LAST_NAME',
				'SECOND_NAME',
				'UF_USER_SN_AUTHORIZATION_IS_ALLOWED',
			],
			'limit' => $limit,
			'count_total' => true,
			'offset' => $offset,
		]);
		return [
			'count' =>  $src->getCount(),
			'list' => $src->fetchAll(),
		];
		
	}

	public static function getWorksData ($DATE_to, $DATE_from) {
		$workfilter = [];
		$DATE_to = (new DateTime($DATE_to))->modify('+1 day')->format('d.m.Y');
		$workfilter['>=DATE_CREATE'] = new \Bitrix\Main\Type\DateTime($DATE_from, 'd.m.Y');
		$workfilter['<=DATE_CREATE'] = new \Bitrix\Main\Type\DateTime($DATE_to, 'd.m.Y');
		
		$src = Bitrix\Sn\WorkTable::getList([
			'filter' => $workfilter,
			'order' => ['DATE_CREATE' => 'ASC', ],
			'select' => [
				'*',
				'USER' => 'PLAN.USER_ID',
				'DEAL' => 'PLAN.DEAL_ID',
				'DEAL_TITLE' => 'DEAL_DATA.TITLE',
			],
			'runtime' => [
				'PLAN' => [
					'data_type' => 'Bitrix\Sn\PlanTable',
					'reference' => ['this.PLAN_ID' => 'ref.ID'],
				],
				'DEAL_DATA' => [
					'data_type' => 'Bitrix\Crm\DealTable',
					'reference' => ['this.DEAL' => 'ref.ID'],
				],
			],
		]);

		$src->addFetchDataModifier(function (&$data) {
			$data['time'] = $data['DATE_CREATE']->format('H:i:s');
			$data['DATE_CREATE'] = $data['DATE_CREATE']->format('d.m.Y');
			if ($data['STATUS'] == 'B') {
				$data['status'] = 'begin';
			} elseif ($data['STATUS'] == 'C') {
				$data['status'] = 'closed';
			}
		});
		return $src->fetchAll();
	}

	public static function setRowsDates ($DATE_to, $DATE_from, $limit, $offset, $userFilterData, $order, $dealsFilter) {
		$users = self::getUsersList($limit, $offset, $userFilterData, $order, $dealsFilter, $DATE_to, $DATE_from);
		$dates = self::getDatesListAction($DATE_to, $DATE_from);
		foreach ($users['list'] as $key => $value) {
			$users['list'][$key]['dates'] = [];
			foreach ($dates as $key1 => $value1) {
				$users['list'][$key]['dates'][$value1] = [];
			}
		}
		return $users;
	}

	public static function getUsersRowsAction (
		$DATE_to=false,
		$DATE_from=false,
		$limit=false,
		$offset=0,
		$userFilterData=false,
		$order=false,
		$dealsFilter=false
	) {
		self::setDateFilter($DATE_to, $DATE_from);
		$users = self::setRowsDates($DATE_to, $DATE_from, $limit, $offset, $userFilterData, $order, $dealsFilter);
		$rows = $users['list'];
		$total_count = $users['count']; 
		$work = self::getWorksData($DATE_to, $DATE_from);
		$usersCurrentProjects = self::getUsersCurrentProjects();
		foreach ($rows as $key => $value) {
			foreach ($work as $key1 => $value1) {
				if ($value1['USER'] == $value['ID']) {
					$rows[$key]['dates'][$value1['DATE_CREATE']][] = $value1;
				}
			}
			$rows[$key]['current_project'] = $usersCurrentProjects[$value['ID']];
		}

		$vocations = self::getVocations($DATE_to, $DATE_from, $users);
		$arResult = [
			'rows' => $rows,
			'total_count' => $total_count,
			'total_hours' => self::totalHoursCounter($rows),
			'vocations' => $vocations,
		];
		return $arResult;
	}

	public static function getUsersCurrentProjects () {
		$arr = [];
		$plans = \Bitrix\Sn\PlanTable::getList([
			'filter' => [
				'<=BEGIN_DATE' => (new DateTime())->format('d.m.Y H:i:s'),
				'>=CLOSE_DATE' => (new DateTime())->format('d.m.Y H:i:s'),
			],
			'runtime' => [
				'DEAL' => [
					'data_type' => 'Bitrix\Crm\DealTable',
					'reference' => ['this.DEAL_ID' => 'ref.ID'],
				],
			],
			'select' => [
				'*',
				'DEAL_TITLE' => 'DEAL.TITLE',
			],
		])->fetchAll();

		foreach ($plans as $key => $value) {
			$arr[$value['USER_ID']][] = [
				'id' => $value['DEAL_ID'],
				'title' => $value['DEAL_TITLE'],
			];
		}
		// file_put_contents($_SERVER['DOCUMENT_ROOT'].'/local/log.json', json_encode([
		// 	'plans' => $arr,
		// ]));

		return $arr;
	}

	public static function getVocations($DATE_to, $DATE_from, $users) {
		$src = CIBlockElement::GetList([], [
			'IBLOCK_CODE' => 'absence',
			'ACTIVE' => 'Y',
			[
				'LOGIC' => 'OR',
				[
					'LOGIC' => 'AND',
					'>=DATE_ACTIVE_FROM' => $DATE_from,
					'<=DATE_ACTIVE_FROM' => $DATE_to,
				], [
					'LOGIC' => 'AND',
					'>=DATE_ACTIVE_TO' => $DATE_from,
					'<=DATE_ACTIVE_TO' => $DATE_to,
				]
			]
		], false, false, [
			'ID',
			'IBLOCK_ID',
			'NAME',
			'PROPERTY_USER',
			'DATE_ACTIVE_FROM',
			'DATE_ACTIVE_TO',
			'PROPERTY_ABSENCE_TYPE',
			'LOCK_STATUS',
			'*',
		]);
		while ($item = $src->Fetch()) {
			$vocations[] = $item;
		}
		$arr = [];
		foreach ($users['list'] as $key => $value) {
			foreach ($value['dates'] as $key1 => $value2) {
				$arr[$key][$key1] = false;
				foreach ($vocations as $key2 => $value2) {
					$start_date = new DateTime($value2['DATE_ACTIVE_FROM']);
					$end_date = new DateTime($value2['DATE_ACTIVE_TO']);
					$check_date = new DateTime($key1);
					if ($check_date >= $start_date && $check_date <= $end_date) {
						if ($value['ID'] == $value2['PROPERTY_USER_VALUE']) {
							$arr[$key][$key1] = $value2['PROPERTY_ABSENCE_TYPE_VALUE'];
						}						
					} 
				}
			}
		}
		return $arr;
	}

	public static function totalHoursCounter ($rows) {
		$arr = [];
		foreach ($rows as $key => $value) {
			$arr[$key] = [];
			foreach ($value['dates'] as $key1 => $value1) {
				$arr[$key][$key1] = 'empty';
				if (count($value1)) {
					$arrDaylyTime['begin'] = [];
					$arrDaylyTime['closed'] = [];
					foreach ($value1 as $key2 => $value2) {
						if ($value2['status'] == 'begin') {
							$arrDaylyTime['begin'][] = $value2['time'];
						} elseif ($value2['status'] == 'closed') {
							$arrDaylyTime['closed'][] = $value2['time'];
						}
					}
					if (count($arrDaylyTime['begin']) == count($arrDaylyTime['closed'])) {
						$totalDaylyHours = [];
						foreach ($arrDaylyTime['begin'] as $key2 => $value2) {
							$totalDaylyHours[] = self::timeDiffCalc($arrDaylyTime['begin'][$key2], $arrDaylyTime['closed'][$key2]);
						}
						$arr[$key][$key1] = self::getTotalTime($totalDaylyHours);
					}
				}
				
			}
			$arr[$key]['total'] = self::getTotalTime($arr[$key]);
		}
		$arr = self::germanFormatTotalTime($arr);
		return $arr;
	}

	public static function germanFormatTotalTime ($arr) {
		foreach ($arr as $key => $value) {
			foreach ($value as $key1 => $value1) {
				$newArr[$key][$key1] = str_replace(':', ' s ', $value1).' m';
				if ($value1 == 'empty') {
					$newArr[$key][$key1] = '0 s 0 m';
				}
			}
		}
		return $newArr;
	}

	public static function getTotalTime($times) {
		$totalMinutes = 0;
		foreach ($times as $time) {
			if ($time != 'empty') {
				list($hours, $minutes) = explode(':', $time);
				$totalMinutes += $hours * 60 + $minutes;
			}			
		}
		$totalHours = floor($totalMinutes / 60);
		$remainingMinutes = $totalMinutes % 60;

		return $totalHours.':'.$remainingMinutes;
	}

	public static function timeDiffCalc ($time1, $time2) {
		$time1 = new \DateTime($time1);
		$time2 = new \DateTime($time2);

		$interval = $time1->diff($time2);

		$hours = $interval->h;
		$minutes = $interval->i;

		return $hours.':'.$minutes;
	}

	public static function saveEditedWorktimeAction ($ID, $DATE_CREATE) {
		$re = Bitrix\Sn\WorkTable::update($ID, [
			'DATE_CREATE' => new \Bitrix\Main\Type\DateTime($DATE_CREATE, 'd.m.Y H:i:s'),
		]);
		if($re->isSuccess()) {
			$ID = $re->getId();
			return 'success';
		} else {
			$error = $re->getErrorMessages();
			return var_export($error, true);
		}
	}
	

}

