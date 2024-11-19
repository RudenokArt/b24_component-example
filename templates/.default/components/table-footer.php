		<table>
			<tr class="checkin_checkout-table-footer">
				<td></td>
				<td></td>
				<td>
					<div class="text-center d-flex">
						<?php echo GetMessage('PAGE'); ?>:
						<template v-for="(item, index) in pagesArr">
							<template v-if="item>(currentPage-5) && item<(currentPage+5)">
								<div v-if="index!=0">|</div>
								<template v-if="currentPage!=(item)">
									<a v-on:click="setCurrentPage(item)" href="#" class="d-block pl-1 pr-1">
										{{item}}
									</a>
								</template>
								<template v-else="currentPage==(item)">
									<span class="d-block pl-1 pr-1 text-danger">
										{{item}}
									</span>
								</template>
							</template>
						</template>
					</div>

				</td>
				<td >
					<div class="d-flex align-items-center">
						<div><?php echo GetMessage('ENTRIES'); ?>:</div>
						<div class="p-1"></div>
						<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-inline ui-ctl-xs">
							<div class="ui-ctl-after ui-ctl-icon-angle"></div>
							<select v-model="rowsQuantity" class="ui-ctl-element">
								<option value="5">5</option>
								<option value="10">10</option>
								<option value="20">20</option>
								<option value="50">50</option>
								<option value="100">100</option>
								<option value="200">200</option>
								<option value="300">300</option>
							</select>
						</div>
					</div>
				</td>
			</tr>
		</table>