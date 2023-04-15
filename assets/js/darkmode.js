// eslint-disable-next-line no-unused-vars
function toggleDarkMode() {
	if(document.getElementById('dark--Toggle').checked) {
		document.documentElement.classList.remove('light');
		document.documentElement.classList.add('dark');
		localStorage.setItem('prefersColorScheme','dark');
	} else {
		document.documentElement.classList.remove('dark');
		document.documentElement.classList.add('light');
		localStorage.setItem('prefersColorScheme','light');
	}
}

window.addEventListener('load', function() {
	if (sessionStorage.prefersColorScheme == 'dark' ) {
		document.documentElement.classList.add('dark');
		document.documentElement.classList.remove('light');
		this.document.querySelector('#dark--Toggle').checked = true;
	}
	if (sessionStorage.prefersColorScheme == 'light' ) {
		document.documentElement.classList.add('light');
		document.documentElement.classList.remove('dark');
		this.document.querySelector('#dark--Toggle').checked = false;
	}

	if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
		document.documentElement.classList.add('dark');
		document.documentElement.classList.remove('light');
		this.document.querySelector('#dark--Toggle').checked = true;
	} else {
		document.documentElement.classList.add('light');
		document.documentElement.classList.remove('dark');
		this.document.querySelector('#dark--Toggle').checked = false;
	}

	if(localStorage.getItem('prefersColorScheme')=='light') {
		document.documentElement.classList.add('light');
		document.documentElement.classList.remove('dark');
		this.document.querySelector('#dark--Toggle').checked = false;
	} else if(localStorage.getItem('prefersColorScheme')=='dark') {
		document.documentElement.classList.add('dark');
		document.documentElement.classList.remove('light');
		this.document.querySelector('#dark--Toggle').checked = true;
	}
});