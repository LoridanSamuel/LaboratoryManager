/* eslint-disable no-unused-vars */
async function request(oSelect) {
	const VALUE = oSelect.options[oSelect.selectedIndex].value;
	const NOW = new Date();
	var day = NOW.getDate();
	var month = NOW.getMonth()+1;
	const YEAR = NOW.getFullYear();
	if (day < 10) {day = '0' + day;}
	if (month < 10) {month = '0' + month;}
	const DATE = YEAR + '-' + month + '-' + day;
	document.getElementById('tableHeader').innerHTML = `<tr>
																												<th>N° de lot</th>
																												<th>Fournisseur</th>
																												<th>Référence fournisseur</th>
																												<th>Date de réception</th>
																												<th>Date d'ouverture</th>
																											</tr>`;

	let response = await fetch('getListData.php?name_Open=' + VALUE, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json; charset=utf-8'
		},
	});

	if(response.ok) {
		const DATA = await response.json();

		document.getElementById('tableBody').innerHTML = '';

		for(let i = 0; i < DATA.length; i++) {
			let receptionDate = DATA[i].reception_date.split('-');
			receptionDate = receptionDate[2] + '/' + receptionDate[1] + '/' + receptionDate[0];

			document.getElementById('tableBody').innerHTML += `<tr>
																													<td>${DATA[i].lot_number}</td>
																													<td>${DATA[i].seller}</td>
																													<td>${DATA[i].reference}</td>
																													<td>${receptionDate}</td>
																													<td>
																														<div class="table_button">
																															<input type="hidden" value="${DATA[i].id}" name="id"/>
																															<input type="date" name="opening_date" value="${DATE}" required/>
																															<button type="submit" name="formOpen" class="table--btn">Ouvrir</button>
																														</div>
																													</td>
																												</tr>`;
		}
	} else {
		alert('HTTP-Error: ' + response.status);
	}
}