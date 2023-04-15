// eslint-disable-next-line no-unused-vars
async function selectAType(TYPE_OF_MATERIAL) {
	let response = await fetch('getListData.php?typeOfMaterial_SOP=' + TYPE_OF_MATERIAL, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json; charset=utf-8'
		},
	});
	if(response.ok) {
		const DATA = await response.json();
		const MATERIAL_NAME = DATA[0];
		const CONCENTRATION = DATA[1];
		const SOLVENT = DATA[2];
		const PACKAGING = DATA[3];
		const LIFETIME = DATA[4];
			
		let oOption, oInner;

		let listArray = ['nameList', 'concentrationList', 'solventList', 'packagingList', 'lifetimeList'];

		listArray.forEach(function(listName) {
			let arrayName = '';

			switch (listName) {
			case 'nameList' :
				arrayName = MATERIAL_NAME;
				break;
			case 'concentrationList' :
				arrayName = CONCENTRATION;
				break;
			case 'solventList' :
				arrayName = SOLVENT;
				break;
			case 'packagingList' :
				arrayName = PACKAGING;
				break;
			case 'lifetimeList' :
				arrayName = LIFETIME;
				break;
			}

			document.getElementById(listName).innerHTML = '<option value="none" disabled selected>Selection</option>';
			for(let i = 0; i < arrayName.length; i++) {
				oOption = document.createElement('option');

				let oOptionValue = '';

				switch (arrayName) {
				case MATERIAL_NAME :
					oOptionValue = MATERIAL_NAME[i].name;
					break;
				case CONCENTRATION :
					oOptionValue = CONCENTRATION[i].concentration;
					break;
				case SOLVENT :
					oOptionValue = SOLVENT[i].solvent;
					break;
				case PACKAGING :
					oOptionValue = PACKAGING[i].packaging;
					break;
				case LIFETIME :
					oOptionValue = LIFETIME[i].lifetime;
					break;
				}

				oInner = document.createTextNode(oOptionValue);
				oOption.value = oOptionValue;
				oOption.appendChild(oInner);
				document.getElementById(listName).appendChild(oOption);
			}	
		});
	} else {
		alert('HTTP-Error: ' + response.status);
	}
}