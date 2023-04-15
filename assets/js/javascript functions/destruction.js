/* eslint-disable no-unused-vars */
async function selectAMaterialType(TYPE_OF_MATERIAL) {
	document.getElementById('tableHeader').innerHTML = '';
	document.getElementById('tableBody').innerHTML = '';

	let response = await fetch('getListData.php?typeOfMaterial_Destruction=' + TYPE_OF_MATERIAL, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json; charset=utf-8'
		},
	});

	if(response.ok) {
		const DATA = await response.json();
		let oSelect = document.getElementById('nameList');
		let oOption, oInner;

		oSelect.innerHTML = '<option value="none" disabled selected>Selection</option>';
		let oOptionValue = '';

		for(let i = 0; i < DATA.length; i++) {
			switch (TYPE_OF_MATERIAL) {
			case 'raw_material' :
				oOptionValue = DATA[i].mat_name;
				break;
			case 'reagent' :
				oOptionValue = DATA[i].reag_name + ' à ' + DATA[i].concentration + ' dans ' + DATA[i].solvent;
				break;
			case 'indicator' :
				oOptionValue = DATA[i].ind_name + ' à ' + DATA[i].concentration + ' dans ' + DATA[i].solvent;
				break;
			case 'standard' :
				oOptionValue = DATA[i].std_name + ' à ' + DATA[i].concentration + ' dans ' + DATA[i].solvent;
				break;
			case 'scale' :
				oOptionValue = DATA[i].sc_name + ' dans ' + DATA[i].solvent;
				break;
			}
			oOption = document.createElement('option');
			oInner = document.createTextNode(oOptionValue);
			oOption.value = oOptionValue;
			oOption.appendChild(oInner);
			oSelect.appendChild(oOption);
		}
	} else {
		alert('HTTP-Error: ' + response.status);
	}
}


async function selectMaterial(oSelect) {
	const INPUT_VALUE = oSelect.options[oSelect.selectedIndex].value;
	const VALUE_SPLITED_AT_SOLVENT = INPUT_VALUE.split(' dans ');
	let solvent = VALUE_SPLITED_AT_SOLVENT[1];

	if(VALUE_SPLITED_AT_SOLVENT.length > 2) {
		for(let i=2; i<VALUE_SPLITED_AT_SOLVENT.length; i++) {
			solvent = solvent.concat(' dans ', VALUE_SPLITED_AT_SOLVENT[i]);
		}
	}

	const VALUE_SPLITED_AT_CONCENTRATION = VALUE_SPLITED_AT_SOLVENT[0].split(' à ');
	const MATERIAL_NAME = VALUE_SPLITED_AT_CONCENTRATION[0];
	const CONCENTRATION = VALUE_SPLITED_AT_CONCENTRATION[1];

	const NOW = new Date();
	let day = NOW.getDate();
	let month = NOW.getMonth()+1;
	const YEAR = NOW.getFullYear();
	if (day < 10) {day = '0' + day;}
	if (month < 10) {month = '0' + month;}
	const DATE = YEAR + '-' + month + '-' + day;

	let response;
	let typeOfMaterial;

	let formulaire = document.getElementById('formtypeofmaterial');
	let inputForm = formulaire.getElementsByTagName('input');
	
	for(let i = 0; i < inputForm.length; i++) {
		if(inputForm[i].type.toLowerCase()=='radio') {
			if(inputForm[i].checked) {
				typeOfMaterial = inputForm[i].value;
			}
		}
	}

	switch(typeOfMaterial) {
	case 'raw_material':
		document.getElementById('tableHeader').innerHTML = `<tr>
																													<th>N° de lot</th>
																													<th>Fournisseur</th>
																													<th>Référence fournisseur</th>
																													<th>Date de réception</th>
																													<th>Date d'ouverture</th>
																													<th>Date de fermeture</th>
																												</tr>`;

		response = await fetch('getListData.php?typeOfMat_Destruction=' + typeOfMaterial + '&name=' + MATERIAL_NAME, {
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
				let opening_date = DATA[i].opening_date.split('-');
				opening_date = opening_date[2] + '/' + opening_date[1] + '/' + opening_date[0];

				document.getElementById('tableBody').innerHTML += `<tr>
																															<td>${DATA[i].lot_number}</td>
																															<td>${DATA[i].seller}</td>
																															<td>${DATA[i].reference}</td>
																															<td>${reception_date}</td>
																															<td>${opening_date}</td>
																															<td>
																																<div class="table_button">
																																	<input type="hidden" value="${DATA[i].id}" name="id"/>
																																	<input type="date" name="destruction_date" value="${DATE}" required/>
																																	<button type="submit" name="formDestruct" class="table--btn">Détruire</button>
																																</div>
																															</td>
																														</tr>`;
			}
		} else {
			alert('HTTP-Error: ' + response.status);
		}
		break;
	case 'scale':
		document.getElementById('tableHeader').innerHTML = `<tr>
																													<th>Conditionnement</th>
																													<th>Date de préparation</th>
																													<th>Préparateur</th>
																													<th>Date de fermeture</th>
																												</tr>`;

		response = await fetch('getListData.php?typeOfMat_Destruction=' + typeOfMaterial + '&name=' + MATERIAL_NAME + '&solvent=' + solvent, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json; charset=utf-8'
			},
		});
					
		if(response.ok) {
			const DATA = await response.json();
			document.getElementById('tableBody').innerHTML = '';

			for(let i = 0; i < DATA.length; i++) {
				let preparation_date = DATA[i].preparation_date.split('-');
				preparation_date = preparation_date[2] + '/' + preparation_date[1] + '/' + preparation_date[0];

				document.getElementById('tableBody').innerHTML += `<tr>
																													<td>${DATA[i].packaging}</td>
																													<td>${preparation_date}</td>
																													<td>${DATA[i].maker}</td>
																													<td>
																														<div class="table_button">
																															<input type="hidden" value="${DATA[i].id}" name="id"/>
																															<input type="date" name="destruction_date" value="${DATE}" required/>
																															<button type="submit" name="formDestruct" class="table--btn">Détruire</button>
																														</div>
																													</td>
																												</tr>`;
			}
		} else {
			alert('HTTP-Error: ' + response.status);
		}
		break;
	default:
		document.getElementById('tableHeader').innerHTML = `<tr>
																													<th>Conditionnement</th>
																													<th>Date de préparation</th>
																													<th>Préparateur</th>
																													<th>Date de fermeture</th>
																												</tr>`;

		response = await fetch('getListData.php?typeOfMat_Destruction=' + typeOfMaterial + '&name=' + MATERIAL_NAME + '&concentration=' + CONCENTRATION + '&solvent=' + solvent, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json; charset=utf-8'
			},
		});
					
		if(response.ok) {
			const DATA = await response.json();
			document.getElementById('tableBody').innerHTML = '';

			for(let i = 0; i < DATA.length; i++) {
				let preparation_date = DATA[i].preparation_date.split('-');
				preparation_date = preparation_date[2] + '/' + preparation_date[1] + '/' + preparation_date[0];

				document.getElementById('tableBody').innerHTML += `<tr>
																																<td>${DATA[i].packaging}</td>
																																<td>${preparation_date}</td>
																																<td>${DATA[i].maker}</td>
																																<td>
																																	<div class="table_button">
																																		<input type="hidden" value="${DATA[i].id}" name="id"/>
																																		<input type="date" name="destruction_date" value="${DATE}" required/>
																																		<button type="submit" name="formDestruct" class="table--btn">Détruire</button>
																																	</div>
																																</td>
																															</tr>`;
			}
		} else {
			alert('HTTP-Error: ' + response.status);
		}
		break;
	}
}