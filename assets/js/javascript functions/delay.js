/* eslint-disable no-unused-vars */
async function request(oSelect) {
	const VALUE = oSelect.options[oSelect.selectedIndex].value;
	const NOW = new Date();
	let day = NOW.getDate();
	let month = NOW.getMonth()+1;
	const YEAR = NOW.getFullYear();

	if(day < 10) {day = '0' + day;}
	if(month < 10) {month = '0' + month;}

	const DATE = YEAR + '-' + month + '-' + day;

	document.getElementById('tableHeader').innerHTML = `<tr>
																												<th>N° de lot</th>
																												<th>Fournisseur</th>
																												<th>Référence fournisseur</th>
																												<th>Date de réception</th>
																												<th>Date de prolongation</th>
																											</tr>`;

	let response = await fetch('getListData.php?name_Delay=' + VALUE, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json; charset=utf-8'
		},
	});

	if(response.ok) {
		const DATA = await response.json();

		document.getElementById('tableBody').innerHTML = '';

		for(let i = 0; i < DATA.length; i++) {
			let reception_date = DATA[i].reception_date.split('-');
			reception_date = reception_date[2] + '/' + reception_date[1] + '/' + reception_date[0];

			document.getElementById('tableBody').innerHTML += `<tr>
																														<td>${DATA[i].lot_number}</td>
																														<td>${DATA[i].seller}</td>
																														<td>${DATA[i].reference}</td>
																														<td>${reception_date}</td>
																														<td>
																															<div class="table_button">
																																<input type="hidden" value="${DATA[i].id}" name="id"/>
																																<input type="hidden" value="${DATA[i].purity}" name="purity"/>
																																<input type="hidden" value="${DATA[i].purity_retested}" name="purity_retested"/>
																																<input type="date" name="retesting_date" value="${DATE}" required/>
																																<button type="submit" name="formPurity" class="table--btn">Prolonger</button>
																															</div>
																														</td>
																													</tr>`;
		}
	} else {
		alert('HTTP-Error: ' + response.status);
	}
}