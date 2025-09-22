//Archivo a usar en caso de requerir modularidad y uso de la funcionalidad de rango fechas en otro lugar

/*export const formatNumber = (number, digits = 2) => {
	number = number && number != '' ? number : 0;
	return new Intl.NumberFormat("de-DE", {
			maximumFractionDigits: digits,
	}).format(number);
};

export const showAlertLoading = (title, subMessage) => {
	return Swal.fire({
		title: title,
		text: 'Esto puede tardar unos segundos...',
		footer: subMessage,
		allowOutsideClick: false,
		didOpen: () => {
			Swal.showLoading();
		},
	});
}
/**
 * Permite realizar solicitudes a una API, manejar la respuesta y
 * cualquier error que pueda ocurrir durante el proceso.
 */

/*export const fetchAPIURL = async (apiURL, params = {}, method = 'GET') => {
	const token = document.querySelector('meta[name = "csrf-token"]').getAttribute('content');
	let requestInfo = {};
	if ( method === 'POST' ) {
		requestInfo = {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': token
			},
			body: JSON.stringify(params)
		};
	}
	try {
		const response = await fetch(apiURL, requestInfo);
		if ( response.ok ) {
			const data = await response.json();
			return data;
		}
		throw new Error(response);
	} catch (err) {
		console.log(err)
		return null;
	}
};

export const sweetAlert = (msg, icon, timer = 1500) => {
	Swal.fire({
		position: 'center',
		icon: icon,
		title: msg,
		showConfirmButton: false,
		backdrop: false,
		allowEscapeKey: false,
		timer: timer
	});
}

export const setRangeDates = (options) => {
	const {
		element,
		startDate = moment(),
		endDate = moment(),
		ranges = {
			Hoy: [moment(), moment()],
			Ayer: [moment().subtract(1, "days"), moment().subtract(1, "days")],
			"Últimos 7 Días": [moment().subtract(6, "days"), moment()],
			"Últimos 30 Días": [moment().subtract(29, "days"), moment()],
			"Este Mes": [moment().startOf("month"), moment().endOf("month")],
			"Ultimo Mes": [
				moment().subtract(1, "month").startOf("month"),
				moment().subtract(1, "month").endOf("month"),
			],
			Todo: [moment().subtract(20, "years"), moment()],
		},
		eventDateRange = function (start, end) {
			let fecha1 = start.format("YYYY-MM-DD");
			let fecha2 = end.format("YYYY-MM-DD");
		}
	} = options;
	$(element).daterangepicker({
		showWeekNumbers: true,
		showDropdowns: true,
		autoApply: true,
		ranges,
		locale: {
			format: "DD-MM-YYYY",
			separator: " - ",
			applyLabel: "Aplicar",
			cancelLabel: "Cancelar",
			fromLabel: "Desde",
			toLabel: "Hasta",
			customRangeLabel: "Personalizado",
			weekLabel: "W",
			daysOfWeek: ["Do", "Lu", "Mar", "Mie", "Jue", "Vie", "Sab"],
			monthNames: [
				"Enero",
				"Febrero",
				"Marzo",
				"Abril",
				"Mayo",
				"Junio",
				"Julio",
				"Agosto",
				"Septiembre",
				"Octubre",
				"Noviembre",
				"Diciembre",
			],
			firstDay: 1,
		},
		alwaysShowCalendars: true,
		startDate,
		endDate,
		opens: "center",
		cancelClass: "btn-danger",
	}, eventDateRange);
}*/
