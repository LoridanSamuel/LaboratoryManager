/* eslint-disable no-unused-vars */

async function selectASolutionType(TYPE_OF_SOLUTION) {
	let response = await fetch('getListData.php?typeOfSolution=' + TYPE_OF_SOLUTION, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json; charset=utf-8'
		},
	});
	if(response.ok) {
		const DATA = await response.json();
		let oSelect = document.getElementById('sol');
		let oOption, oInner;

		oSelect.innerHTML = '<option value="none" disabled selected>Selection</option>';

		for(let i = 0; i < DATA.length; i++) {
			oOption = document.createElement('option');
			switch (TYPE_OF_SOLUTION) {
			case 'RM':
				oInner = document.createTextNode(DATA[i].mat_number + ' - ' + DATA[i].mat_name);
				oOption.value = DATA[i].mat_number + ' - ' + DATA[i].mat_name + ' ' + TYPE_OF_SOLUTION;
				break;
			case 'scale':
				oInner = document.createTextNode(DATA[i].sol_number + ' - ' + DATA[i].name + ' dans ' + DATA[i].solvent);
				oOption.value = DATA[i].sol_number + ' - ' + DATA[i].name + ' dans ' + DATA[i].solvent + ' ' + TYPE_OF_SOLUTION;
				break;
			default:
				oInner = document.createTextNode(DATA[i].sol_number + ' - ' + DATA[i].name + ' à ' + DATA[i].concentration + ' dans ' + DATA[i].solvent);
				oOption.value = DATA[i].sol_number + ' - ' + DATA[i].name + ' à ' + DATA[i].concentration + ' dans ' + DATA[i].solvent + ' ' + TYPE_OF_SOLUTION;
				break;
			}
			oOption.appendChild(oInner);
			oSelect.appendChild(oOption);
		}
	} else {
		alert('HTTP-Error: ' + response.status);
	}
}

//fonctions d'ajout de valeur dans la visualisation du test
function addHeader() {
	if (document.getElementById('test_ref').value != '' && document.getElementById('test_name').value != '' && document.getElementById('method').value != '') {
		document.getElementById('testHeader').classList.toggle('noDisplay');
		document.getElementById('testReference').innerHTML = document.getElementById('test_ref').value;
		document.getElementById('testHeader').innerHTML = '<th id="testName" class="WStestName">' + document.getElementById('test_name').value + '</th>';
		document.getElementById('testHeader').innerHTML += '<th id="testMethod" class="WStestMethod">' + document.getElementById('method').value + '</th>';

		document.getElementById('testCreationForm').innerHTML = '<input type="hidden" name="test_ref" id="test_ref" value="' + document.getElementById('test_ref').value + '">';
		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="test_name" id="test_name" value="' + document.getElementById('test_name').value + '">';
		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="method" id="method" value="' + document.getElementById('method').value + '">';

		document.getElementById('headerTest').style.display = 'none';
		document.getElementById('SOPTest').style.display = 'block';
	} else {
		alert('Un ou plusieurs champs ne sont pas renseignés!');
		document.getElementById('testHeader').classList.toggle('noDisplay');
	}
}

function addSOP() {
	if(document.getElementById('SOP').value == '') {
		document.getElementById('testText').innerHTML = '';
		document.getElementById('testText').style.display = 'none';
		document.getElementById('SOPTest').classList.toggle('noDisplay');
		document.getElementById('limitsTest').classList.toggle('noDisplay');
	} else {
		document.getElementById('testText').style.display = 'table-row';
		document.getElementById('testText').innerHTML = '<td id="testSOP" class="WStestSOP">' + document.getElementById('SOP').value.replace(/\n/g,'<br/>') + '</td>';

		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="SOP" id="SOPhidden" value="' + document.getElementById('SOP').value.replace(/\n/g,'<br/>') + '">';

		document.getElementById('SOPTest').style.display = 'none';
		document.getElementById('solTest').style.display = 'block';
	}
}

function addASol() {
	if(document.getElementById('sol').value) {
		const LAST_LETTER = document.getElementById('sol').value.slice(-1);

		let stringFromInputWithoutType = '';
		let typeOfMaterial = '';

		switch(LAST_LETTER) {
		case 'M':
			stringFromInputWithoutType = document.getElementById('sol').value.slice(0, -3);
			typeOfMaterial = 'RM';
			break;
		case 't':
			stringFromInputWithoutType = document.getElementById('sol').value.slice(0, -8);
			typeOfMaterial = 'Reagent';
			break;	
		case 'r':
			stringFromInputWithoutType = document.getElementById('sol').value.slice(0, -10);
			typeOfMaterial = 'Indicator';
			break;
		case 'd':
			stringFromInputWithoutType = document.getElementById('sol').value.slice(0, -9);
			typeOfMaterial = 'Standard';
			break;
		case 'e':
			stringFromInputWithoutType = document.getElementById('sol').value.slice(0, -6);
			typeOfMaterial = 'Scale';
			break;
		}

		let stringSplittedAtNumber = stringFromInputWithoutType.split(' - ');
		let solutionNumber = stringSplittedAtNumber[0];
		let solutionName = stringSplittedAtNumber[1];
		if(stringSplittedAtNumber.length > 2) {
			for(let i = 2; i < stringSplittedAtNumber.length; i++) {
				solutionName += ' - ' + stringSplittedAtNumber[i];
			}
		}

		if(document.getElementById('testReagents') != null) {
			let i = parseInt(document.getElementById('numberOfSol').value) + 1;
			document.getElementById('numberOfSol').value = i;
			document.getElementById('testReagents').innerHTML += `<div id="solution${i}" class="solution ${typeOfMaterial}">
																		<div id="solutionNumber${i}">${solutionNumber}</div>
																		<div id="solutionName${i}"> ${solutionName}</div>
																	</div>`;
		} else {
			document.getElementById('testCreationForm').innerHTML += '<input type="hidden" id="numberOfSol" name="numberOfSol" value=1>';
			document.getElementById('testText').innerHTML += `<td id="testReagents">
															<div id="solution1" class="solution ${typeOfMaterial}">
																<div id="solutionNumber1">${solutionNumber}</div>
																<div id="solutionName1"> ${solutionName}</div>
															</div>
														</td>`;
		}
	}
}

function deleteASol() {
	let i = parseInt(document.getElementById('numberOfSol').value);

	if(i == 1) {
		document.getElementById('testReagents').remove();
		document.getElementById('numberOfSol').remove();
	} else {
		document.getElementById('solution' + i).remove();
		document.getElementById('numberOfSol').value = i - 1;
	}
}

function addSol() {
	if(document.getElementById('testReagents')) {
		const NUMBER_OF_SOLUTION = parseInt(document.getElementById('numberOfSol').value);
		let reagentsSumup = '';
		let reagentType = '';
		for(let i = 1; i <= NUMBER_OF_SOLUTION; i++) {
			switch(document.getElementById('solutionNumber' + i).style.color) {
			case 'rgb(244, 246, 248)':
				reagentType = 'RM';
				break;
			case 'rgb(0, 0, 0)':
				reagentType = 'RM';
				break;
			case 'rgb(255, 0, 0)':
				reagentType = 'reagent';
				break;
			case 'rgb(0, 128, 0)':
				reagentType = 'indicator';
				break;
			case 'rgb(0, 193, 255)':
				reagentType = 'standard';
				break;
			case 'rgb(255, 165, 0)':
				reagentType = 'scale';
				break;
			}
			reagentsSumup += document.getElementById('solutionName' + i).innerHTML + ' _ ' + reagentType + ' $ ';
		}

		reagentsSumup = reagentsSumup.substring(0, reagentsSumup.length - 3);

		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="reagents" id="reagents" value="' + reagentsSumup + '">';
	} else {
		document.getElementById('testSOP').setAttribute('colspan', 2);
	}
	document.getElementById('solTest').style.display = 'none';
	document.getElementById('limitsTest').style.display = 'block';
}

function addLimits() {
	document.getElementById('testNumbers').style.display = 'table-row';

	if(document.getElementById('testLimits')) {
		document.getElementById('testLimits').remove();
	}
	if(document.getElementById('testFormula')) {
		document.getElementById('testFormula').remove();
	}
	if(document.getElementById('resultSentence').value != '') {
		document.getElementById('resultSentence').innerHTML = '';
	}

	if(document.getElementById('notLimited').checked && (document.getElementById('noCalculation').checked || document.getElementById('calculation').checked)) {
		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="passes_test" id="passes_test" value="yes">';
		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="limits" id="limits" value="no">';
		document.getElementById('testNumbers').style.display = 'none';
		document.getElementById('limitsTest').style.display = 'none';
		document.getElementById('resultTest').style.display = 'block';
		if(document.getElementById('result').length) {
			document.getElementById('result').remove();
		}
		document.getElementById('resultSentence').innerHTML = `<p>La conformité du test résulte d'une comparaison avec un standard ou de la présence d'une réaction chimique spécifique (virage coloré, turbidité, etc.).</p>
																<p>Veuillez saisir le texte qui fera office de conclusion au test :</p>
																<textarea id='result' placeholder='Texte de conclusion'></textarea>`;
	} else if (document.getElementById('limited').checked && document.getElementById('noCalculation').checked) {
		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="unit" id="unit" value="' + document.getElementById('testUnit').value + '">';
		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="passes_test" id="passes_test" value="yes">';
		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="limits" id="limits" value="yes">';
		document.getElementById('testNumbers').innerHTML = `<td id="testLimits" colspan=2 class="WStestLimits">
																<table>
																	<thead>
																		<tr>
																			<th>Bornes:</th>
																		</tr>
																	</thead>
																	<tbody>
																		<tr>
																			<td id="limitsdisplayed">_____ - _____  ${document.getElementById('testUnit').value}</td>
																		</tr>
																	</tbody>
																</table>
															</td>`;
		document.getElementById('limitsTest').style.display = 'none';
		document.getElementById('resultTest').style.display = 'block';

		if(document.getElementById('result').length) {
			document.getElementById('result').remove();
		}

		document.getElementById('resultSentence').innerHTML = `<p>La conformité du test résulte d'une comparaison avec un standard ou de la présence d'une réaction chimique spécifique (virage coloré, turbidité, etc.).</p>
																<p>Veuillez saisir le texte qui fera office de conclusion au test :</p>
																<textarea id='result' placeholder='Texte de conclusion' class="wodth100 height500"></textarea>`;
	} else if (document.getElementById('limited').checked && document.getElementById('calculation').checked) {

		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="unit" id="unit" value="' + document.getElementById('testUnit').value + '">';
		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="passes_test" id="passes_test" value="no">';
		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="limits" id="limits" value="yes">';

		document.getElementById('testNumbers').innerHTML = `<td id="testFormula" class="WStestFormula">
																<table>
																	<thead>
																		<tr>
																			<th colspan = 2>Calcul:</th>
																		</tr>
																	</thead>
																	<tbody id="operandeSumup">
																		<tr>
																			<td id="formulaExpression" colspan = 2 class="paddingSides">
																				<span id="resultName">Resultat = </span>
																				<span id="sign1"></span>
																				<span id="opName1">A</span>
																				<span id="sign2"></span>
																				<span id="opName2">B</span>
																				<span id="sign3"></span>
																			</td>
																		</tr>
																		<tr id="line1">
																			<td id="operandeExpression1">
																				<span id="operandeName1">A</span>
																				<span>=</span>
																				<span id="operandeDescription1"></span>
																			</td>
																			<td id="operandeValue1" class="WSoperandeValue">
																			</td>
																		</tr>
																		<tr id="line2">
																			<td id="operandeExpression2"">
																				<span id="operandeName2">B</span>
																				<span>=</span>
																				<span id="operandeDescription2"></span>
																			</td>
																			<td id="operandeValue2" class="WSoperandeValue">
																			</td>
																		</tr>
																	</tbody>
																</table>
															</td>
															<td id="testLimits" class="WStestLimitswithCalcul">
																<table>
																	<thead>
																		<tr>
																			<th>Bornes :</th>
																		</tr>
																	</thead>
																	<tbody>
																		<tr>
																			<td id="limitsdisplayed">_____ - _____ ${document.getElementById('testUnit').value}</td>
																		</tr>
																	</tbody>
																</table>
															</td>`;
		document.getElementById('testCalcul').style.display = 'table-row';
		document.getElementById('testCalcul').innerHTML = `<td colspan="2">
															<div id="unknownFromTest" class="WSunknownFromTest">
															</div>
															<table id="calcSumup" class="WScalcSumup">
																<thead>
																	<tr>
																		<th></th>
																		<th id="resultCalc" class="WSresultCalc">= (${document.getElementById('testUnit').value})</th>
																	</tr>
																</thead>
																<tbody>
																	<tr id="calcLineA">
																		<td id="echNumberA">Ech</td>
																		<td id="resultA" class="WSresultCalc"></td>
																	</tr>
																</tbody>
															</table>
														</td>`;
		document.getElementById('limitsTest').style.display = 'none';
		document.getElementById('calculTest').style.display = 'block';
	}
}

function addCalcul() {
	document.getElementById('testCalcul').style.display = 'table-row';

	const NUMBER_OF_OPERANDES = parseInt(document.getElementById('number').innerHTML);
	let calculDescription = document.getElementById('resultData').value + ' _ = _ ';
	let operandesWithDollar = '';

	for(let i = 1; i <= NUMBER_OF_OPERANDES; i++) {
		calculDescription += document.getElementById('blank' + i).value + ' _ ' + document.getElementById('op' + i).innerHTML + ' _ ';
		
		let operandeType;
		let formulaire = document.getElementById('type' + i);
		let inputForm = formulaire.getElementsByTagName('input');
		for(let i = 0; i < inputForm.length; i++) {
			if(inputForm[i].checked) {
				operandeType = inputForm[i].value;
			}
		}

		let unknownType;
		let constanteType;

		switch(operandeType) {
		case 'unknown' + i :
			if(document.getElementById('unknownSample' + i).checked) {
				unknownType = 'unknownSample' + i;
			} else if(document.getElementById('unknownTest' + i).checked) {
				unknownType = 'unknownTest' + i;
			}

			operandesWithDollar += document.getElementById('unknownName' + i).value + ' _ ' + document.getElementById('unknownDesc' + i).value + ' _ ' + unknownType + ' $ ';
			break;
		case 'constante' + i :
			if(document.getElementById('editable' + i).checked) {
				constanteType = 'editable' + i;
			} else if(document.getElementById('notEditable' + i).checked) {
				constanteType = 'notEditable' + i;
			}

			operandesWithDollar += document.getElementById('constanteName' + i).value + ' _ ' + document.getElementById('constanteDesc' + i).value + ' _ ' + constanteType + ' _ ' + document.getElementById('constanteValue' + i).value + ' $ ';
			break;
		case 'density' + i :
			operandesWithDollar += 'd(ech) _ Densité de l\'échantillon (en g/mL) _ density' + i + ' $ ';
			break;
		}
	}

	const OPERANDES_WITHOUT_LAST_DOLLAR = operandesWithDollar.substring(0, operandesWithDollar.length - 3);

	calculDescription += document.getElementById('blank' + (NUMBER_OF_OPERANDES + 1)).value;

	document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="calcul_description" id="calcul_description" value="' + calculDescription + '">';
	document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="operandes" id="operandes" value="' + OPERANDES_WITHOUT_LAST_DOLLAR + '">';

	document.getElementById('calculTest').style.display = 'none';
	document.getElementById('resultTest').style.display = 'block';

	if(document.getElementById('result')) {
		document.getElementById('result').remove();
	}
	document.getElementById('resultSentence').innerHTML = `<p>La conformité du test résulte de la comparaison du résultat d'un calcul avec les limites d'acceptation de la spécification. Selon les résultats, la conclusion du test pourra être :</p>
															<p> - Le résultat du test rentre dans la spécification.</p>
															<p> - Le résultat du test ne rentre pas dans la spécification.</p>
															<p> - Le calcul ne peut s'effectuer car des données sont manquantes.</p>`;
}

function addResult() {
	document.getElementById('testConclusion').style.display = 'table-row';

	if(document.getElementById('result')) {
		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="result" id="result" value="' + document.getElementById('result').value + '">';
		document.getElementById('testResult').innerHTML = document.getElementById('result').value;
	} else {
		document.getElementById('testCreationForm').innerHTML += '<input type="hidden" name="result" id="result" value="default">';
		document.getElementById('testResult').innerHTML = 'Le résultat du test rentre dans la spécification.';
	}

	document.getElementById('resultTest').style.display = 'none';
	document.getElementById('confirmation').style.display = 'block';
}

function addOperande() {
	const NUMBER_OF_OPERANDES = parseInt(document.getElementById('number').innerHTML) + 1;
	document.getElementById('number').innerHTML = NUMBER_OF_OPERANDES;
	let operandeLetter;
	switch(NUMBER_OF_OPERANDES) {
	case 3:
		operandeLetter = 'C';
		break;
	case 4:
		operandeLetter = 'D';
		break;
	case 5:
		operandeLetter = 'E';
		break;
	case 6:
		operandeLetter = 'F';
		break;
	case 7:
		operandeLetter = 'G';
		break;
	case 8:
		operandeLetter = 'H';
		break;
	case 9:
		operandeLetter = 'I';
		break;
	case 10:
		operandeLetter = 'J';
		break;
	}

	document.getElementById('oform').innerHTML += `<span id="op${NUMBER_OF_OPERANDES}" class="WSop">${operandeLetter}</span>`;
	document.getElementById('oform').innerHTML += '<input type="text" id="blank' + (NUMBER_OF_OPERANDES + 1) + '" onInput="addSign(NUMBER_OF_OPERANDES + 1)"/>';
	document.getElementById('operandeSelection').innerHTML += `<div id="operande${NUMBER_OF_OPERANDES}" class="WSoperande">
																		<label for="type${NUMBER_OF_OPERANDES}">L'operande ${operandeLetter} est :</label>
																		<div id="type${NUMBER_OF_OPERANDES}">
																			<input class="radiobutton" type="radio" name="type${NUMBER_OF_OPERANDES}" id="unknown${NUMBER_OF_OPERANDES}" value="unknown${NUMBER_OF_OPERANDES}" onchange="addOptionFromOperandeType(${NUMBER_OF_OPERANDES}, 'unknown${NUMBER_OF_OPERANDES}')"/>
																				<label class="radiolabel" for="unknown${NUMBER_OF_OPERANDES}">
																					<div class="newradio"></div>
																					<span class="radiotext">une inconnue</span>
																				</label>
																			<input class="radiobutton" type="radio" name="type${NUMBER_OF_OPERANDES}" id="constante${NUMBER_OF_OPERANDES}" value="constante${NUMBER_OF_OPERANDES}" onchange="addOptionFromOperandeType(${NUMBER_OF_OPERANDES}, 'constante${NUMBER_OF_OPERANDES}')"/>
																				<label class="radiolabel" for="constante${NUMBER_OF_OPERANDES}">
																					<div class="newradio"></div>
																					<span class="radiotext">une constante</span>
																				</label>
																			<input class="radiobutton" type="radio" name="type${NUMBER_OF_OPERANDES}" id="density${NUMBER_OF_OPERANDES}" value="density${NUMBER_OF_OPERANDES}" onchange="addOptionFromOperandeType(${NUMBER_OF_OPERANDES}, 'density${NUMBER_OF_OPERANDES}')"/>
																				<label class="radiolabel" for="density${NUMBER_OF_OPERANDES}">
																					<div class="newradio"></div>
																					<span class="radiotext">une densité</span>
																				</label>
																		</div>
																		<div id="info${NUMBER_OF_OPERANDES}">
																		</div>
																	</div>`;
	document.getElementById('formulaExpression').innerHTML += `<span id="opName${NUMBER_OF_OPERANDES}">${operandeLetter}</span>`;
	document.getElementById('formulaExpression').innerHTML += '<span id="sign'  + (NUMBER_OF_OPERANDES + 1) + '"></span>';
	document.getElementById('operandeSumup').innerHTML += `<tr id="line${NUMBER_OF_OPERANDES}">
																<td id="operandeExpression${NUMBER_OF_OPERANDES}">
																	<span id="operandeName${NUMBER_OF_OPERANDES}">${operandeLetter}</span>
																	<span>=</span>
																	<span id="operandeDescription${NUMBER_OF_OPERANDES}"></span>
																</td>
																<td id="operandeValue${NUMBER_OF_OPERANDES}" class="WSoperandeValue">
																</td>
															</tr>`;
}

function removeOperande() {
	const NUMBER_OF_OPERANDES = parseInt(document.getElementById('number').innerHTML);
	if(NUMBER_OF_OPERANDES > 2) {
		document.getElementById('number').innerHTML = NUMBER_OF_OPERANDES - 1;
		document.getElementById('op' + (NUMBER_OF_OPERANDES)).remove();
		document.getElementById('blank' + (NUMBER_OF_OPERANDES + 1)).remove();
		document.getElementById('operande' + (NUMBER_OF_OPERANDES)).remove();
		document.getElementById('opName' + (NUMBER_OF_OPERANDES)).remove();
		document.getElementById('sign' + (NUMBER_OF_OPERANDES + 1)).remove();
		document.getElementById('line' + (NUMBER_OF_OPERANDES)).remove();
	}
}

//affichage des signes et nom du résultat dans le visuel du test

function addResultName() {
	document.getElementById('resultName').innerHTML = document.getElementById('resultData').value + ' = ';
}

function addSign(i) {
	document.getElementById('sign' + i).innerHTML = ' ' + document.getElementById('blank' + i).value + ' ';
}

//affichage des options selon le type d'opérande (inconnue / constante / densité)
function addOptionFromOperandeType(i, OPERANDE_TYPE) {
	switch(OPERANDE_TYPE) {
	case 'unknown' + i:
		if(document.getElementById('constanteType' + i)) {
			document.getElementById('constanteType' + i).remove();
			document.getElementById('constanteDescription' + i).remove();
		} else if(document.getElementById('unknownType' + i)) {
			document.getElementById('unknownType' + i).remove();
			document.getElementById('unknownDescription' + i).remove();
		}
		document.getElementById('operandeValue' + i).innerHTML = '';
		document.getElementById('info' + i).innerHTML = `<div id="unknownType${i}" class="WSoperandeType">
															Cette inconnue est :
															<div>
																<input class="radiobutton" type="radio" name="unknownType${i}" id="unknownSample${i}" value="unknownSample${i}" onchange="addUnknownToVisualisation(${i})"/>
																	<label class="radiolabel" for="unknownSample${i}">
																		<div class="newradio"></div>
																		<span class="radiotext">propre à chaque échantillon (ex. un volume de titration)</span>
																	</label>
															</div>
															<div>
																<input class="radiobutton" type="radio" name="unknownType${i}" id="unknownTest${i}" value="unknownTest${i}" onchange="addUnknownToVisualisation(${i})"/>
																	<label class="radiolabel" for="unknownTest${i}">
																		<div class="newradio"></div>
																		<span class="radiotext">propre à la réalisation du test (ex. la valeur d'un blanc analytique)</span>
																	</label>
															</div>
														</div>
														<div id="unknownDescription${i}" class="WSoperandeDescription" onInput="addUnknownDesc(${i})">
															<label for="unknownName${i}">Nom :</label>
															<input type="text" id="unknownName${i}" class="WSopeName" onInput="addUnknownName(${i})"/>
															<label for="unknownDesc${i}">Description :</label>
															<input type="text" id="unknownDesc${i}" class="WSopeDesc"/>
														</div>`;
		break;
	case 'constante' + i:
		if(document.getElementById('constanteType' + i)) {
			document.getElementById('constanteType' + i).remove();
			document.getElementById('constanteDescription' + i).remove();
		} else if(document.getElementById('unknownType' + i)) {
			document.getElementById('unknownType' + i).remove();
			document.getElementById('unknownDescription' + i).remove();
		}
		document.getElementById('operandeValue' + i).innerHTML = '';
		document.getElementById('info' + i).innerHTML += `<div id="constanteType${i}" class="WSoperandeType")>
														Cette constante est :
														<div>
															<input class="radiobutton" type="radio" name="constanteType${i}" id="editable${i}" value="editable${i}" onchange="addTextEditable(${i})"/>
																<label class="radiolabel" for="editable${i}">
																	<div class="newradio"></div>
																	<span class="radiotext">potentiellement modifiable (ex. un volume d'échantillon)</span>
																</label>
														</div>
														<div>
															<input class="radiobutton" type="radio" name="constanteType${i}" id="notEditable${i}" value="notEditable${i}" onchange="addTextEditable(${i})"/>
																<label class="radiolabel" for="notEditable${i}">
																	<div class="newradio"></div>
																	<span class="radiotext">non modifiable (ex. une masse molaire)</span>
																</label>
														</div>
													</div>
													<div id="constanteDescription${i}" class="WSoperandeDescription">
														<label for="constanteName${i}">Nom :</label>
														<input type="text" id="constanteName${i}" class="WSopeName" onInput="addConstanteName(${i})"/>
														<label for="constanteDesc${i}">Description :</label>
														<input type="text" id="constanteDesc${i}" class="WSopeDesc" onInput="addConstanteDesc(${i})"/>
														<label for="constanteValue${i}">Valeur :</label>
														<input type="text" id="constanteValue${i}" class="WSopeValue" onInput="addConstanteValue(${i})"/>
													</div>`;
		break;
	case 'density' + i:
		if(document.getElementById('constanteType' + i)) {
			document.getElementById('constanteType' + i).remove();
			document.getElementById('constanteDescription' + i).remove();
		} else if(document.getElementById('unknownType' + i)) {
			document.getElementById('unknownType' + i).remove();
			document.getElementById('unknownDescription' + i).remove();
		}
		document.getElementById('operandeValue' + i).innerHTML = '';
		document.getElementById('op' + i).innerHTML = 'd(ech)';
		document.getElementById('operandeName' + i).innerHTML = 'd(ech)';
		document.getElementById('opName' + i).innerHTML = 'd(ech)';
		document.getElementById('operandeDescription' + i).innerHTML = 'Densité de l\'échantillon (en g/mL)';
		break;
	}
}

//affichage du nom des operandes
function addUnknownName(i) {
	if(document.getElementById('unknownName' + i)) {
		document.getElementById('op' + i).innerHTML = document.getElementById('unknownName' + i).value;
		document.getElementById('operandeName' + i).innerHTML = document.getElementById('unknownName' + i).value;
		document.getElementById('opName' + i).innerHTML = document.getElementById('unknownName' + i).value;
		if(document.getElementById('unknownCalc' + i)) {
			document.getElementById('unknownCalc' + i).innerHTML = document.getElementById('unknownName' + i).value;
		}
		if(document.getElementById('unknownLabel' + i)) {
			document.getElementById('unknownLabel' + i).innerHTML = document.getElementById('unknownName' + i).value + ' = ';
		}
	}
}

function addConstanteName(i) {
	if(document.getElementById('constanteName' + i)) {
		document.getElementById('op' + i).innerHTML = document.getElementById('constanteName' + i).value;
		document.getElementById('operandeName' + i).innerHTML = document.getElementById('constanteName' + i).value;
		document.getElementById('opName' + i).innerHTML = document.getElementById('constanteName' + i).value;
	}
}

//affichage du descriptif des operandes
function addUnknownDesc(i) {
	if(document.getElementById('unknownDesc' + i)) {
		document.getElementById('operandeDescription' + i).innerHTML = document.getElementById('unknownDesc' + i).value;
	}
}

function addConstanteDesc(i) {
	if(document.getElementById('constanteDesc' + i)) {
		document.getElementById('operandeDescription' + i).innerHTML = document.getElementById('constanteDesc' + i).value;
	}
}

//affichage du caractère modifiable ou non des constantes dans le visuel du test
function addTextEditable(i) {
	if(document.getElementById('editable' + i).checked) {
		if(document.getElementById('constanteValue' + i).value) {
			document.getElementById('operandeValue' + i).innerHTML = '<input type="text" name="operandeEdit' + i + '" id="operandeEdit' + i + '" value = "' + document.getElementById('constanteValue' + i).value + '" class="WSoperandeEdit"/>';
		} else {
			document.getElementById('operandeValue' + i).innerHTML = '<input type="text" name="operandeEdit' + i + '" id="operandeEdit' + i + '" class="WSoperandeEdit"/>';
		}
	} else {
		if(document.getElementById('operandeEdit' + i)) {
			if(document.getElementById('constanteValue' + i).value) {
				document.getElementById('operandeValue' + i).innerHTML = document.getElementById('constanteValue' + i).value;
			} else {
				document.getElementById('operandeValue' + i).innerHTML = '';
			}
		}
	}
}
	
//affichage des valeurs de constantes dans le visuel du test
function addConstanteValue(i) {
	if(document.getElementById('constanteValue' + i)) {
		if(document.getElementById('operandeEdit' + i)) {
			document.getElementById('operandeEdit' + i).value = document.getElementById('constanteValue' + i).value;
		} else {
			document.getElementById('operandeValue' + i).innerHTML = document.getElementById('constanteValue' + i).value;
		}
	}
}

//affichage des inconnues dans le tableau de visualisation ou à côté selon leur type (Sample ou Test)
function addUnknownToVisualisation(i) {
	if(document.getElementById('unknownSample' + i).checked) {
		if(document.getElementById('unknownFromTest' + i)) {
			document.getElementById('unknownFromTest' + i).remove();
		}
		if(document.getElementById('unknownCalc' + i)) {
			document.getElementById('unknownCalc' + i).remove();
			document.getElementById('unknown' + i + 'A').remove();
		}
		let columnHeader = document.createElement('th');
		columnHeader.setAttribute('id', 'unknownCalc' + i);
		columnHeader.innerHTML = 'inconnue';
		document.getElementById('resultCalc').before(columnHeader);
		if(document.getElementById('unknownName' + i).value) {
			document.getElementById('unknownCalc' + i).innerHTML = document.getElementById('unknownName' + i).value;
		}
		let columnBody = document.createElement('td');
		columnBody.setAttribute('id', 'unknown' + i + 'A');
		document.getElementById('resultA').before(columnBody);
		document.getElementById('unknown' + i + 'A').innerHTML = '<input type="text" id="unknownValue' + i + 'A" class="WSunknownValue"/>';
	} else if(document.getElementById('unknownTest' + i).checked) {
		if(document.getElementById('unknown' + i + 'A')) {
			document.getElementById('unknownCalc' + i).remove();
			document.getElementById('unknown' + i + 'A').remove();
		}
		document.getElementById('unknownFromTest').innerHTML = '<div id="unknownFromTest' + i + '" class="WSunknownFromTest"><label  for="unknownCalc' + i + '" id="unknownLabel' + i + '">inconnue = </label><input type="text" id="unknownCalc' + i + '" class="WSunknownCalc"/></div>';
		if(document.getElementById('unknownName' + i).value) {
			document.getElementById('unknownLabel' + i).innerHTML = document.getElementById('unknownName' + i).value + ' = ';
		}
	}
}

//fonctions de retour en arrière
function backFromSOP() {
	document.getElementById('testReference').innerHTML = '';
	document.getElementById('testHeader').innerHTML = '';

	document.getElementById('testSOP').innerHTML = '';
	document.getElementById('testText').style.display = 'none';

	document.getElementById('SOPTest').style.display = 'none';
	document.getElementById('headerTest').style.display = 'block';

	document.getElementById('test_ref').remove();
	document.getElementById('test_name').remove();
	document.getElementById('method').remove();
}

function backFromSol() {
	document.getElementById('testText').innerHTML = '';
	document.getElementById('solTest').style.display = 'none';
	document.getElementById('SOPTest').style.display = 'block';

	document.getElementById('SOPhidden').remove();
	if(document.getElementById('numberOfSol')){
		document.getElementById('numberOfSol').remove();
	}
}

function backFromLimits() {
	if(document.getElementById('testLimits')) {
		document.getElementById('testLimits').innerHTML = '';
	}
	document.getElementById('testNumbers').innerHTML = '';
	document.getElementById('limitsTest').style.display = 'none';
	document.getElementById('solTest').style.display = 'block';

	document.getElementById('reagents').remove();
}

function backFromCalcul() {
	document.getElementById('testNumbers').innerHTML = '';
	document.getElementById('testCalcul').innerHTML = '';
	document.getElementById('testCalcul').style.display = 'none';

	document.getElementById('calculTest').style.display = 'none';
	document.getElementById('limitsTest').style.display = 'block';

	document.getElementById('unit').remove();
	document.getElementById('passes_test').remove();
	document.getElementById('limits').remove();
}

function backFromResult() {
	document.getElementById('testResult').innerHTML = '';
	document.getElementById('testResult').style.display = 'none';

	document.getElementById('resultTest').style.display = 'none';

	if(document.getElementById('testFormula').length) {
		document.getElementById('calculTest').style.display = 'block';

		document.getElementById('calcul_description').remove();
		document.getElementById('operandes').remove();
	} else {
		backFromCalcul();
	}
}

function backFromValidation() {
	document.getElementById('testResult').innerHTML = '';
	document.getElementById('testResult').style.display = 'none';
	document.getElementById('confirmation').style.display = 'none';
	document.getElementById('resultTest').style.display = 'block';

	document.getElementById('result').remove();
}