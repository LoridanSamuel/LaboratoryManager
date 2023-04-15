/* eslint-disable no-unused-vars */
function Ajouter(){
	const TR = document.createElement('tr');
	const TEST = getSelectText('testSelect');
	let lowerlimit = '';
	let upperlimit = '';
	let limit = '';
	let critical = '';
	let crit = '';
	let visible = '';
	let vis = '';
	if(document.getElementById('lower_limit')) {
		lowerlimit = document.getElementById('lower_limit').value;
		upperlimit = document.getElementById('upper_limit').value;
		limit = lowerlimit + ' - ' + upperlimit + ' ' + document.getElementById('units').value;
	} else {
		lowerlimit = 'PT';
		upperlimit = 'PT';
		limit = 'Passes Test';
	}
	if(document.getElementById('critical').checked){
		critical = '✔';
		crit = 'yes';
	} else {
		critical = '✘';
		crit = 'no';
	}
	if(document.getElementById('visibility').checked) {
		visible = '✔';
		vis = 'yes';
	} else {
		visible = '✘';
		vis = 'no';
	}
	const TD_TEST = document.createElement('td');
	const TXT_TEST = document.createTextNode(TEST);
	TD_TEST.appendChild(TXT_TEST);
	TR.appendChild(TD_TEST);

	const TD_LIMIT = document.createElement('td');
	const TXT_LIMIT = document.createTextNode(limit);
	TD_LIMIT.appendChild(TXT_LIMIT);
	TR.appendChild(TD_LIMIT);

	const TD_CRITICAL = document.createElement('td');
	const TXT_CRITICAL = document.createTextNode(critical);
	TD_CRITICAL.appendChild(TXT_CRITICAL);
	TR.appendChild(TD_CRITICAL);

	const TD_VISIBLE = document.createElement('td');
	const TXT_VISIBLE = document.createTextNode(visible);
	TD_VISIBLE.appendChild(TXT_VISIBLE);
	TR.appendChild(TD_VISIBLE);

	document.getElementById('test_sumup').appendChild(TR);

	document.getElementById('testsubmit').innerHTML += '<input type="hidden" name="testhtml[]" value="' + TEST + ' _ ' + limit + ' _ ' + critical + ' _ ' + visible + '">';

	let units;
	if(document.getElementById('units')) {
		units = document.getElementById('units').value;
	}
	const TEST_SUBMIT = TEST + ' _ ' + lowerlimit + ' _ ' + upperlimit + ' _ ' + units + ' _ ' + crit + ' _ ' + vis;

	if(document.getElementById('numberOfTests')) {
		var i = parseInt(document.getElementById('numberOfTests').value) + 1;
		document.getElementById('numberOfTests').value = i;
		document.getElementById('testsubmit').innerHTML += '<input type="hidden" name="tests[]" id ="test' + i + '" value="' + TEST_SUBMIT + '">';
	} else {
		document.getElementById('testsubmit').innerHTML += '<input type="hidden" name="numberOfTests" id ="numberOfTests" value="1"><input type="hidden" name="tests[]" id ="test1" value="' + TEST_SUBMIT + '">';
	}

	document.getElementById('testSelect').value = 'none';
	if(document.getElementById('lower_limit')) {
		document.getElementById('lower_limit').remove();
		document.getElementById('upper_limit').remove();
		document.getElementById('units').remove();
	}
	document.getElementById('limits').style.display = 'none';
	document.getElementById('critical').checked = false;
	document.getElementById('visibility').checked = false;
}

function Supprimer(){
	const LAST_TEST = document.getElementById('test_sumup').getElementsByTagName('tr').length;
	document.getElementById('test_sumup').deleteRow(LAST_TEST - 1);
	if(document.getElementById('numberOfTests').value === 1) {
		document.getElementById('numberOfTests').remove();
		document.getElementById('test1').remove();
	} else {
		document.getElementById('test' + document.getElementById('numberOfTests').value).remove();
		document.getElementById('numberOfTests').value += - 1;
	}
}

async function request(oSelect) {
	const INPUT_VALUE = oSelect.options[oSelect.selectedIndex].value;

	if(document.getElementById('lower_limit')) {
		document.getElementById('lower_limit').value = '';
	}
	if(document.getElementById('upper_limit')) {
		document.getElementById('upper_limit').value = '';
	}
	document.getElementById('limits').style.display = 'none';
	document.getElementById('critical').checked = false;
	document.getElementById('visibility').checked = false;

	let response = await fetch('getListData.php?test_id=' + INPUT_VALUE, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json; charset=utf-8'
		},
	});

	if(response.ok) {
		const DATA = await response.json();
		const O_LIMITS = document.getElementById('limits');
		let oUnit = DATA[0].unit;

		if(oUnit === null) {
			oUnit = '';
		}

		if(DATA[0].limits == 'no') {
			document.getElementById('limits').style.display = 'none';
			O_LIMITS.innerHTML = '';
		} else {
			document.getElementById('limits').style.display = 'block';
			O_LIMITS.innerHTML = `<label for="limitspresentation">Bornes : </label>
														<span id="limitspresentation">
															<input type="text" name="lower_limit" id="lower_limit" size="5"/>
															<span> - </span>
															<input type="text" name="upper_limit" id="upper_limit" size="5"/>
															<span> ${oUnit} </span>
															<input type="hidden" id="units" value="${oUnit}">
														</span>`;
		}
	} else {
		alert('HTTP-Error: ' + response.status);
	}
}

function getSelectText(selectId) {
	var selectElmt = document.getElementById(selectId);
	return selectElmt.options[selectElmt.selectedIndex].textContent;
}