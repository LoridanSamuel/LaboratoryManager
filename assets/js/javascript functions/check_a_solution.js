// eslint-disable-next-line no-unused-vars
async function selectAMaterial() {

	//Store the informations about the solution selected in the input

	const INPUT_SELECTION = (document.getElementById('mat_name').value).split(' préparé le ');
	const SOLUTION_INFO = INPUT_SELECTION[0];
	const PREPARATION_INFO = INPUT_SELECTION[1].split(' par ');

	let solutionSplittedAtType = '';
	let typeOfMaterial = '';

	if (SOLUTION_INFO.includes('(Réactif)')) {
		solutionSplittedAtType = (SOLUTION_INFO.split(' (Réactif)'))[0];
		typeOfMaterial = 'reagent';
	} else if (SOLUTION_INFO.includes('(Indicateur)')) {
		solutionSplittedAtType = (SOLUTION_INFO.split(' (Indicateur)'))[0];
		typeOfMaterial = 'indicator';
	} else if (SOLUTION_INFO.includes('(Standard)')) {
		solutionSplittedAtType = (SOLUTION_INFO.split(' (Standard)'))[0];
		typeOfMaterial = 'standard';
	} else if (SOLUTION_INFO.includes('(Etalon)')) {
		solutionSplittedAtType = (SOLUTION_INFO.split(' (Etalon)'))[0];
		typeOfMaterial = 'scale';
	}

	let solutionSplittedAtSolvent = solutionSplittedAtType.split(' dans ');
	let solvent = solutionSplittedAtSolvent[1];
	if (solutionSplittedAtSolvent.length > 2) {
		for(let i = 2; i < solutionSplittedAtSolvent.length; i++) {
			solvent += ' dans ' + solutionSplittedAtSolvent[i];
		}
	}

	let concentration = '';

	let solutionSplittedAtConcentration = solutionSplittedAtSolvent[0].split(' à ');
	if (solutionSplittedAtConcentration.length == 1) {
		concentration = '-';
	} else {
		concentration = solutionSplittedAtConcentration[1];
	}

	const MATERIAL_NAME = solutionSplittedAtConcentration[0];
	const PREPARATION_DATE = PREPARATION_INFO[0];
	const MAKER = PREPARATION_INFO[1];

	document.getElementById('hidden').innerHTML = `<input type="hidden" value="${typeOfMaterial}" name="typeOfMaterial"/>
											<input type="hidden" value="${MATERIAL_NAME}" name="matName"/>
											<input type="hidden" value="${concentration}" name="concentration"/>
											<input type="hidden" value="${solvent}" name="solvent"/>
											<input type="hidden" value="${PREPARATION_DATE}" name="preparation_date"/>
											<input type="hidden" value="${MAKER}" name="maker"/>`;

	//actions performed with these informations

	let response1 = await fetch('getListData.php?typeOfMaterial=' + typeOfMaterial + '&checkName=' + MATERIAL_NAME + '&concentration=' + concentration + '&solvent=' + solvent, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json; charset=utf-8'
		},
	});
	if(response1.ok) {
		const SOP_FOUND = await response1.json();
		const RESULTS_FROM_SOP = SOP_FOUND[0];
		var SOPPackaging = RESULTS_FROM_SOP.packaging;
		var SOPComponents = (RESULTS_FROM_SOP.used_products).split(' $ ');
		var SOPText = RESULTS_FROM_SOP.SOPtext;
	} else {
		alert('HTTP-Error: ' + response1.status);
	}

	let response2 = await fetch('getListData.php?typeOfMaterial=' + typeOfMaterial + '&checkSol=' + MATERIAL_NAME + '&concentration=' + concentration + '&solvent=' + solvent + '&prepDate=' + PREPARATION_DATE + '&maker=' + MAKER, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json; charset=utf-8'
		},
	});
	if(response2.ok) {
		const SOLUTION_FOUND = await response2.json();
		const RESULTS_FROM_SOLUTION = SOLUTION_FOUND[0];
		var SolutionPackaging = RESULTS_FROM_SOLUTION.packaging;
		var SolutionComponents = (RESULTS_FROM_SOLUTION.material_used).split(' $ ');
	} else {
		alert('HTTP-Error: ' + response2.status);
	}

	document.getElementById('sop').innerHTML = `<p>Le mode opératoire de préparation de cette solution est le suivant :</p><p>${SOPText}</p>`;

	//Check the theorical packaging and the real packaging then calculate the ratio between them
	let ratio = '';

	if(SOPPackaging == SolutionPackaging) {
		document.getElementById('pack').innerHTML = '<p class="remark">Le volume final de cette solution est le même que celui proposé par le mode opératoire.</p>';
		ratio = 1;
	} else {
		document.getElementById('pack').innerHTML = `<p class="alert">Le volume final de cette solution est de ${SolutionPackaging} alors que le mode opératoire prévoit un volume final de ${SOPPackaging}.
			De ce fait, les quantités théoriques à utiliser ci-dessous peuvent être érronées dans certains cas (dilutions en cascade, ...).</p>`;
		var sopPack = parseValue(SOPPackaging);
		var solPack = parseValue(SolutionPackaging);
		ratio = sopPack.value / solPack.value;
	}

	//Retrieve the theorical values for each components and eventually apply the ratio on it
	const COMPONENTS_TABLE_HEADER = `<table>
																		<thead>
																			<tr>
																				<th>Nom du composé</th>
																				<th>Type</th>
																				<th>n° de lot</th>
																				<th>Quantité théorique</th>
																				<th>Quantité réelle</th>
																			</tr>
																		</thead>
																		<tbody>`;

	let componentsTableBody = '';

	for (let i = 0; i < SOPComponents.length; i++) {
		const INFO_FROM_SOP = SOPComponents[i].split(' _ ');
		const COMPONENT_NAME_FROM_SOP = INFO_FROM_SOP[0];
		const COMPONENT_TYPE_FROM_SOP = INFO_FROM_SOP[1];
		const COMPONENT_QUANTITY_FROM_SOP = INFO_FROM_SOP[2];

		let sopCompQty=parseValue(COMPONENT_QUANTITY_FROM_SOP);
		const COMPONENT_QUANTITY_FROM_SOP_CORRECTED = (sopCompQty.value / ratio) + sopCompQty.unit;

		const INFO_FROM_SOLUTION = SolutionComponents[i].split(' _ ');
		const COMPONENT_LOT_FROM_SOLUTION = INFO_FROM_SOLUTION[1];
		const COMPONENT_QUANTITY_FROM_SOLUTION = INFO_FROM_SOLUTION[2] + sopCompQty.unit;

		let sopCompType = '';

		switch (COMPONENT_TYPE_FROM_SOP) {
		case 'RM':
			sopCompType = 'Produit pur';
			break;
		case 'reagent':
			sopCompType = 'Réactif';
			break;
		case 'indicator':
			sopCompType = 'Indicateur';
			break;
		case 'standard':
			sopCompType = 'Standard';
			break;
		case 'scale':
			sopCompType = 'Etalon';
			break;
		}

		componentsTableBody += `<tr>
															<td>${COMPONENT_NAME_FROM_SOP}</td>
															<td>${sopCompType}</td>
															<td>${COMPONENT_LOT_FROM_SOLUTION}</td>
															<td>${COMPONENT_QUANTITY_FROM_SOP_CORRECTED}</td>
															<td>${COMPONENT_QUANTITY_FROM_SOLUTION}</td>
														</tr>`;
	}

	const COMPONENTS_TABLE_FOOTER = '</tbody></table>';

	const COMPONENTS_TABLE = COMPONENTS_TABLE_HEADER + componentsTableBody + COMPONENTS_TABLE_FOOTER;

	document.getElementById('componentsTable').innerHTML = COMPONENTS_TABLE;
}

function parseValue(val) {
	let v = parseFloat(val);
	return {
		'value':v,
		'unit':val.replace(v,'')
	};
}