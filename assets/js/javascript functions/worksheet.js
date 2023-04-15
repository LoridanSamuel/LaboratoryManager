/* eslint-disable no-unused-vars */
/* eslint-env jquery */
const ALPHA_LOWERCASE = Array.from(Array(26)).map((e,i)=>i + 97);
const ALPHA_UPPERCASE = Array.from(Array(26)).map((e,i)=>i + 65);
const ALPHABET_LOWERCASE = ALPHA_LOWERCASE.map((x)=>String.fromCharCode(x));
const ALPHABET_UPPERCASE = ALPHA_UPPERCASE.map((x)=>String.fromCharCode(x));

function goToHeader() {
	document.getElementById('visibleTest').style.display = 'none';
	document.getElementById('worksheetHeader').style.display=  'block';
}

function backFromHeader() {
	document.getElementById('visibleTest').style.display = 'block';
	document.getElementById('worksheetHeader').style.display=  'none';
}

function calculation(typeOfCalculation, oOperande) {
	const OPERANDE = oOperande.id;
	const DENSITY_WITH_TEXT_AND_UNIT = document.getElementById('density').innerHTML;
	const DENSITY = (DENSITY_WITH_TEXT_AND_UNIT.replace('Densité : ', '')).replace(' g/mL', '');
	let operandeSplittedAtType = '';

	if(OPERANDE.includes('unknownSample')) {
		operandeSplittedAtType = OPERANDE.split('unknownSample');
	}  else if(OPERANDE.includes('unknownTest')) {
		operandeSplittedAtType = OPERANDE.split('unknownTest');
	} else if(OPERANDE.includes('editable')) {
		operandeSplittedAtType = OPERANDE.split('editable');
	}

	const TEST_REFERENCE = operandeSplittedAtType[0];
	const NUMBER_OF_SAMPLE = document.getElementById(TEST_REFERENCE + 'numberOfSample').value;
	const LIMITS = document.getElementById(TEST_REFERENCE + 'limits').innerHTML;
	const LIMITS_SPLITTED = LIMITS.split(' - ');
	const LOWER_LIMIT = parseFloat(LIMITS_SPLITTED[0]);
	const UPPER_LIMIT = parseFloat(LIMITS_SPLITTED[1]);
	let arrondi = '';

	if((UPPER_LIMIT.toString()).includes('.')) {
		let upperDecimal = (((UPPER_LIMIT.toString()).split('.'))[1].split('')).length;
		let lowerDecimal = (LOWER_LIMIT.toString()).includes('.') ? (((LOWER_LIMIT.toString()).split('.'))[1].split('')).length : 0;

		arrondi = upperDecimal >= lowerDecimal ? upperDecimal + 1 : lowerDecimal + 1;

	} else {
		arrondi = 1;
	}

	let resultSentence = '';

	switch (typeOfCalculation) {
	case 'Basic':
		for(let i = 0; i <= NUMBER_OF_SAMPLE - 1; i++) {
			const CALCUL = ((document.getElementById(TEST_REFERENCE + 'hiddenCalcul').value).split('x').join('*')).split('E*tr').join('Extr');
			const CALCUL_SPLITTED = CALCUL.split(TEST_REFERENCE);
			const SAMPLE_NUMBER = ALPHABET_UPPERCASE[i];
			const CALCUL_REPLACED = operandeReplacement(CALCUL_SPLITTED, CALCUL, SAMPLE_NUMBER, TEST_REFERENCE, DENSITY);
			checkingResult(typeOfCalculation, CALCUL_REPLACED, SAMPLE_NUMBER, TEST_REFERENCE, arrondi, UPPER_LIMIT, LOWER_LIMIT);
			if(document.getElementById(TEST_REFERENCE + 'result' + SAMPLE_NUMBER).classList.contains('cellnotOK')) {
				resultSentence = 'notOk';
			} else if (document.getElementById(TEST_REFERENCE + 'result' + SAMPLE_NUMBER).classList.contains('cellOK') && resultSentence != 'notOk') {
				resultSentence = 'Ok';
			}
		}
		break;
	case 'Assay_EP':
		for(let i = 0; i <= NUMBER_OF_SAMPLE -1; i++) {
			const CALCUL = ((document.getElementById(TEST_REFERENCE + 'hiddenCalcul_EP').value).split('x').join('*')).split('E*tr').join('Extr');
			const CALCUL_SPLITTED = CALCUL.split(TEST_REFERENCE);
			const SAMPLE_NUMBER = ALPHABET_UPPERCASE[i];
			const CALCUL_REPLACED = operandeReplacement(CALCUL_SPLITTED, CALCUL, SAMPLE_NUMBER, TEST_REFERENCE, DENSITY);
			checkingResult(typeOfCalculation, CALCUL_REPLACED, SAMPLE_NUMBER, TEST_REFERENCE, arrondi, UPPER_LIMIT, LOWER_LIMIT);
			if(document.getElementById(TEST_REFERENCE + 'result' + SAMPLE_NUMBER).classList.contains('cellnotOK')) {
				resultSentence = 'notOk';
			} else if (document.getElementById(TEST_REFERENCE + 'result' + SAMPLE_NUMBER).classList.contains('cellOK') && resultSentence != 'notOk') {
				resultSentence = 'Ok';
			}
		}
		break;
	case 'Assay_USP':
		for(let i = 0; i <= NUMBER_OF_SAMPLE -1; i++) {
			const CALCUL = ((document.getElementById(TEST_REFERENCE + 'hiddenCalcul_USP').value).split('x').join('*')).split('E*tr').join('Extr');
			const CALCUL_SPLITTED = CALCUL.split(TEST_REFERENCE);
			const SAMPLE_NUMBER = ALPHABET_LOWERCASE[i];
			const CALCUL_REPLACED = operandeReplacement(CALCUL_SPLITTED, CALCUL, SAMPLE_NUMBER, TEST_REFERENCE, DENSITY);
			checkingResult(typeOfCalculation, CALCUL_REPLACED, SAMPLE_NUMBER, TEST_REFERENCE, arrondi, UPPER_LIMIT, LOWER_LIMIT);
			if(document.getElementById(TEST_REFERENCE + 'result' + SAMPLE_NUMBER).classList.contains('cellnotOK')) {
				resultSentence = 'notOk';
			} else if (document.getElementById(TEST_REFERENCE + 'result' + SAMPLE_NUMBER).classList.contains('cellOK') && resultSentence != 'notOk') {
				resultSentence = 'Ok';
			}
		}
		break;
	case 'Assay_all':
		for(let i = 0; i <= NUMBER_OF_SAMPLE -1; i++) {
			const SAMPLE_NUMBER_EP = ALPHABET_UPPERCASE[i];
			const SAMPLE_NUMBER_USP = ALPHABET_LOWERCASE[i];
			const CALCUL_EP = ((document.getElementById(TEST_REFERENCE + 'hiddenCalcul_EP').value).split('x').join('*')).split('E*tr').join('Extr');
			const CALCUL_USP = ((document.getElementById(TEST_REFERENCE + 'hiddenCalcul_USP').value).split('x').join('*')).split('E*tr').join('Extr');
			const CALCUL_SPLITTED_EP = CALCUL_EP.split(TEST_REFERENCE);
			const CALCUL_SPLITTED_USP = CALCUL_USP.split(TEST_REFERENCE);
			const CALCUL_REPLACED_EP = operandeReplacement(CALCUL_SPLITTED_EP, CALCUL_EP, SAMPLE_NUMBER_EP, TEST_REFERENCE, DENSITY);
			const CALCUL_REPLACED_USP = operandeReplacement(CALCUL_SPLITTED_USP, CALCUL_USP, SAMPLE_NUMBER_USP, TEST_REFERENCE, DENSITY);
			checkingResult_all(CALCUL_REPLACED_EP, CALCUL_REPLACED_USP, SAMPLE_NUMBER_EP, SAMPLE_NUMBER_USP, TEST_REFERENCE, arrondi, UPPER_LIMIT, LOWER_LIMIT);
			if(document.getElementById(TEST_REFERENCE + 'result' + SAMPLE_NUMBER_EP).classList.contains('cellnotOK') || document.getElementById(TEST_REFERENCE + 'result' + SAMPLE_NUMBER_USP).classList.contains('cellnotOK')) {
				resultSentence = 'notOk';
			} else if(resultSentence != 'notOk') {
				if(document.getElementById(TEST_REFERENCE + 'result' + SAMPLE_NUMBER_EP).classList.contains('cellOK') || document.getElementById(TEST_REFERENCE + 'result' + SAMPLE_NUMBER_USP).classList.contains('cellOK')) {
					resultSentence = 'Ok';
				}
			}
		}
		break;
	}

	if(resultSentence == '') {
		document.getElementById(TEST_REFERENCE + 'resultSentence').innerHTML = 'Le calcul ne peut s\'effectuer car des données sont manquantes.';
		document.getElementById(TEST_REFERENCE + 'resultSentence').classList.remove('ResultOK').classList.add('ResultnotOK');
	} else if (resultSentence == 'notOk') {
		document.getElementById(TEST_REFERENCE + 'resultSentence').innerHTML = 'Le résultat du test ne rentre pas dans la spécification.';
		document.getElementById(TEST_REFERENCE + 'resultSentence').classList.remove('ResultOK').classList.add('ResultnotOK');
	} else if (resultSentence == 'Ok') {
		document.getElementById(TEST_REFERENCE + 'resultSentence').innerHTML = 'Le résultat du test rentre dans la spécification.';
		document.getElementById(TEST_REFERENCE + 'resultSentence').classList.remove('ResultnotOK').classList.add('ResultOK');
	}
}

function operandeReplacement(calculSplit, calcul, sampleNumber, TEST_REFERENCE, DENSITY) {
	for(let i = 1; i < calculSplit.length; i++) {
		if(calculSplit[i].includes('unknownSample1')) {
			if($('#' + TEST_REFERENCE + 'unknownSample1' + sampleNumber).val() != '') {
				calcul = calcul.replace(TEST_REFERENCE + 'unknownSample1', Number($('#' + TEST_REFERENCE + 'unknownSample1' + sampleNumber).val()));
			} else {
				calcul = calcul.replace(TEST_REFERENCE + 'unknownSample1', 'error');
			}
		} else if(calculSplit[i].includes('unknownSample3')) {
			if($('#' + TEST_REFERENCE + 'unknownSample3' + sampleNumber).val() != '') {
				calcul = calcul.replace(TEST_REFERENCE + 'unknownSample3', Number($('#' + TEST_REFERENCE + 'unknownSample3' + sampleNumber).val()));
			} else {
				calcul = calcul.replace(TEST_REFERENCE + 'unknownSample3', 'error');
			}
		}else if(calculSplit[i].includes('unknownSample')) {
			if($('#' + TEST_REFERENCE + 'unknownSample' + i + sampleNumber).val() != '') {
				calcul = calcul.replace(TEST_REFERENCE + 'unknownSample' + i, Number($('#' + TEST_REFERENCE + 'unknownSample' + i + sampleNumber).val()));
			} else {
				calcul = calcul.replace(TEST_REFERENCE + 'unknownSample' + i, 'error');
			}
		} else if(calculSplit[i].includes('unknownTest')) {
			if($('#' + TEST_REFERENCE + 'unknownTest' + i).val() != '') {
				calcul = calcul.replace(TEST_REFERENCE + 'unknownTest' + i, Number($('#' + TEST_REFERENCE + 'unknownTest' + i).val()));
			} else {
				calcul = calcul.replace(TEST_REFERENCE + 'unknownTest' + i, 'error');
			}
		} else if(calculSplit[i].includes('editable')) {
			if($('#' + TEST_REFERENCE + 'editable' + i).val() != '') {
				calcul = calcul.replace(TEST_REFERENCE + 'editable' + i, Number($('#' + TEST_REFERENCE + 'editable' + i).val()));
			} else {
				calcul = calcul.replace(TEST_REFERENCE + 'editable' + i, 'error');
			}
		} else if(calculSplit[i].includes('notEditable')) {
			calcul = calcul.replace(TEST_REFERENCE + 'notEditable' + i, Number($('#' + TEST_REFERENCE + 'notEditable' + i).html()));
		} else if(calculSplit[i].includes('density')) {
			calcul = calcul.replace(TEST_REFERENCE + 'density' + i, Number(DENSITY));
		}
	}
	return calcul;
}

function checkingResult(typeOfCalculation, CALCUL, SAMPLE_NUMBER, TEST_REFERENCE, arrondi, UPPER_LIMIT, LOWER_LIMIT) {
	const RESULT_CELL = $('#' + TEST_REFERENCE + 'result' + SAMPLE_NUMBER);
	const RESULT_SENTENCE = $('#' + TEST_REFERENCE + 'resultSentence');
	if(CALCUL.includes('error') || eval(CALCUL).toFixed(arrondi) ==='NaN') {
		RESULT_CELL.html('');
		RESULT_CELL.removeClass('cellnotOK cellOK');
	} else {
		switch (typeOfCalculation) {
		case 'Basic':
			var resultString = eval(CALCUL).toFixed(arrondi);
			RESULT_CELL.html(resultString);
			if((eval(CALCUL).toFixed(Number(arrondi -1))>= Number(LOWER_LIMIT)) && (eval(CALCUL).toFixed(Number(arrondi -1))<= Number(UPPER_LIMIT))) {
				RESULT_CELL.removeClass('cellnotOK').addClass('cellOK');
			} else {
				RESULT_CELL.removeClass('cellOK').addClass('cellnotOK');
			}
			break;
		case 'Assay_EP':
			var densityCalculatedEP = ((eval(CALCUL))*1000);
			if(densityCalculatedEP >= 789.24 && densityCalculatedEP <= 998.2) {
				$.ajax({
					url: 'getListData.php',
					method: 'GET',
					datatype: 'application/json',
					data: 'density_calculated_EP=' + densityCalculatedEP,
				})

					.done(function(response) {
						const RESULT = resultDetermination(response, arrondi);
						RESULT_CELL.html(RESULT);

						if(Number(RESULT).toFixed(Number(arrondi -1)) >= Number(LOWER_LIMIT) && Number(RESULT).toFixed(Number(arrondi -1)) <= Number(UPPER_LIMIT)) {
							RESULT_CELL.removeClass('cellnotOK').addClass('cellOK');
							if(RESULT_SENTENCE.html() === 'Le calcul ne peut s\'effectuer car des données sont manquantes.') {
								RESULT_SENTENCE.html('Le résultat du test rentre dans la spécification.');
								RESULT_SENTENCE.removeClass('ResultnotOK').add('ResultOK');
							}
						} else {
							RESULT_CELL.removeClass('cellOK').addClass('cellnotOK');
							RESULT_SENTENCE.html('Le résultat du test ne rentre pas dans la spécification.');
							RESULT_SENTENCE.removeClass('ResultOK').add('ResultnotOK');
						}
					})

					.fail(function(error) {
						alert('la requête s\'est terminée par une erreur. Infos : ' + JSON.stringify(error));
					});
			} else {
				RESULT_CELL.html('valeur impossible');
				RESULT_CELL.removeClass('cellOK').addClass('cellnotOK');
			}
			break;
		case 'Assay_USP':
			var densityCalculatedUSP = eval(CALCUL);
			if(densityCalculatedUSP >= 0.7936 && densityCalculatedUSP <= 1) {
				$.ajax({
					url: 'getListData.php',
					method: 'GET',
					datatype: 'application/json',
					data: 'density_calculated_USP=' + densityCalculatedUSP,
				})

					.done(function(response){
						const RESULT = resultDetermination(response, arrondi);
						RESULT_CELL.html(RESULT);
						if(Number(RESULT).toFixed(Number(arrondi-1)) >= Number(LOWER_LIMIT) && Number(RESULT).toFixed(Number(arrondi-1)) <= Number(UPPER_LIMIT)) {
							RESULT_CELL.removeClass('cellnotOK').addClass('cellOK');

							if(RESULT_SENTENCE.html() == 'Le calcul ne peut s\'effectuer car des données sont manquantes.') {
								RESULT_SENTENCE.html('Le résultat du test ne rentre pas dans la spécification.');
								RESULT_SENTENCE.removeClass('ResultnotOK').addClass('ResultOK');
							}
						} else {
							RESULT_CELL.removeClass('cellOK').addClass('cellnotOK');
							RESULT_SENTENCE.html('Le résultat du test ne rentre pas dans la spécification.');
							RESULT_SENTENCE.removeClass('ResultOK').addClass('ResultnotOK');
						}
					})

					.fail(function(error){
						alert('La requête s\'est terminée par une erreur. Infos : ' + JSON.stringify(error));
					});
			} else {
				RESULT_CELL.html('valeur impossible');
				RESULT_CELL.removeClass('cellOK').addClass('cellnotOK');
			}
			break;
		}
	}
}

function checkingResult_all(CALCUL_EP, CALCUL_USP, SAMPLE_NUMBER_EP, SAMPLE_NUMBER_USP, TEST_REFERENCE, arrondi, UPPER_LIMIT, LOWER_LIMIT) {
	const RESULT_CELL_EP = $('#' + TEST_REFERENCE + 'result' + SAMPLE_NUMBER_EP);
	const RESULT_CELL_USP = $('#' + TEST_REFERENCE + 'result' + SAMPLE_NUMBER_USP);
	const RESULT_SENTENCE = $('#' + TEST_REFERENCE + 'resultSentence');

	console.log(CALCUL_EP);
	console.log(CALCUL_USP);

	if(CALCUL_EP.includes('error')) {
		RESULT_CELL_EP.html('');
		RESULT_CELL_EP.removeClass('cellnotOK cellOK');
	} else {
		const DENSITY_CALCULATED_EP = (eval(CALCUL_EP)*1000);
		if(DENSITY_CALCULATED_EP >= 789.24 && DENSITY_CALCULATED_EP <= 998.2) {
			$.ajax({
				url: 'getListData.php',
				method: 'GET',
				datatype: 'application/json',
				data: 'density_calculated_EP=' + DENSITY_CALCULATED_EP,
			})

				.done(function(response){
					const RESULT_EP = resultDetermination(response, arrondi);
					RESULT_CELL_EP.html(RESULT_EP);

					if(Number(RESULT_EP).toFixed(Number(arrondi)) >= Number(LOWER_LIMIT) && Number(RESULT_EP).toFixed(Number(arrondi)) <= Number(UPPER_LIMIT)) {
						RESULT_CELL_EP.removeClass('cellnotOK').addClass('cellOK');

						if(RESULT_SENTENCE.html() == 'Le calcul ne peut s\'effectuer car des données sont manquantes.') {
							RESULT_SENTENCE.html('Le résultat du test ne rentre pas dans la spécification.');
							RESULT_SENTENCE.removeClass('ResultnotOK').addClass('ResultOK');
						}

					} else {
						RESULT_CELL_EP.removeClass('cellOK').addClass('cellnotOK');
						RESULT_SENTENCE.html('Le résultat du test ne rentre pas dans la spécification.');
						RESULT_SENTENCE.removeClass('ResultOK').addClass('ResultnotOK');
					}
				})

				.fail(function(error){
					alert('La requête s\'est terminée par une erreur. Infos : ' + JSON.stringify(error));
				});
		} else if (DENSITY_CALCULATED_EP < 789.24) {
			RESULT_CELL_EP.removeClass('cellnotOK').addClass('cellOK');
			RESULT_CELL_EP.html('>100.0');

			if(RESULT_SENTENCE.html() == 'Le calcul ne peut s\'effectuer car des données sont manquantes.') {
				RESULT_SENTENCE.html('Le résultat du test ne rentre pas dans la spécification.');
				RESULT_SENTENCE.removeClass('ResultnotOK').addClass('ResultOK');
			}
		} else {  
			RESULT_CELL_EP.removeClass('cellOK').addClass('cellnotOK');
			RESULT_CELL_EP.html('valeur impossible');
		}
	}
	
	if(CALCUL_USP && CALCUL_USP.includes('error')) {
		RESULT_CELL_USP.html('');
		RESULT_CELL_USP.removeClass('cellnotOK cellOK');
	} else {
		const DENSITY_CALCULATED_USP = (eval(CALCUL_USP));
		if(DENSITY_CALCULATED_USP >= 0.7936 && DENSITY_CALCULATED_USP <= 1) {
			$.ajax({
				url: 'getListData.php',
				method: 'GET',
				datatype: 'application/json',
				data: 'density_calculated_USP=' + DENSITY_CALCULATED_USP,
			})

				.done(function(response){
					const RESULT_USP = resultDetermination(response, arrondi);
					RESULT_CELL_USP.html(RESULT_USP);

					if(Number(RESULT_USP).toFixed(Number(arrondi)) >= Number(LOWER_LIMIT) && Number(RESULT_USP).toFixed(Number(arrondi)) <= Number(UPPER_LIMIT)) {
						RESULT_CELL_USP.removeClass('cellnotOK').addClass('cellOK');

						if(RESULT_SENTENCE.html() == 'Le calcul ne peut s\'effectuer car des données sont manquantes.') {
							RESULT_SENTENCE.html('Le résultat du test ne rentre pas dans la spécification.');
							RESULT_SENTENCE.removeClass('ResultnotOK').addClass('ResultOK');
						}

					} else {
						RESULT_CELL_USP.removeClass('cellOK').addClass('cellnotOK');
						RESULT_SENTENCE.html('Le résultat du test ne rentre pas dans la spécification.');
						RESULT_SENTENCE.removeClass('ResultOK').addClass('ResultnotOK');
					}
				})
				.fail(function(error){
					alert('La requête s\'est terminée par une erreur. Infos : ' + JSON.stringify(error));
				});
		} else if (DENSITY_CALCULATED_USP < 0.7936) {
			RESULT_CELL_USP.removeClass('cellnotOK').addClass('cellOK');
			RESULT_CELL_USP.html('>100.0');

			if(RESULT_SENTENCE.html() == 'Le calcul ne peut s\'effectuer car des données sont manquantes.') {
				RESULT_SENTENCE.html('Le résultat du test ne rentre pas dans la spécification.');
				RESULT_SENTENCE.removeClass('ResultnotOK').addClass('ResultOK');
			}
		} else {
			RESULT_CELL_USP.removeClass('cellOK').addClass('cellnotOK');
			RESULT_CELL_USP.html('valeur impossible');
		}
	}
}

function resultDetermination (response, arrondi) {
	const LD = response[0];
	const UP = response[1];
	const UD = response[2];
	const LP = response[3];
	const DENSITY_CALCULATED = response[4];
	const DELTA_P = UP - LP;
	const DELTA_D = UD - LD;
	const DELTA_VALUE = UD - DENSITY_CALCULATED;
	return eval(((DELTA_P) * (DELTA_VALUE) / (DELTA_D)) + Number(LP)).toFixed(arrondi);
}

function closeModal() {
	$('#oModal').hide();
}

function printPDF() {

	let lotNumber = $('#lotNumber').html().substring(16);
	let matName = $('#matName').html().substring(29);
	let QAName = $('#QA_name').html();
	let versionNumber = $('#version_number').html().substring(8);

	console.log(lotNumber);
	console.log(matName);
	console.log(QAName);
	console.log(versionNumber);

	var textArea = document.createElement('textarea');
	textArea.class = 'printPDF';
	textArea.value = '\\\\Srv-dc1-p-bio\\data\\QC\\labo QC\\Data\\' + lotNumber + '\\' + lotNumber + ' - ' + matName + ' - ' + QAName + ' v' + versionNumber + '_Feuille de Calcul QC.pdf';
	document.body.appendChild(textArea);
	textArea.focus();
	textArea.select();
	try {
		var successful = document.execCommand('copy');
		var msg = successful ? 'successful' : 'unsuccessful';
		console.log('Copying text command was ' + msg);
	} catch (err) {
		console.log('Oops, unable to copy');
	}
	document.body.removeChild(textArea);

	window.print();
}