/* eslint-disable no-unused-vars */
async function selectAMaterialType(TYPE_OF_MATERIAL) {
	document.getElementById('sop').innerHTML = '';
	document.getElementById('packsentence').innerHTML = '';
	document.getElementById('packcombo').innerHTML = '';
	document.getElementById('packcombo').style.display = 'none';
	document.getElementById('compoundtable').style.display = 'none';

	let response = await fetch('getListData.php?typeOfMaterial_Prepare=' + TYPE_OF_MATERIAL, {
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

		for(let i = 0; i < DATA.length; i++) {
			let dataName = '';

			if(TYPE_OF_MATERIAL == 'scale'){
				dataName = DATA[i].name + ' dans ' + DATA[i].solvent;
			} else {
				dataName = DATA[i].name + ' à ' + DATA[i].concentration + ' dans ' + DATA[i].solvent;
			}

			oOption = document.createElement('option');
			oInner = document.createTextNode(dataName);
			oOption.value = dataName;

			oOption.appendChild(oInner);
			oSelect.appendChild(oOption);
		}
	} else {
		alert('HTTP-Error: ' + response.status);
	}
}

async function request(oSelect) {

	let typeOfMaterial;

	let formulaire = document.getElementById('catform');
	let inputForm = formulaire.getElementsByTagName('input');
	
	for(let i = 0; i < inputForm.length; i++) {
		if(inputForm[i].type.toLowerCase()=='radio') {
			if(inputForm[i].checked) {
				typeOfMaterial = inputForm[i].value;
			}
		}
	}

	const INPUT_VALUE = oSelect.options[oSelect.selectedIndex].value;
	const VALUE_SPLITED_AT_SOLVENT = INPUT_VALUE.split(' dans ');

	let solvent = VALUE_SPLITED_AT_SOLVENT[1];

	if(VALUE_SPLITED_AT_SOLVENT.length > 2) {
		for(let i = 2; i < VALUE_SPLITED_AT_SOLVENT.length; i++) {
			solvent = solvent.concat(' dans ', VALUE_SPLITED_AT_SOLVENT[i]);
		}
	}

	const VALUE_SPLITED_AT_CONCENTRATION = VALUE_SPLITED_AT_SOLVENT[0].split(' à ');

	const MATERIAL_NAME = VALUE_SPLITED_AT_CONCENTRATION[0];
	const CONCENTRATION = VALUE_SPLITED_AT_CONCENTRATION[1];

	document.getElementById('compoundtable').style.display = 'block';
	document.getElementById('components').innerHTML = '';

	let response = await fetch('getListData.php?typeOfMat_Prepare=' + typeOfMaterial + '&name=' + MATERIAL_NAME + '&concentration=' + CONCENTRATION + '&solvent=' + solvent, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json; charset=utf-8'
		},
	});
	if(response.ok) {
		const DATA = await response.json();
		const COMPOUNDS = DATA[0].used_products.split(' $ ');
		const NUMBER_OF_COMPOUNDS = COMPOUNDS.length;
		let compinfo = [];
		let compname = [];
		let comptype = [];
		let compqty = [];
		let compdetail = [];

		document.getElementById('sop').innerHTML = DATA[0].SOPtext;
		document.getElementById('packsentence').innerHTML = `Le volume final original est de ${DATA[0].packaging}. Voulez-vous préparer un volume différent de solution?
																												<input type="hidden" name="ratio" id="ratio" value="1"/>
																												<input type="hidden" name="sol_number" id="sol_number" value="${DATA[0].sol_number}"/>
																												<input type="hidden" name="lifetime" id="lifetime" value="${DATA[0].lifetime}"/>
																												<input type="hidden" name="theoricalpack" id="theoricalpack" value="${DATA[0].packaging}"/>`;
		document.getElementById('pack').innerHTML = '<option value="'+ DATA[0].packaging +'">' + DATA[0].packaging + '</option>';
		document.getElementById('packcombo').style.display = 'block';
		document.getElementById('packsentence').innerHTML += '<input type="hidden" name="numberofcompounds" id="numberofcompounds" value="' + NUMBER_OF_COMPOUNDS + '"/>';

		for (let i = 0; i < NUMBER_OF_COMPOUNDS; i++) {
			compinfo[i] = COMPOUNDS[i].split(' _ ');
			compname[i] = compinfo[i][0];
			comptype[i] = compinfo[i][1];
			compqty[i] = compinfo[i][2];
			compdetail[i] = '';
			getData(compname[i], comptype[i], compqty[i], i);
		}
	} else {
		alert('HTTP-Error: ' + response.status);
	}
}

function selectANewPackaging() {
	let desiredPackaging = document.getElementById('packcombo').value;
	let ratio = document.getElementById('ratio').value;
	let componentsTable = document.getElementById('components');
	let components = componentsTable.querySelectorAll('tr');
	if(desiredPackaging != '') {
		let theoricalPackaging = parseValue(document.getElementById('theoricalpack').value);
		desiredPackaging = parseValue(desiredPackaging);
		if(theoricalPackaging.unit === desiredPackaging.unit || desiredPackaging.unit === '') {
			ratio = theoricalPackaging.value/desiredPackaging.value;
			document.getElementById('ratio').value = ratio;
			for(let element of components) {
				let cells = element.querySelectorAll('td');
				let theoricalQuantity = parseValue(cells[3].querySelector('input[type="hidden"]').value);
				theoricalQuantity.value = theoricalQuantity.value / ratio;
				cells[2].innerHTML = theoricalQuantity.value + theoricalQuantity.unit;
			}
		}
	} else {
		for(let element of components) {
			let cells = element.querySelectorAll('td');
			cells[2].innerHTML = cells[3].querySelector('input[type="hidden"]').value;
		}
	}
}

async function getData(name, type, quantity, i) {

	let typeOfMaterial;

	let formulaire = document.getElementById('catform');
	let inputForm = formulaire.getElementsByTagName('input');
	
	for(let i = 0; i < inputForm.length; i++) {
		if(inputForm[i].type.toLowerCase()=='radio') {
			if(inputForm[i].checked) {
				typeOfMaterial = inputForm[i].value;
			}
		}
	}

	if(type == 'RM') {
		let response = await fetch('getListData.php?RM_compound=' + name, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json; charset=utf-8'
			},
		});
		if(response.ok) {
			const DATA = await response.json();
			if (DATA.length == 1){
				document.getElementById('components').innerHTML += `<tr>
																															<td id="compname${i}">
																																${name}
																																<input type="hidden" name="compname${i}" value="${name}"/>
																															</td>
																															<td id="compdetail${i}">
																																(Produit pur - Lot n°${DATA[0].lot_number})
																																<input type="hidden" name="compdetail${i}" value="(Produit pur - Lot n°${DATA[0].lot_number})"/>
																															</td>
																															<td id="compqty${i}">${quantity}</td>
																															<td id="realqty${i}">
																																<input type="text" name="realqty${i}"/>
																																<input type="hidden" name="compqty${i}" value="${quantity}"/>
																															</td>
																														</tr>`;
			} else {
				let lot = '<select name = "compdetail' + i + '">';
				for (let j = 0; j < DATA.length; j++) {
					lot += '<option value="(Produit pur - Lot n°' + DATA[j].lot_number + ')">(Produit pur - Lot n°' + DATA[j].lot_number + ')</option>';
				}
				lot += '</select>';
				document.getElementById('components').innerHTML += `<tr>
																															<td id="compname${i}">
																																${name}
																																<input type="hidden" name="compname${i}" value="${name}"/>
																															</td>
																															<td id="compdetail${i}">
																																${lot}
																															</td>
																															<td id="compqty${i}">
																																${quantity}
																															</td>
																															<td id="realqty${i}">
																																<input type="text" name="realqty${i}"/>
																															</td>
																														</tr>`;
			}
		} else {
			alert('HTTP-Error: ' + response.status);
		}
	} else if (type == 'scale') {
		const COMPONENT_NAME_SPLITTED = name.split(' dans ');
		const MATERIAL_NAME = COMPONENT_NAME_SPLITTED[0];
		let solvent = COMPONENT_NAME_SPLITTED[1];
		if(COMPONENT_NAME_SPLITTED > 2) {
			for(let i = 2; i < COMPONENT_NAME_SPLITTED.length; i++) {
				solvent = solvent.concat(' dans ', COMPONENT_NAME_SPLITTED[i]);
			}
		}

		let response = await fetch('getListData.php?Sc_compound=' + MATERIAL_NAME + '&solvent=' + solvent, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json; charset=utf-8'
			},
		});
		if(response.ok) {
			const DATA = await response.json();
			if (DATA == 0){
				document.getElementById('components').innerHTML += `<tr>
																															<td id="compname${i}">
																																${name}
																																<input type="hidden" name="compname${i}" value="${name}"/>
																															</td>
																															<td id="compdetail${i}" class="bold red">
																																(Etalon - Expiré ou non trouvé)
																																<input type="hidden" name="compdetail${i}" value="(Etalon)"/>
																															</td>
																															<td id="compqty${i}">
																																${quantity}
																															</td>
																															<td id="realqty${i}">
																																<input type="text" name="realqty${i}"/>
																															</td>
																														</tr>`;
			} else {
				document.getElementById('components').innerHTML += `<tr>
																															<td id="compname${i}">
																																${name}
																																<input type="hidden" name="compname${i}" value="${name}"/>
																															</td>
																															<td id="compdetail${i}" class="white">
																																(Etalon)
																																<input type="hidden" name="compdetail${i}" value="(Etalon)"/>
																															</td>
																															<td id="compqty${i}">
																																${quantity}
																															</td>
																															<td id="realqty${i}">
																																<input type="text" name="realqty${i}"/>
																															</td>
																														</tr>`;
			}
		} else {
			alert('HTTP-Error: ' + response.status);
		}
	} else {
		const COMPONENT_NAME_SPLITTED_AT_SOLVENT = name.split(' dans ');
		let solvent = COMPONENT_NAME_SPLITTED_AT_SOLVENT[1];
		if(COMPONENT_NAME_SPLITTED_AT_SOLVENT > 2) {
			for(let i = 2; i < COMPONENT_NAME_SPLITTED_AT_SOLVENT.length; i++) {
				solvent = solvent.concat(' dans ', COMPONENT_NAME_SPLITTED_AT_SOLVENT[i]);
			}
		}
		const COMPONENT_NAME_SPLITTED_AT_CONCENTRATION = COMPONENT_NAME_SPLITTED_AT_SOLVENT[0].split(' à ');
		const MATERIAL_NAME = COMPONENT_NAME_SPLITTED_AT_CONCENTRATION[0];
		const CONCENTRATION = COMPONENT_NAME_SPLITTED_AT_CONCENTRATION[1];

		let response = await fetch('getListData.php?Sol_compound=' + MATERIAL_NAME + '&concentration=' + CONCENTRATION + '&solvent=' + solvent + '&type=' + type, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json; charset=utf-8'
			},
		});
		if(response.ok) {
			const DATA = await response.json();
			switch(type) {
			case 'reagent':
				typeOfMaterial = 'Réactif';
				break;
			case 'indicator':
				typeOfMaterial = 'Indicateur';
				break;
			case 'standard':
				typeOfMaterial = 'Standard';
				break;
			}

			if (DATA == 0){
				document.getElementById('components').innerHTML += `<tr>
																															<td id="compname${i}">
																																${name}
																																<input type="hidden" name="compname${i}" value="${name}"/>
																															</td>
																															<td id="compdetail${i}" class="alert">
																																(${typeOfMaterial} - Expiré ou non trouvé)
																																<input type="hidden" name="compdetail${i}" value="(${typeOfMaterial})"/>
																															</td>
																															<td id="compqty${i}">
																																${quantity}
																															</td>
																															<td id="realqty${i}">
																																<input type="text" name="realqty${i}"/>
																															</td>
																														</tr>`;
			} else {
				document.getElementById('components').innerHTML += `<tr>
																															<td id="compname${i}">
																																${name}
																																<input type="hidden" name="compname${i}" value="${name}"/>
																															</td>
																															<td id="compdetail${i}" class="white">
																																(${typeOfMaterial})
																																<input type="hidden" name="compdetail${i}" value="(${typeOfMaterial})"/>
																															</td>
																															<td id="compqty${i}">
																																${quantity}
																															</td>
																															<td id="realqty${i}">
																																<input type="text" name="realqty${i}"/>
																															</td>
																														</tr>`;
			}
		} else {
			alert('HTTP-Error: ' + response.status);
		}
	}
}

function parseValue(val) {
	let v = parseFloat(val);
	return {
		'value':v,
		'unit':val.replace(v,'')
	};
}