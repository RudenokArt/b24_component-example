<div class="d-flex pb-2 justify-content-between align-items-center">	
	<div>
		<?php echo GetMessage('USERS_FILTER'); ?>:
		<select id="checkin_checkout-users_filter" name="userFilter[]" multiple="multiple" style="min-width: 350px;">
			<?php foreach ($arResult['usersList'] as $key => $value): ?>
				<option value=""></option>
				<option value="<?php echo $value['ID'] ?>">
					<?php echo $value['NAME'] ?>
					<?php echo $value['LAST_NAME'] ?>
					<?php echo $value['SECOND_NAME'] ?>
					(id: <?php echo $value['ID'] ?>)
				</option>
			<?php endforeach ?>
		</select>
	</div>

	<div class="d-flex">
		<?php $APPLICATION->IncludeComponent("bitrix:main.calendar","",Array(
			"SHOW_INPUT" => "Y",
			"FORM_NAME" => "",
			"INPUT_NAME" => "DATE_from",
			"INPUT_NAME_FINISH" => "DATE_to",
			"INPUT_VALUE" => "",
			"INPUT_VALUE_FINISH" => "", 
			"SHOW_TIME" => "N",
			"HIDE_TIMEBAR" => "Y"
		)
	); ?>

	<div class="d-flex justify-content-center ml-3">
		<button
		v-if="datePeriod=='MONTH'"
		v-on:click="dateMonthOffset--"
		class="ui-btn ui-btn-sm ui-btn-light-border ui-btn-icon-back"></button>
		<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-inline ui-ctl-sm">
			<div class="ui-ctl-after ui-ctl-icon-angle"></div>
			<select class="ui-ctl-element" name="DATE_PERIOD" v-model="datePeriod">
				<option value="TODAY"><?php echo GetMessage('TODAY'); ?></option>
				<option value="MONTH"><?php echo GetMessage('MONTH'); ?></option>
			</select>
		</div>
		<button
		v-if="datePeriod=='MONTH'"
		v-on:click="dateMonthOffset++"
		class="ui-btn ui-btn-sm ui-btn-light-border ui-btn-icon-forward m-0"></button>
	</div>

</div>

<div>
	<button
	v-on:click="csvGeneration"
	class="ui-btn ui-btn-success ui-btn-xs ui-btn-icon-download">.csv</button>
	<a href="" id="csvGenerated-link" download="timetable.csv" class="d-none">.csv</a>
</div>
</div>