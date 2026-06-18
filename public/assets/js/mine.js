function deleteConfirm(url){
	swal({
		title: "Apakah Anda yakin?",
		text: "Setelah dihapus, data tidak dapat dikembalikan.",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	})
	.then((willDelete) => {
		if (willDelete) {
			window.location.href = url;
		}
	});
}

function validasiConfirm(formId, text){
	swal({
		title: "Apakah Anda yakin?",
		text: text,
		icon: "warning",
		buttons: true,
	})
	.then((confirmed) => {
		if (confirmed) {
			document.getElementById(formId).submit();
		}
	});
}

function printArea(elementId){
	var content = document.getElementById(elementId).innerHTML;
	var printWindow = window.open('', '_blank');
	printWindow.document.write('<!DOCTYPE html><html><head><title>Cetak Surat Ijin</title>');
	printWindow.document.write('<style>@page{size: 210mm 148mm; margin: 0;} html,body{margin:0;padding:0;width:100%;} body{font-family: Arial, sans-serif; font-size: 9pt; box-sizing:border-box; padding: 8mm;} *{box-sizing:border-box;} table{width:100%;border-collapse:collapse;} img{max-width:100%;} td{padding:2px 6px;} .center{text-align:center;} .bold{font-weight:bold;} .underline{text-decoration:underline;} hr.atas{border-top:3px solid black;} hr.bawah{border-top:1px solid black;margin-top:-8px;}</style>');
	printWindow.document.write('</head><body>');
	printWindow.document.write(content);
	printWindow.document.write('</body></html>');
	printWindow.document.close();
	printWindow.focus();
	setTimeout(function(){
		printWindow.print();
		printWindow.close();
	}, 300);
}

function loadDatePicker(element){
	if(element == '.datepicker'){
		comp = {
			calendar: true,
			date: true,
			month: true,
			year: true,
			decades: true,
			clock: false,
			hours: false,
			minutes: false,
			seconds: false,
			useTwentyfourHour: false
		}
		form = 'YYYY-MM-DD'
	}else{
		comp = {
			calendar: true,
			date: true,
			month: true,
			year: true,
			decades: true,
			clock: true,
			hours: true,
			minutes: true,
			seconds: false,
			useTwentyfourHour: true
		}
		form = 'YYYY-MM-DD hh:mm'
	}
	$(element).each(function(){
		let d = new tempusDominus.TempusDominus(this, 
		{
			display: {
				icons: {
					type: 'icons',
					time: 'fa fa-solid fa-clock',
					date: 'fa fa-solid fa-calendar-alt',
					up: 'fa fa-solid fa-arrow-up',
					down: 'fa fa-solid fa-arrow-down',
					previous: 'fa fa-solid fa-chevron-left',
					next: 'fa fa-solid fa-chevron-right',
					today: 'fa fa-solid fa-dot-circle',
					clear: 'fa fa-solid fa-trash',
					close: 'fa fa-solid fa-times'
				},
				sideBySide: false,
				calendarWeeks: false,
				viewMode: 'calendar',
				toolbarPlacement: 'bottom',
				keepOpen: false,
				buttons: {
					today: true,
					clear: false,
					close: false
				},
				components: comp,
				inline: false,
				theme: 'light'
			},
			localization: {
				today: 'Waktu Sekarang',
			}
		
		});
		d.dates.formatInput = function(date) { {return moment(date).format(form) } }
	});
}