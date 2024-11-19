BX.ready(function() {

		const targetNode = document.getElementById('CheckinCheckout_dealsSelect-wrapper');
// Настраиваем параметры наблюдения
		const config = { childList: true, subtree: true };
// Создаем экземпляр наблюдателя
		const observer = new MutationObserver((mutationsList, observer) => {
			setTimeout(function() {
				var dealsFilter = [];
				$('input[name="dealsSelect[]"]').each(function () {
					if (this.value) {
						dealsFilter.push(Number(this.value));
					}				
				});
				CheckinCheckout.dealsFilter = dealsFilter;
			}, 100);		
		});
// Начинаем наблюдение
		observer.observe(targetNode, config);

		$('#checkin_checkout-users_filter').select2();

		$('#checkin_checkout-users_filter').change(function () {
			var arr = [];
			for (var i = 0; i < this.selectedOptions.length; i++) {
				arr.push(this.selectedOptions[i].value);
			}
			CheckinCheckout.userFilter(arr);
		});

		const ears = new  BX.UI.Ears({
			container: document.querySelector('#checkin_checkout-table-container'),
			// mousewheel: true,
		});
		ears.init();

		$('input[name="DATE_from"], input[name="DATE_to"]')
		.attr('readonly', true)
		.addClass('form-control form-control-sm w-25 d-inline');

		$('input[name="DATE_from"]').change(function () {
			var DATE_from = $('input[name="DATE_from"]').prop('value');
			CheckinCheckout.DATE_from = DATE_from;
		});

		$('input[name="DATE_to"]').change(function () {
			var DATE_to = $('input[name="DATE_to"]').prop('value');
			CheckinCheckout.DATE_to = DATE_to;
		});

		$('select[name="DATE_PERIOD"]').change(function () {

			// if (this.value == 'MONTH') {
			// 	const now = new Date();
			// 	const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
			// 	const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
			// 	CheckinCheckout.DATE_from = getFormattedDate(startOfMonth);
			// 	CheckinCheckout.DATE_to = getFormattedDate(endOfMonth);
			// }
			// if (this.value == 'TODAY') {
			// 	const now = new Date();
			// 	const oneWeekAgo = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 7);
			// 	CheckinCheckout.DATE_from = getFormattedDate(oneWeekAgo);
			// 	CheckinCheckout.DATE_to = getFormattedDate(now);
			// }

		})

		// function getFormattedDate(date) {
		// 	const day = String(date.getDate()).padStart(2, '0');
		// 	const month = String(date.getMonth() + 1).padStart(2, '0');
		// 	const year = date.getFullYear();
		// 	return `${day}.${month}.${year}`;
		// }

		new BX.MaskedInput({
			mask: '99:99:99',
			input: BX('beginWorktime'),
		});
		new BX.MaskedInput({
			mask: '99:99:99',
			input: BX('closeWorktime'),
		});

		var maskE = new BX.MaskedInput({
			mask: '99:99:99',
			input: BX('editedTime'),
		});
		var maskC = new BX.MaskedInput({
			mask: '99:99:99',
			input: BX('closedTime'),
		});


	});