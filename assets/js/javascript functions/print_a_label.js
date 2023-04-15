/* eslint-disable no-unused-vars */
async function selectAMaterialType(TYPE_OF_MATERIAL) {
	document.getElementById('tableHeader').innerHTML='';
	document.getElementById('tableBody').innerHTML='';

	let response = await fetch('getListData.php?typeOfMaterial_Label=' + TYPE_OF_MATERIAL, {
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

		switch (TYPE_OF_MATERIAL) {
		case 'raw_material':
			for(let i = 0; i < DATA.length; i++) {
				oOption = document.createElement('option');
				oInner = document.createTextNode(DATA[i].mat_name);
				oOption.value = DATA[i].mat_name;

				oOption.appendChild(oInner);
				oSelect.appendChild(oOption);
			}
			break;
		case 'reagent':
			for(let i = 0; i < DATA.length; i++) {
				oOption = document.createElement('option');
				oInner = document.createTextNode(DATA[i].reag_name + ' à ' + DATA[i].concentration + ' dans ' + DATA[i].solvent);
				oOption.value = DATA[i].reag_name + ' à ' + DATA[i].concentration + ' dans ' + DATA[i].solvent;

				oOption.appendChild(oInner);
				oSelect.appendChild(oOption);
			}
			break;
		case 'indicator':
			for(let i = 0; i < DATA.length; i++) {
				oOption = document.createElement('option');
				oInner = document.createTextNode(DATA[i].ind_name + ' à ' + DATA[i].concentration + ' dans ' + DATA[i].solvent);
				oOption.value = DATA[i].ind_name + ' à ' + DATA[i].concentration + ' dans ' + DATA[i].solvent;

				oOption.appendChild(oInner);
				oSelect.appendChild(oOption);
			}
			break;
		case 'standard':
			for(let i = 0; i < DATA.length; i++) {
				oOption = document.createElement('option');
				oInner = document.createTextNode(DATA[i].std_name + ' à ' + DATA[i].concentration + ' dans ' + DATA[i].solvent);
				oOption.value = DATA[i].std_name + ' à ' + DATA[i].concentration + ' dans ' + DATA[i].solvent;

				oOption.appendChild(oInner);
				oSelect.appendChild(oOption);
			}
			break;
		case 'scale':
			for(let i = 0; i < DATA.length; i++) {
				oOption = document.createElement('option');
				oInner = document.createTextNode(DATA[i].sc_name + ' dans ' + DATA[i].solvent);
				oOption.value = DATA[i].sc_name + ' dans ' + DATA[i].solvent;

				oOption.appendChild(oInner);
				oSelect.appendChild(oOption);
			}
			break;
		}
	} else {
		alert('HTTP-Error: ' + response.status);
	}
}

function selectAFormat(FORMAT) {
	document.getElementById('labeltable').innerHTML = '';
	if(FORMAT === 'small') {
		let smallLines;
		let smallTable = '<table id="smallformat"">';
		for(let i = 1; i <= 12; i++) {
			smallLines = '<tr>';
			for(const LETTER of ['A', 'B', 'C']) {
				smallLines += `<td>
													<input class="radiobutton" type="radio" id="S${LETTER}${i}" name="labelposition" value="S${LETTER}${i}"/>
														<label class="radiolabel" for="S${LETTER}${i}">
															<div class="newradio"></div>
															<span class="radiotext">${LETTER}${i}</span>
														</label>`;
			}
			smallLines += '</tr>';
			smallTable += smallLines;
		}
		smallTable += '</table>';
		document.getElementById('labeltable').innerHTML = smallTable;
	} else if(FORMAT === 'big') {
		let bigLines;
		let bigTable = '<table id="bigformat"">';
		for(let i = 1; i <= 6; i++) {
			bigLines = '<tr>';
			for(const LETTER of ['A', 'B', 'C']) {
				bigLines += `<td>
													<input class="radiobutton" type="radio" id="B${LETTER}${i}" name="labelposition" value="B${LETTER}${i}"/>
														<label class="radiolabel" for="B${LETTER}${i}">
															<div class="newradio"></div>
															<span class="radiotext">${LETTER}${i}</span>
														</label>`;
			}
			bigLines += '</tr>';
			bigTable += bigLines;
		}
		bigTable += '</table>';
		document.getElementById('labeltable').innerHTML = bigTable;
	}
}

async function selectAMaterial(oSelect) {
	const INPUT_VALUE = oSelect.options[oSelect.selectedIndex].value;
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

	let concentration = '';
	let solvent = '';

	let INPUT_VALUE_SPLITTED_AT_SOLVENT = INPUT_VALUE.split(' dans ');

	if(INPUT_VALUE_SPLITTED_AT_SOLVENT.length != 1) {
		solvent = INPUT_VALUE_SPLITTED_AT_SOLVENT[1];
	} else {
		solvent = '';
	}

	if(INPUT_VALUE_SPLITTED_AT_SOLVENT.length > 2) {
		for(let i=2; i<INPUT_VALUE_SPLITTED_AT_SOLVENT.length; i++) {
			solvent = solvent.concat(' dans ', INPUT_VALUE_SPLITTED_AT_SOLVENT[i]);
		}
	}

	const INPUT_VALUE_SPLITTED_AT_CONCENTRATION = INPUT_VALUE_SPLITTED_AT_SOLVENT[0].split(' à ');

	const MATERIAL_NAME = INPUT_VALUE_SPLITTED_AT_CONCENTRATION[0];

	if(INPUT_VALUE_SPLITTED_AT_CONCENTRATION.length != 1) {
		concentration = INPUT_VALUE_SPLITTED_AT_CONCENTRATION[1];
	} else {
		concentration = '';
	}

	if(typeOfMaterial === 'raw_material') {
		document.getElementById('tableHeader').innerHTML = `<tr>
																													<th>N° de lot</th>
																													<th>Fournisseur</th>
																													<th>Référence fournisseur</th>
																													<th>Date de réception</th>
																													<th>Date d'ouverture</th><th>Impression</th>
																												</tr>`;

		let response = await fetch('getListData.php?typeOfMat_Label=' + typeOfMaterial + '&name=' + MATERIAL_NAME, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json; charset=utf-8'
			},
		});
		if(response.ok) {
			const DATA = await response.json();
			let oTable = document.getElementById('tableBody');
			oTable.innerHTML = '';

			for(var i = 0; i < DATA.length; i++) {
				let reception_date = DATA[i].reception_date.split('-');
				reception_date = reception_date[2] + '/' + reception_date[1] + '/' + reception_date[0];

				if(DATA[i].opening_date != null) {

					var opening_date = DATA[i].opening_date.split('-');
					opening_date = opening_date[2] + '/' + opening_date[1] + '/' + opening_date[0];

				} else {
					opening_date = '-';
				}

				oTable.innerHTML += `<tr>
																<td>${DATA[i].lot_number}</td>
																<td>${DATA[i].seller}</td>
																<td>${DATA[i].reference}</td>
																<td>${reception_date}</td>
																<td>${opening_date}</td>
																<td nowrap="nowrap">
																	<input type="hidden" value="${DATA[i].id}" name="id"/>
																	<button type="submit" name="formPrint" class="table--btn">Selectionner</button>
																</td>
															</tr>`;
			}
		} else {
			alert('HTTP-Error: ' + response.status);
		}
	} else if(typeOfMaterial === 'scale') {
		document.getElementById('tableHeader').innerHTML = `<tr>
																													<th>Conditionnement</th>
																													<th>Date de préparation</th>
																													<th>Préparateur</th>
																													<th>Impression</th>
																												</tr>`;

		let response = await fetch('getListData.php?typeOfMat_Label=' + typeOfMaterial + '&name=' + MATERIAL_NAME + '&solvent=' + solvent, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json; charset=utf-8'
			},
		});
		if(response.ok) {
			const DATA = await response.json();
			let oTable = document.getElementById('tableBody');
			oTable.innerHTML = '';

			for(let i = 0; i < DATA.length; i++) {
				let preparation_date = DATA[i].preparation_date.split('-');
				preparation_date = preparation_date[2] + '/' + preparation_date[1] + '/' + preparation_date[0];

				oTable.innerHTML += `<tr>
																<td>${DATA[i].packaging}</td>
																<td>${preparation_date}</td>
																<td>${DATA[i].maker}</td>
																<td nowrap="nowrap">
																	<input type="hidden" value="${DATA[i].id}" name="id"/>
																	<button type="submit" name="formPrint" class="table--btn">Selectionner</button>
																</td>
															</tr>`;
			}
		} else {
			alert('HTTP-Error: ' + response.status);
		}
	} else {
		document.getElementById('tableHeader').innerHTML = `<tr>
																													<th>Conditionnement</th>
																													<th>Date de préparation</th>
																													<th>Préparateur</th>
																													<th>Impression</th>
																												</tr>`;

		let response = await fetch('getListData.php?typeOfMat_Label=' + typeOfMaterial + '&name=' + MATERIAL_NAME + '&concentration=' + concentration + '&solvent=' + solvent, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json; charset=utf-8'
			},
		});
		if(response.ok) {
			const DATA = await response.json();
			var oTable = document.getElementById('tableBody');
			oTable.innerHTML = '';

			for(let i = 0; i < DATA.length; i++) {
				let preparation_date = DATA[i].preparation_date.split('-');
				preparation_date = preparation_date[2] + '/' + preparation_date[1] + '/' + preparation_date[0];

				oTable.innerHTML += `<tr>
																<td>${DATA[i].packaging}</td>
																<td>${preparation_date}</td>
																<td>${DATA[i].maker}</td>
																<td nowrap="nowrap">
																	<input type="hidden" value="${DATA[i].id}" name="id"/>
																	<button type="submit" name="formPrint" class="table--btn">Selectionner</button>
																</td>
															</tr>`;
			}
		} else {
			alert('HTTP-Error: ' + response.status);
		}
	}
}