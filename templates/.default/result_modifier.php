<?php
// $APPLICATION->ShowHead();
\Bitrix\Main\Loader::includeModule('sn.rest');
\Bitrix\Main\UI\Extension::load("ui.notification");
\Bitrix\Main\UI\Extension::load("ui.ears");
CJSCore::Init(['jquery', 'ui.bootstrap4', 'masked_input']);
$APPLICATION->SetAdditionalCSS("/bitrix/css/main/font-awesome.css");
// Bitrix\Sn\PlanTable
// Bitrix\Sn\WorkTable
$arResult['rowsQuantity'] = CUserOptions::GetOption('sn', 'checkin_checkout');
if (!$arResult['rowsQuantity']) {
	$arResult['rowsQuantity'] = 10;
}

$arResult['usersList'] = \Bitrix\Main\UserTable::getList([
			'filter' => [
				'UF_USER_SN_AUTHORIZATION_IS_ALLOWED' => 1,
			],
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
		])->fetchAll();
		