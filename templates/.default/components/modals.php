

<div 
class="modal fade" id="newWorktime"
tabindex="-1"
role="dialog"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
	<div class="modal-content" id="newWorktime-preloader">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLongTitle"><?php echo GetMessage('ADD') ?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			{{newWorktime.date}}
			<div v-if="newWorktime.user">
				<?php echo GetMessage('USER');?>: {{newWorktime.user.NAME}}
			</div>
			<div v-if="newWorktime.projectId">
				<?php echo GetMessage('PROJECT');?>:{{newWorktime.projectTitle}}
			</div>
			<div v-else="!newWorktime.projectId">
				<div class="ui-alert ui-alert-danger">
					<span class="ui-alert-message"><?php echo GetMessage('NO_PLAN');?></span>
				</div>				
			</div>

				<div class="ui-ctl ui-ctl-textbox ui-ctl-w25 ui-ctl-inline">
					<input id="beginWorktime" type="text" class="ui-ctl-element" v-model="newWorktime.beginWorktime">
				</div>
				<span> - </span>
				<div class="ui-ctl ui-ctl-textbox ui-ctl-w25 ui-ctl-inline">
					<input id="closeWorktime" type="text" class="ui-ctl-element" v-model="newWorktime.closeWorktime">
				</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="ui-btn ui-btn-light-border" data-dismiss="modal">
				<?php echo GetMessage('CANCEL') ?>
			</button>
			<button 
			v-on:click="newWorktimeSave"
			v-if="newWorktime.projectId"
			type="button"
			class="ui-btn ui-btn-success">
			<?php echo GetMessage('SAVE') ?>
		</button>
	</div>
</div>
</div></div>

<div 
class="modal fade" id="worktimeCheckout"
tabindex="-1"
role="dialog"
aria-labelledby="exampleModalCenterTitle"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
	<div class="modal-content" id="worktimeCheckout-preloader">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLongTitle"><?php echo GetMessage('CLOSE') ?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">

			<div class="ui-ctl ui-ctl-textbox ui-ctl-w25 ui-ctl-inline ui-ctl-disabled">
				<input  v-bind:value="checoutData.DATE_CREATE" type="text" class="ui-ctl-element" readonly>
			</div>
			<div class="ui-ctl ui-ctl-textbox ui-ctl-w25 ui-ctl-inline">
				<input  v-bind:value="checoutData.time" id="closedTime" type="text" class="ui-ctl-element">
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="ui-btn ui-btn-light-border" data-dismiss="modal">
				<?php echo GetMessage('CANCEL') ?>
			</button>
			<button v-on:click="worktimeCheckout" type="button" class="ui-btn ui-btn-success">
				<?php echo GetMessage('SAVE') ?>
			</button>
		</div>
	</div>
</div></div>

<div 
class="modal fade" id="editWorktime"
tabindex="-1"
role="dialog"
aria-labelledby="exampleModalCenterTitle"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
	<div class="modal-content" id="editWorktime-preloader">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLongTitle"><?php echo GetMessage('EDIT') ?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<div class="ui-ctl ui-ctl-textbox ui-ctl-w25 ui-ctl-inline ui-ctl-disabled">
				<input  v-bind:value="editedDate" type="text" class="ui-ctl-element" readonly>
			</div>
			<div class="ui-ctl ui-ctl-textbox ui-ctl-w25 ui-ctl-inline">
				<input  v-bind:value="editedTime" id="editedTime" type="text" class="ui-ctl-element">
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="ui-btn ui-btn-light-border" data-dismiss="modal">
				<?php echo GetMessage('CANCEL') ?>
			</button>
			<button v-on:click="saveEditedWorktime" type="button" class="ui-btn ui-btn-success">
				<?php echo GetMessage('SAVE') ?>
			</button>
		</div>
	</div>
</div></div>
