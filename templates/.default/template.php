<!-- <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script> -->
<script src="/local/js/vue.global.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div id="CheckinCheckout_dealsSelect-wrapper">
	<?php echo GetMessage('PROJECT'); ?>
	<span>:</span>
	<?php $GLOBALS["APPLICATION"]->IncludeComponent('bitrix:crm.entity.selector',
		'',
		array(
			'ENTITY_TYPE' => ['DEAL',],
			'INPUT_NAME' => 'dealsSelect',
			'INPUT_VALUE' => '',
			'MULTIPLE' => 'Y',
		),
		false,
		array('HIDE_ICONS' => 'Y')
	);?>
</div>



<div id="checkin_checkout" class="pt-3">


	<?php include_once 'components/filter.php'; ?>


	<div class="d-flex">
		<div class="w-100" id="checkin_checkout-table-container">
			<table class="table table-bordered checkin_checkout-table">
				<tr class="text-secondary">
					<th>
						ID
						<template v-if="order.ID=='DESC'">
							<button v-on:click="order={ID:'ASC'}" class="ui-btn ui-btn-xs ui-btn-light">
								<i class="fa fa-sort-asc" aria-hidden="true"></i>
							</button>
						</template>
						<template v-else="order.ID=='ASC'">
							<button v-on:click="order={ID:'DESC'}" class="ui-btn ui-btn-xs ui-btn-light">
								<i class="fa fa-sort-desc" aria-hidden="true"></i>
							</button>
						</template>
					</th>
					<th>
						<?php echo GetMessage('USER'); ?>
						<template v-if="order.NAME=='DESC'">
							<button v-on:click="order={NAME:'ASC'}" class="ui-btn ui-btn-xs ui-btn-light">
								<i class="fa fa-sort-asc" aria-hidden="true"></i>
							</button>
						</template>
						<template v-else="order.NAME=='ASC'">
							<button v-on:click="order={NAME:'DESC'}" class="ui-btn ui-btn-xs ui-btn-light">
								<i class="fa fa-sort-desc" aria-hidden="true"></i>
							</button>
						</template>
					</th>
					<th><?php echo GetMessage('PROJECT') ?></th>
					<th height="100" v-for="(item, index) in datesList">{{item}}</th>
				</tr>
				<tr v-for="(item, index) in rows">
					<td>{{item.ID}}</td>
					<td>
						<a v-bind:href="'/company/personal/user/'+item.ID+'/'">
							{{item.NAME}} {{item.LAST_NAME}} {{item.SECOND_NAME}}
						</a>
						<div class="border-top d-flex justify-content-around flex-wrap">
							<div class="text-secondary">
								<small><b><?php echo GetMessage('TOTAL'); ?>: </b></small>
							</div>
							<div>
								<span class="ms-5">{{total_hours[index]['total']}}</span>
							</div>
						</div>
					</td>
					<td style="min-width: 100px;">
						<template v-if="item.current_project">
							<template v-for="(current_project_item, current_project_index) in item.current_project">
								<template v-for="(current_project_item, project_index) in item.current_project">
									<a v-bind:href="'/crm/deal/details/'+current_project_item.id+'/'">
										{{current_project_item.title}}
										(id: {{current_project_item.id}})
									</a>
								</template>
							</template>
						</template>

					</td>
					
					<td height="125" class="p-0 m-0" style="min-width: 150px; position: relative;" v-for="(value, key) in item.dates">
						
						<div v-if="backLightCounter(value)!=0" class="backLightCounter">
							<div v-if="currentDateFormatted_dmY==key"  class="backLightCounter-today"></div>
							<button 
							class="timetable-checkout-button ui-btn ui-btn-xs ui-btn-round ui-btn-icon-add ui-btn-success"
							title="<?php echo GetMessage('CLOSE') ?>"
							data-toggle="modal"
							data-target="#worktimeCheckout"
							v-on:click="closedPlanDetector(value)"
							></button>
						</div>
						<div class="row p-0 m-0">
							<template v-if="value.length>0">
								<div class="col-6 p-0 m-0 border-bottom p-1 text-secondary text-center"><b>Start</b></div>
								<div class="col-6 p-0 m-0 border-bottom border-left p-1 text-secondary text-center"><b>End</b></div>
							</template>
							<template v-for="(element, number) in value">
								<div class="col-6  p-0 m-0 border-left">
									<div class="d-flex">
										<div class="checkin_checkout-worktime-detail-trigger">
											{{element.time}}
											<div class="checkin_checkout-worktime-detail-target">
												{{element.DEAL_TITLE}} (id: {{element.DEAL}})
												<br>
												<i>{{element.ADDRESS.adr}}</i>
											</div>
										</div>
										<button
										data-toggle="modal"
										data-target="#editWorktime"
										v-on:click="editWorktime(element.ID, element.STATUS, element.DATE_CREATE, element.time)"
										class="ui-btn ui-btn-icon-edit ui-btn-xs ui-btn-light"></button>
									</div>
								</template>
							</div>
							<div v-if="value.length>0" class="border-top d-flex justify-content-around flex-wrap">
								<div class="text-secondary">
									<b><?php echo GetMessage('TOTAL'); ?>: </b>
								</div>
								<div>
									<span class="ms-5">{{total_hours[index][key]}}</span>
								</div>
							</div>
							<div class="bg-info p-1" v-if="vocations[index] && vocations[index][key]">
								<span class="text-danger">
									&#9632 
								</span>
								<span class="text-light"> 
									{{vocations[index][key]}}
								</span>
							</div>
							<div v-if="value.length<=0">
								<button
								data-toggle="modal"
								data-target="#newWorktime"
								v-on:click="getNewWorktimeData(key, item)"
								class="ui-btn ui-btn-icon-add ui-btn-secondary ui-btn-round m-1"></button>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<?php include_once 'components/table-footer.php'; ?>
		<?php include_once 'components/modals.php'; ?>
	</div>


	<script>

		var CheckinCheckout = Vue.createApp({

			data () {
				return {
					datesList: [],
					DATE_to: '',
					DATE_from: '',
					rows: false,
					editedDate: false,
					editedTime: false,
					editedWork: false,
					total_count: false,
					total_hours: false,
					vocations: false,
					rowsQuantity: "<?php echo $arResult['rowsQuantity'] ?>",
					offset: 0,
					currentPage: 1,
					checoutData: {},
					userFilterData: [],
					order: {},
					dealsFilter: [],
					datePeriod: 'TODAY',
					dateMonthOffset: 0,
					currentDateFormatted_dmY: '',
					newWorktime: {
						beginWorktime: '09:00:00',
						closeWorktime: '18:00:00',
					},
				};
			},

			computed: {
				pagesQuantity: function () {
					var qty = Math.ceil(this.total_count / this.rowsQuantity);
					return qty;
				},
				pagesArr: function () {
					var arr = [];
					for (var i = 0; i < this.pagesQuantity; i++) {
						arr[i] = i + 1;
					}
					return arr;
				}
			},

			watch: {

				dateMonthOffset: function () {
					this.setMonth();
				},
				datePeriod: function () {
					this.setMonth();
				},
				order: function () {
					this.getDatesList();
					this.getUsersRows();
				},
				rowsQuantity: function () {
					this.setCurrentPage();
				},
				DATE_from: function () {
					this.getDatesList();
					this.getUsersRows();
				},
				DATE_to: function () {
					this.getDatesList();
					this.getUsersRows();
				},
				dealsFilter: function () {
					this.getDatesList();
					this.getUsersRows();
				},
			},

			mounted: function () {
				this.getDatesList();
				this.getUsersRows();
				this.currentDateFormatted_dmY = this.getCurrentDateFormatted_dmY();
			},

			methods: {

				compareTimes:	function(time1, time2) {
    			// Разделяем строки времени на часы, минуты и секунды
					const [hours1, minutes1, seconds1] = time1.split(':').map(Number);
					const [hours2, minutes2, seconds2] = time2.split(':').map(Number);

    			// Создаем объекты Date с одинаковой датой, но разным временем
					const date1 = new Date(0, 0, 0, hours1, minutes1, seconds1);
					const date2 = new Date(0, 0, 0, hours2, minutes2, seconds2);

    			// Сравниваем объекты Date
					if (date1 >= date2) {
						this.invalidTimeNotify();
						return false;
					}
					return true;
				},

				newWorktimeSave: async function () {
					if (!this.timeValidation('#beginWorktime') || !this.timeValidation('#closeWorktime')) {
						$('#newWorktime').find('.close').trigger('click');
						return false;
					}
					this.newWorktime.beginWorktime = this.timeValidation('#beginWorktime');
					this.newWorktime.closeWorktime = this.timeValidation('#closeWorktime');
					if (!this.compareTimes(this.newWorktime.beginWorktime, this.newWorktime.closeWorktime)) {
						$('#newWorktime').find('.close').trigger('click');
						return false;
					}
					BX.showWait('newWorktime-preloader');
					var re = await BX.ajax.runComponentAction(
						'sn:work.timetable',
						'newWorktimeSave',
						{
							mode: 'ajax',
							data: {
								work: this.newWorktime,
							},
						})
					.then(function (result) {
						return result.data;
					});

					$('#newWorktime').find('.close').trigger('click');
					this.getDatesList();
					this.getUsersRows();
					BX.closeWait('newWorktime-preloader');
				},

				getNewWorktimeData: async function (key, item) {
					this.newWorktime.date = key;
					this.newWorktime.user = {
						ID: item.ID,
						NAME: item.NAME+' '+item.LAST_NAME+' '+item.SECOND_NAME,
					}
					BX.showWait('newWorktime-preloader');
					var re = await BX.ajax.runComponentAction(
						'sn:work.timetable',
						'getNewWorktimeData',
						{
							mode: 'ajax',
							data: this.newWorktime,
						})
					.then(function (result) {
						return result.data;
					});
					this.newWorktime.projectTitle = re.DEAL_TITLE;
					this.newWorktime.projectId = re.DEAL_ID;
					this.newWorktime.planId = re.ID;
					BX.closeWait('newWorktime-preloader');
				},

				getCurrentDateFormatted_dmY: function() {
					let date = new Date();
					let day = String(date.getDate()).padStart(2, '0');
					let month = String(date.getMonth() + 1).padStart(2, '0'); 
					let year = date.getFullYear();
					return `${day}.${month}.${year}`;
				},

				getDateNMonthsAgo: function () {
					let date = new Date();
					date.setMonth(date.getMonth() + this.dateMonthOffset);
					let year = date.getFullYear();
					let month = date.getMonth() + 1;
					return { year, month };
				},

				setMonth: function () {
					var dateNMonthsAgo = this.getDateNMonthsAgo();
					if (this.datePeriod == 'MONTH') {
						const now = new Date();
						// const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
						// const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
						const startOfMonth = new Date(dateNMonthsAgo.year, dateNMonthsAgo.month, 1);
						const endOfMonth = new Date(dateNMonthsAgo.year, dateNMonthsAgo.month + 1, 0);
						this.DATE_from = this.getFormattedDate(startOfMonth);
						this.DATE_to = this.getFormattedDate(endOfMonth);
					}
					if (this.datePeriod == 'TODAY') {
						const now = new Date();
						const oneWeekAgo = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 7);
						this.DATE_from = this.getFormattedDate(oneWeekAgo);
						this.DATE_to = this.getFormattedDate(now);
					}
				},

				getFormattedDate: function(date) {
					const day = String(date.getDate()).padStart(2, '0');
					const month = String(date.getMonth() + 1).padStart(2, '0');
					const year = date.getFullYear();
					return `${day}.${month}.${year}`;
				},

				userFilter: function (arr) {
					this.userFilterData = arr;
					this.setCurrentPage();
					this.getDatesList();
					this.getUsersRows();
				},

				closedPlanDetector(value) {
					var re = [];
					for (var i = 0; i < value.length; i++) {
						if (value[i]['STATUS'] == 'B') {
							re = value[i];
						}
					}
					this.checoutData = re;
				},

				worktimeCheckout: async function () {
					var time = this.timeValidation('#closedTime');
					if (!time) {
						return;
					}
					BX.showWait('worktimeCheckout-preloader');
					var re = await BX.ajax.runComponentAction(
						'sn:work.timetable',
						'worktimeCheckout',
						{
							mode: 'ajax',
							data: {
								PLAN_ID: this.checoutData.PLAN_ID,
								DATE_CREATE: this.checoutData.DATE_CREATE + ' ' + time,
							},
						})
					.then(function (result) {
						return result.data;
					});
					this.getDatesList();
					this.getUsersRows();
					BX.closeWait('worktimeCheckout-preloader');
					$('#worktimeCheckout').find('.close').trigger('click');
				},

				csvGeneration: async function (e) {
					$(e.target).toggleClass('ui-btn-wait');
					var re = await BX.ajax.runComponentAction(
						'sn:work.timetable',
						'csvGeneration',
						{
							mode: 'ajax',
							data: {
								rows: this.rows,
								datesList: this.datesList,
								totalHours: this.total_hours,
								vocations: this.vocations,
							},
						})
					.then(function (result) {
						return result.data;
					});
					$(e.target).toggleClass('ui-btn-wait');
					$('#csvGenerated-link').attr('href', re);
					$('#csvGenerated-link')[0].click();
				},

				backLightCounter: function (value) {
					var count = 0;
					for (var i = 0; i < value.length; i++) {
						if (value[i].STATUS == "B") {
							count++;
						} else {
							count --;
						}
					}
					return count;
				},

				setCurrentPage: function (item=1) {
					this.currentPage = item;
					this.offset=(item-1)*this.rowsQuantity;				
					this.getDatesList();
					this.getUsersRows();
				},

				invalidTimeNotify: function () {
					BX.UI.Notification.Center.notify({
						content: BX.create("div", {
							style: {
								fontSize: "20px",
								color: 'red',
							},
							html: "<?php echo GetMessage('INVALID_TIME'); ?>",
						})
					});
				},

				timeValidation: function (timeInput) {
					var time = $(timeInput).prop('value');
					var arr = time.split(':');
					for (var i = 0; i < arr.length; i++) {
						if (arr[i] >= 60 || arr[0] >= 24 || !time) {
							this.invalidTimeNotify();
							return false;
						}
					}
					return time;
				},

				saveEditedWorktime: async function () {
					var time = this.timeValidation('#editedTime');
					if (!time) {
						return;
					}

					BX.showWait('editWorktime-preloader');
					var re = await BX.ajax.runComponentAction(
						'sn:work.timetable',
						'saveEditedWorktime',
						{
							mode: 'ajax',
							data: {
								ID: this.editedWork,
								DATE_CREATE: this.editedDate + ' ' + time,
							},
						})
					.then(function (result) {
						return result.data;
					});
					this.getDatesList();
					this.getUsersRows();
					BX.closeWait('editWorktime-preloader');
					$('#editWorktime').find('.close').trigger('click');
				},

				editWorktime: function(workId, STATUS, DATE_CREATE, time) {
					this.editedWork = workId;
					this.editedDate = DATE_CREATE;
					this.editedTime = time;
				},

				getUsersRows: async function () {
					BX.showWait('checkin_checkout');
					var re = await BX.ajax.runComponentAction(
						'sn:work.timetable',
						'getUsersRows',
						{
							mode: 'ajax',
							data: {
								DATE_to: this.DATE_to,
								DATE_from: this.DATE_from,
								limit: this.rowsQuantity,
								offset: this.offset,
								userFilterData: this.userFilterData,
								order: this.order,
								dealsFilter: this.dealsFilter,
							},
						})
					.then(function (result) {
						return result.data;
					});
					this.rows = re.rows;
					this.total_count = re.total_count;
					this.total_hours = re.total_hours;
					this.vocations = re.vocations;
					BX.closeWait('checkin_checkout');
				},

				getDatesList: async function () {
					BX.showWait('checkin_checkout');
					var re = await BX.ajax.runComponentAction(
						'sn:work.timetable',
						'getDatesList',
						{
							mode: 'ajax',
							data: {
								DATE_to: this.DATE_to,
								DATE_from: this.DATE_from,
							},
						})
					.then(function (result) {
						return result.data;
					});
					this.datesList = re;
					BX.closeWait('checkin_checkout');

					$('input[name="DATE_from"]').prop('value', re.slice(-1));
					$('input[name="DATE_to"]').prop('value', re[0]);
				}
			},
		}).mount('#checkin_checkout');

	</script>

