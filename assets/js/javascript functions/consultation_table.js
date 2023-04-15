const compare = (ids, asc) => (row1, row2) => {
	const tdValue = (row, ids) => row.children[ids].textContent;
	const tri = (v1, v2) => v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2);
	return tri(tdValue(asc ? row1 : row2, ids), tdValue(asc ? row2 : row1, ids));
};

const tbody = document.querySelector('tbody');
const thx = document.querySelectorAll('th');
const trxb = tbody.querySelectorAll('tr');
thx.forEach(th => th.addEventListener('click', () => {
	let classe = Array.from(trxb).sort(compare(Array.from(thx).indexOf(th), this.asc = !this.asc));
	classe.forEach(tr => tbody.appendChild(tr));
}));

let props = {
	btn: false,
	btn_text: 'Bouton Filtrer',
	enter_key: true,
	display_all_text: ' ',
	on_change: true,
	display_nrows: true,
	nrows_text: 'Lignes affichées : ',
	btn_reset: true,
	btn_reset_text: 'Reinitialiser',
	sort_select: true,
	paging: false,
	paging_length: 20,
	loader: true,
	loader_text: 'Chargement...',
	exact_match: false,
	col_0: 'select',
	col_1: 'select',
	col_2: 'select',
	col_3: 'select',
	col_4: 'select',
	col_5: 'select',
	col_6: 'select',
	col_7: 'select',
	col_8: 'select',
	col_9: 'select',
	col_10: 'select',
	col_11: 'select',
	col_12: 'select',
	col_13: 'select',
	col_14: 'select',
	col_15: 'select'   
};
// eslint-disable-next-line no-undef
setFilterGrid('tableconsult',0,props);

document.addEventListener('DOMContentLoaded', function() {
	document.querySelector('select option[value="OK"]').selected = true;
	document.getElementById('flt2_tableconsult').onchange();
});