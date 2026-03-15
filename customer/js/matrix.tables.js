
$(document).ready(function(){
	function applyMobileCardTable(table) {
		var headers = [];
		table.find('thead th').each(function(index) {
			headers[index] = $.trim($(this).text()).replace(/\s+/g, ' ') || 'Field';
		});

		table.find('tbody').each(function() {
			$(this).find('tr').each(function() {
				$(this).children('td').each(function(index) {
					$(this).attr('data-label', headers[index] || 'Field');
				});
			});
		});

		if ($(window).width() <= 767) {
			table.addClass('mobile-card-table');
		} else {
			table.removeClass('mobile-card-table');
		}
	}

	function syncMobileCardTables() {
		$('.data-table').each(function() {
			applyMobileCardTable($(this));
		});
	}
	
	$('.data-table').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"sDom": '<""l>t<"F"fp>'
	});

	syncMobileCardTables();
	$(window).on('resize', syncMobileCardTables);
	
	$('input[type=checkbox],input[type=radio],input[type=file]').uniform();
	
	$('select').select2();
	
	$("span.icon input:checkbox, th input:checkbox").click(function() {
		var checkedStatus = this.checked;
		var checkbox = $(this).parents('.widget-box').find('tr td:first-child input:checkbox');		
		checkbox.each(function() {
			this.checked = checkedStatus;
			if (checkedStatus == this.checked) {
				$(this).closest('.checker > span').removeClass('checked');
			}
			if (this.checked) {
				$(this).closest('.checker > span').addClass('checked');
			}
		});
	});	
});
