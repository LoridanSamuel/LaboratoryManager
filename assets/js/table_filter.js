
/*====================================================
	- HTML Table Filter Generator
	- développé par Max Guglielmi
	- http://mguglielmi.free.fr/scripts/TableFilter/?l=fr
	- Prière de conserver ce message
	
	- Modified By jpconnexion oct 2006: 
		* fonctonnement similaire aux filtres sous excel
		* lors d'un filtrage les listes déroulantes select sont mises à jour en fonction du résultat du filtre
		
=====================================================*/

var TblId, StartRow, SearchFlt, ModFn, ModFnId;
var ExactMatchId, ExactMatch;
TblId = new Array(), StartRow = new Array();
ModFn = new Array(), ModFnId = new Array();
ExactMatchId = new Array(), ExactMatch = new Array();
SlcArgs = new Array();
memSelect = new Array();
comptage=0;

function setFilterGrid(id)
/*====================================================
	- Checks if id exists and is a table
	- Then looks for additional params 
	- Calls fn that adds inputs and button
=====================================================*/
{
	var tbl = document.getElementById(id);
	var ref_row, fObj;
	memSelect[id] = new Array();
	if(tbl != null && tbl.nodeName.toLowerCase() == "table"){
		TblId.push(id);		
		if(arguments.length>1){
			for(var i=0; i<arguments.length; i++){
				var argtype = typeof arguments[i];
				switch(argtype.toLowerCase()){
					case "number":
						ref_row = arguments[i];
					break;
					case "object":
						fObj = arguments[i];
					break;
				}
			}
		}
		ref_row == undefined ? StartRow.push(2) : StartRow.push(ref_row+2);
		var ncells = getCellsNb(id,ref_row);
		AddRow(id,ncells,fObj);		
	}
}

function AddRow(id,n,f)
/*====================================================
	- adds a row containing the filtering grid
=====================================================*/
{	
	var t = document.getElementById(id);
	var row = t.getElementsByTagName("tr");
	var start_row = getStartRow(id);
	var fltrow = t.insertRow(0);
	var inpclass, displayBtn, btntext, enterkey;
	var modfilter_fn, display_allText, on_slcChange;
	var displaynrows, totrows_text, btnreset, btnreset_text;
	var sort_slc, displayPaging, pagingLength, displayLoader;
	var load_text, exactMatch;
	
	f!=undefined && f["btn"]==false ? displayBtn = false : displayBtn = true;//shows/hides button
	f!=undefined && f["btn_text"]!=undefined ? btntext = f["btn_text"] : btntext = "go";//defines button text
	f!=undefined && f["enter_key"]==false ? enterkey = false : enterkey = true;//enables/disables enter key
	f!=undefined && f["mod_filter_fn"] ? modfilter_fn = true : modfilter_fn = false;//defines alternative fn
	f!=undefined && f["display_all_text"]!=undefined ? display_allText = f["display_all_text"] : display_allText = "";//defines 1st option text
	f!=undefined && f["on_change"]==true ? on_slcChange = true : on_slcChange = false;//enables/disables onChange event on combo-box 
	f!=undefined && f["display_nrows"]==true ? displaynrows = true : displaynrows = false;//show/hides rows counter
	f!=undefined && f["nrows_text"]!=undefined ? totrows_text = f["nrows_text"] : totrows_text = "Data rows: ";//defines rows counter text
	f!=undefined && f["btn_reset"]==true ? btnreset = true : btnreset = false;//show/hides reset link
	f!=undefined && f["btn_reset_text"]!=undefined ? btnreset_text = f["btn_reset_text"] : btnreset_text = "Reset";//defines reset text
	f!=undefined && f["sort_select"]==true ? sort_slc = true : sort_slc = false;//enables/disables select options sorting
	f!=undefined && f["paging"]==true ? displayPaging = true : displayPaging = false;//enables/disables table paging
	f!=undefined && f["paging_length"]!=undefined ? pagingLength = f["paging_length"] : pagingLength = 10;//defines table paging length
	f!=undefined && f["loader"]==true ? displayLoader= true : displayLoader = false;//enables/disables loader
	f!=undefined && f["loader_text"]!=undefined ? load_text = f["loader_text"] : load_text = "Loading...";//defines loader text
	f!=undefined && f["exact_match"]==true ? exactMatch= true : exactMatch = false;//enables/disbles exact match for search
	

	if(modfilter_fn){
		ModFnId.push(id);// used by DetectKey fn
		ModFn.push(f["mod_filter_fn"]);
	}
	
	if(exactMatch){
		ExactMatchId.push(id);// used by DetectKey fn
		ExactMatch.push(exactMatch);
	}	

	if(displaynrows || btnreset || displayPaging || displayLoader){
		/*** div containing rows # displayer + reset btn ***/
		var infdiv = document.createElement("div");
		infdiv.setAttribute("id","inf_"+id);
		infdiv.className = "inf";
		t.parentNode.insertBefore(infdiv, t);
		
		if(displaynrows){
			/*** left div containing rows # displayer ***/
			var totrows;
			var ldiv =  document.createElement("div");
			ldiv.setAttribute("id","ldiv_"+id);
			displaynrows ? ldiv.className = "ldiv" : ldiv.style.display = "none";
			displayPaging ? totrows = pagingLength : totrows = getRowsNb(id);
			
			var totrows_span = document.createElement("span"); // tot # of rows displayer 
			totrows_span.setAttribute("id","totrows_span_"+id);
			totrows_span.className = "tot";
			totrows_span.appendChild( document.createTextNode(totrows) );
		
			var totrows_txt = document.createTextNode(totrows_text);
			ldiv.appendChild(totrows_txt);
			ldiv.appendChild(totrows_span);
			infdiv.appendChild(ldiv);
		}
		
		if(displayLoader){
			/*** div containing loader  ***/
			var loaddiv =  document.createElement("div");
			loaddiv.setAttribute("id","load_"+id);
			loaddiv.className = "loader";
			loaddiv.style.display = "none";
			loaddiv.appendChild(document.createTextNode(load_text));	
			infdiv.appendChild(loaddiv);
		}
				
		if(displayPaging){
			row[0].setAttribute("paging", "true"); //set paging attribute
			row[0].setAttribute("pagingLength", pagingLength); //set pagingLength attribute
			row[0].setAttribute("pagingStart", 0);
			
			for(var j=start_row; j<row.length; j++)	{
				row[j].setAttribute("rowValid", "true"); //rend toutes les lignes valides au filtrage pour l'initialisation
			}

			/*** mid div containing paging displayer ***/
			var mdiv =  document.createElement("div");
			mdiv.setAttribute("id","mdiv_"+id);
			displayPaging ? mdiv.className = "mdiv" : mdiv.style.display = "none";						
			infdiv.appendChild(mdiv);
			
			var start_row = getStartRow(id);
			var nrows = t.getElementsByTagName("tr").length;
			var npages = Math.ceil( (nrows - start_row)/pagingLength );
			
			var slcPages = document.createElement("select");
			slcPages.setAttribute("id","slcPages_"+id);
			slcPages.onchange = function(){ 
				if(displayLoader) showLoader(id,"");
				
				row[0].setAttribute("pagingStart", this.value); //set pagingStart attribute row du départ de paging
				if(displayLoader) showLoader(id,"none");
				
				(!modfilter_fn) ? Filter(id, exactMatch)  :  f["mod_filter_fn"]; //Filter(id,exactMatch); //ligne ajoutée pour evenement de filtrage si displayPaging actif
			}
			document.getElementById("mdiv_"+id).appendChild( document.createTextNode(" Page ") );
			document.getElementById("mdiv_"+id).appendChild(slcPages);
			document.getElementById("mdiv_"+id).appendChild( document.createTextNode(" of "+npages+" ") );
			for(var t=0; t<npages; t++){
				var currOpt = new Option((t+1),t*pagingLength,false,false);
				document.getElementById("slcPages_"+id).options[t] = currOpt;
			}
		}
		
		if(btnreset){
			/*** right div containing reset button **/	
			var rdiv =  document.createElement("div");
			rdiv.setAttribute("id","reset_"+id);
			btnreset ? rdiv.className = "rdiv" : rdiv.style.display = "none";
			
			var fltreset = document.createElement("a");
			fltreset.setAttribute("href","javascript:clearFilters('"+id+"');Filter('"+id+"','"+exactMatch+"');");
			fltreset.appendChild(document.createTextNode(btnreset_text));
			rdiv.appendChild(fltreset);
			infdiv.appendChild(rdiv);
		}
		
	}


	/* =====================================================================
	Ecrit les contrôles pour le filtrage
	contôle de type <input>
	contrôle de type <select>
	===================================================================== */
	for(var i=0; i<n; i++){
		var fltcell = fltrow.insertCell(i);
		fltcell.noWrap = true;
		i==n-1 && displayBtn==true ? inpclass = "flt_s" : inpclass = "flt";
		
		if(f==undefined || f["col_"+i]==undefined || f["col_"+i]=="none"){
			var inp = document.createElement("input");		
			inp.setAttribute("id","flt"+i+"_"+id);
			if(f==undefined || f["col_"+i]==undefined) inp.setAttribute("type","text");
			else inp.setAttribute("type","hidden");
			//inp.setAttribute("class","flt"); //doesn't seem to work on ie<=6
			inp.className = inpclass;			
			fltcell.appendChild(inp);
			if(enterkey) inp.onkeypress = DetectKey;
		}
		else if(f["col_"+i]=="select"){
			var slc = document.createElement("select");
			var indexCell=i;
			
			slc.setAttribute("id","flt"+indexCell+"_"+id);
			slc.className = inpclass;
			fltcell.appendChild(slc);
			memSelect[id]["flt"+indexCell+"_"+id]=display_allText;
			
			//stores arguments for PopulateOptions()
			var args = new Array();
			args.push(id); args.push(i); args.push(n);
			args.push(display_allText); args.push(sort_slc); args.push(displayPaging);
			SlcArgs.push(args);
			
			if(enterkey) slc.onkeypress = DetectKey;
			if(on_slcChange) {(!modfilter_fn) ? slc.onchange = function(){ fnMenSelect(id,this); Filter(id,exactMatch); } : slc.onchange = f["mod_filter_fn"];} 
		}
		
		if(i==n-1 && displayBtn==true){// this adds button
			var btn = document.createElement("input");
			
			btn.setAttribute("id","btn"+i+"_"+id);
			btn.setAttribute("type","button");
			btn.setAttribute("value",btntext);
			btn.className = "btnflt";
			
			fltcell.appendChild(btn);
			(!modfilter_fn) ? btn.onclick = function(){ Filter(id,exactMatch); } : btn.onclick = f["mod_filter_fn"];					
		}		
		
	}
	
	/* =====================================================================
	Appel de la fonction Filter avec ematch=false
	Liste les filtres qui ici sont vides: getFilters();
	Provoque l'affichage des données: affichage();
	===================================================================== */
	Filter(id,false);
}

function Filter(id,ematch){
/*====================================================
	- gets search strings from SearchFlt array
	- retrieves data from each td in every single tr
	and compares to search string for current
	column
	- tr is hidden if all search strings are not 
	found
=====================================================*/
	showLoader(id,"");
	getFilters(id);
	var t = document.getElementById(id);
	var SearchArgs = new Array();
	var ncells = getCellsNb(id);
	var totrows = getRowsNb(id), hiddenrows = 0;
	//totrows est le nombre total de lignes du tableau hors paging
	
	for(var i=0; i<SearchFlt.length; i++)
		SearchArgs.push((document.getElementById(SearchFlt[i]).value).toLowerCase());
	
	var start_row = getStartRow(id);
	var row = t.getElementsByTagName("tr");
	
	for(var k=start_row; k<row.length; k++){	
		/*** if table already filtered some rows are not visible ***/
		if(row[k].style.display == "none") row[k].style.display = "";
		
		var cell = getChildElms(row[k]).childNodes;
		var nchilds = cell.length;

		if(nchilds == ncells){// checks if row has exact cell #
			var cell_value = new Array();
			var occurence = new Array();
			var isRowValid = true;
				
			for(var j=0; j<nchilds; j++){// this loop retrieves cell data
				var cell_data = getCellText(cell[j]).toLowerCase();
				cell_value.push(cell_data);
				
				if(SearchArgs[j]!=""){
					var num_cell_data = parseFloat(cell_data);
					
					if(/<=/.test(SearchArgs[j]) && !isNaN(num_cell_data)){ // first checks if there is an operator (<,>,<=,>=)
						num_cell_data <= parseFloat(SearchArgs[j].replace(/<=/,"")) ? occurence[j] = true : occurence[j] = false;
					} else if(/>=/.test(SearchArgs[j]) && !isNaN(num_cell_data)){
						num_cell_data >= parseFloat(SearchArgs[j].replace(/>=/,"")) ? occurence[j] = true : occurence[j] = false;
					} else if(/</.test(SearchArgs[j]) && !isNaN(num_cell_data)){
						num_cell_data < parseFloat(SearchArgs[j].replace(/</,"")) ? occurence[j] = true : occurence[j] = false;
					} else if(/>/.test(SearchArgs[j]) && !isNaN(num_cell_data)){
						num_cell_data > parseFloat(SearchArgs[j].replace(/>/,"")) ? occurence[j] = true : occurence[j] = false;
					} else{
						var regexp;
						if(ematch) regexp = new RegExp('(^)'+SearchArgs[j]+'($)',"gi");
						else regexp = new RegExp(SearchArgs[j],"gi");
						occurence[j] = regexp.test(cell_data);
					}
				}
			}
			
			for(var t=0; t<ncells; t++){
				if(SearchArgs[t]!="" && !occurence[t]) isRowValid = false;
			}	
		}			

		
		// checks if table is paged and displays rows
		var isPaged = row[k].getAttribute("paging"); //retrieves paging attribute
		
		//test de validité de la ligne pour affichage
		if (!isRowValid){
			row[k].setAttribute("rowValid", "false");//lignes ajoutées pour contrôle de validation
			hiddenrows++; //il faut remettre cette option pour les fonctions jpconnexion: affichage
		}
		else row[k].setAttribute("rowValid", "true");//lignes ajoutées pour contrôle de validation
	}

	/*** refreshes tot # of rows displayer ***/
	
	affichage(id);
}

function getCellsNb(id,nrow){
/*====================================================
	- returns number of cells in a row
	- if nrow param is passed returns number of cells 
	of that specific row
=====================================================*/
  	var t = document.getElementById(id);
	var tr;
	if(nrow == undefined) tr = t.getElementsByTagName("tr")[0];
	else  tr = t.getElementsByTagName("tr")[nrow];
	var n = getChildElms(tr);
	return n.childNodes.length;
}

function getRowsNb(id){
/*====================================================
	- returns total nb of rows for a given table
=====================================================*/
	var t = document.getElementById(id);
	var s = getStartRow(id);
	var ntrs = t.getElementsByTagName("tr").length;
	return parseInt(ntrs-s);
}

function getFilters(id){
/*====================================================
	- filter (input or select) ids are stored in  
	SearchFlt array
=====================================================*/
	SearchFlt = new Array();
	var t = document.getElementById(id);
	var tr = t.getElementsByTagName("tr")[0];
	var enfants = tr.childNodes;
	
	for(var i=0; i<enfants.length; i++) SearchFlt.push(enfants[i].firstChild.getAttribute("id"));
}

function getStartRow(id){
/*====================================================
	- returns starting row for Filter fn for a 
	given table id
=====================================================*/
	var r;
	for(j in TblId){
		if(TblId[j] == id) r = StartRow[j];
	}
	return r;
}

function clearFilters(id){
/*====================================================
	- clears grid filters
=====================================================*/
	getFilters(id);
	for(i in SearchFlt) document.getElementById(SearchFlt[i]).value = "";
}

function showLoader(id,p){
/*====================================================
	- displays/hides loader div
=====================================================*/
	var loaderdiv = document.getElementById("load_"+id);
	if(loaderdiv != null && p=="none")
		setTimeout("document.getElementById('load_"+id+"').style.display = '"+p+"'",250);
	else if(loaderdiv != null && p!="none") loaderdiv.style.display = p;
}

function showTotRowsN(id,p){
/*====================================================
	- Shows total number of filtered rows
=====================================================*/
	var totrows_displayer = document.getElementById("totrows_span_"+id);
	if(totrows_displayer != null && totrows_displayer.nodeName.toLowerCase() == "span" ) 
		totrows_displayer.innerHTML = p;
}

function getChildElms(n){
/*====================================================
	- checks passed node is a ELEMENT_NODE nodeType=1
	- removes TEXT_NODE nodeType=3  
=====================================================*/
	if(n.nodeType == 1){
		var enfants = n.childNodes;
		for(var i=0; i<enfants.length; i++){
			var child = enfants[i];
			if(child.nodeType == 3) n.removeChild(child);
		}
		return n;	
	}
}

function getCellText(n){
/*====================================================
	- returns text + text of child nodes of a cell
=====================================================*/
	var s = "";
	var enfants = n.childNodes;
	for(var i=0; i<enfants.length; i++){
		var child = enfants[i];
		if(child.nodeType == 3) s+= child.data;
		else s+= getCellText(child);
	}
	return s;
}

function DetectKey(e){
/*====================================================
	- common fn that detects return key for a given
	element (onkeypress attribute on input)
=====================================================*/
	var evt=(e)?e:(window.event)?window.event:null;
	if(evt){
		var key=(evt.charCode)?evt.charCode:
			((evt.keyCode)?evt.keyCode:((evt.which)?evt.which:0));
		if(key=="13"){
			var cid, leftstr, tblid, CallFn, Match;		
			cid = this.getAttribute("id");
			leftstr = this.getAttribute("id").split("_")[0];
			tblid = cid.substring(leftstr.length+1,cid.length);
			for(i in ModFn)	ModFnId[i] == tblid ? CallFn=true : CallFn=false;
			for(j in ExactMatchId){ if(ExactMatchId[j] == tblid) Match = ExactMatch[j]; }
			(CallFn) ? ModFn[i].call() : Filter(tblid,Match);
		}
	}
}


/*=================================================
Fonctions ajoutés par jpconnexion
=================================================*/


function affichage(id) {
	/*
	id est l'identifiant du tableau
	nbRows est le nombres de lignes à afficher en fonction du filtre
	*/
	var t = document.getElementById(id);
	var row = t.getElementsByTagName("tr");
	var displayPaging = row[0].getAttribute("paging"); //retrieves paging attribute
	var groupe=1;
	var pagingStart = row[0].getAttribute("pagingStart");
	
	PopulateOptions(id);
	if (displayPaging=="true"){
		GroupByPage(id,groupe);//calls Create Group page function
	}
	else affichageNotPaging(id);
}


function affichageNotPaging(id){
	var t = document.getElementById(id);
	var row = t.getElementsByTagName("tr");
	var start_row = getStartRow(id);
	var counter=0;
	
	for(var k=start_row; k<row.length; k++){
		var isRowValid = row[k].getAttribute("rowValid"); //retrieves rowValid attribute
				
		//affichage du filtre sans paging
		if(isRowValid=="false") {
			row[k].style.display = "none";
		} else {
			row[k].style.display = ""; // affichage de la ligne
			counter++;
		}
	}
	

	showTotRowsN(id, counter); // mets à jour le nombre de lignes retournées
	
	showLoader(id,"none"); //affiche or masque le message loader
}


function GroupByPage(id,valeurGroupe) {
	var t = document.getElementById(id);
	var row = t.getElementsByTagName("tr");
	var start_row = getStartRow(id); //ligne de départ actif - élimine les lignes rajoutées en t^te du tableau par le script et les éventuelles lignes de titre définies lors de l'appel de setFilterGrid
	var counter=0;
	var page=1;
	var nTotRows=row.length;
	var longueurPage = row[0].getAttribute("pagingLength");
	var nRows; //retourne le nombre de ligne réellement affichées
	/* ===========================================
	il faut :
	appliquer le filtre
	calculer les groupes de page 
	afficher la page valeurGroupe
	========================================== */
	for(var k=start_row; k<nTotRows; k++){
		//row[k].style.display = "none";//efface le tableau pour le ré-écrire
		var isRowValid = row[k].getAttribute("rowValid"); //retrieves rowValid attribute
		
		if(isRowValid=="false") {
			row[k].style.display = "none";
		} else {
			// les lignes répondent aux critères du filtre
			counter++;
			if (page==valeurGroupe)	{
				row[k].style.display = ""; // ces lignes correspondent aux critères du filtre et seront affichées
				nRows=counter; //retourne le nombre de ligne réellement affichées
			} else{
				row[k].style.display = "none";//ces lignes correspondent aux critères du filtre mais ne seront pas affichées;
			}
			if (counter >= longueurPage){
				//il faut créer un nouveau groupe de page
				counter=0;
				page++;
				if (k==nTotRows-1) page--;
			}
		}
	}
	/* ==================================================
	les groupes de pages correspondants aux critières du filtre sont constitués
	le groupe valeurGroupe est prêt à l'affichage
	il faut mettre le contrôle select du nombre de pages à jour
	================================================== */

	var selectText
	selectText="Page";
	selectText += '<select id = "slcPages_'+id+'" onchange=GroupByPage("'+id+'",this.value); >';
	for (var n=1; n<page+1; n++){
		if (n==valeurGroupe) selectText += '<option selected value=' + n + '>'+n+'</option>'; //affichage du nombre de page dans le contrôle select
		else selectText += '<option value=' + n + '>'+n+'</option>'; //affichage du nombre de page dans le contrôle select
	}
	selectText += '</select>';
	selectText += 'of '+page+' ';
	
	var slcPagesDiv = document.getElementById("mdiv_"+id);
	slcPagesDiv.innerHTML = selectText;
	
	showTotRowsN(id, nRows); // mets à jour le nombre de lignes affichées
	
	showLoader(id,"none"); //affiche or masque le message loade
}



function PopulateOptions(id){
/*====================================================
	- populates select
	- adds only 1 occurence of a value
=====================================================*/
	var t = document.getElementById(id);
	var start_row = getStartRow(id);
	var row = t.getElementsByTagName("tr");
	
	for(var i=0; i<SlcArgs.length; i++){//this loop retrieves PopulateOptions() fn params
		if(SlcArgs[i][0]==id) {				
			//id = SlcArgs[i][0];
			var cellIndex = SlcArgs[i][1];
			var ncells = SlcArgs[i][2];
			var opt0txt = SlcArgs[i][3]; //display_all_text
			var sort_opts = SlcArgs[i][4];
			//displayPaging = SlcArgs[i][5];
	
			var OptArray = new Array();
			var optIndex = 0; // option index
			var indexSelected=0;
			var objectSelect = document.getElementById("flt"+cellIndex+"_"+id);
			var currOpt = new Option(opt0txt,"",false,false); //1st option
			objectSelect.options[optIndex] = currOpt; //charge le texte par défaut de la première option indice 0

			/* ===================================================================
			Recherche des options qui correspondent aux critères du filtrage
			Vérifie que ces options soient uniques dans la liste du tableau
			==================================================================== */
			
			for(var k=start_row; k<row.length; k++){
				var cell = getChildElms(row[k]).childNodes;
				var nchilds = cell.length;
				var isRowValid = row[k].getAttribute("rowValid"); //retrieves rowValid attribute
				
				if(nchilds == ncells){// checks if row has exact cell #
					for(var j=0; j<nchilds; j++){// this loop retrieves cell data
						if(cellIndex==j){
							var cell_data = getCellText(cell[j]);
							
							// checks if celldata is already in array
							var isMatched = false;
							for(w in OptArray){
								if( cell_data == OptArray[w] ) isMatched = true;//la valeur est déjà dans le tableau, ne pas la retenir à nouveau
							}
							if(!isMatched && isRowValid=="true"){
								OptArray.push(cell_data);//ajoute au tableau si première valeur trouvée et ligne répond aux critères du filtre
							}
						}
					}
				}
			}
			
			if(sort_opts) OptArray.sort();
			/*====================================================================
			Le tableau des options correspondant au filtre est créé
			Il faut maintenant écrire les options dans le contrôle select
			Il faut tout d'abord initialiser la première ligne option indépendante du filtre qui permet de tout valider
			Puis écrire les options
			Si une option est sélectionnée il faut la détectée option selected="selected"
			=====================================================================*/
			
			var SelectedValeur = memSelect[id]["flt"+cellIndex+"_"+id];

			for(y in OptArray){
				optIndex++;
				
				if(SelectedValeur == OptArray[y]) 	indexSelected=optIndex;
				currOpt = new Option(OptArray[y],OptArray[y],false,false);
				objectSelect.options[optIndex] = currOpt;
			}
			objectSelect.options[indexSelected].selected="selected";		
			var longueur=objectSelect.length;//fourni le nombre de clause <option> du contrôle Select
			for(var n=longueur-1; n>optIndex; n--){
				objectSelect.remove(n);//vide le contrôle select de toutes ces <options> sauf la première option: display_all_text
			}
		}
	}
}

function fnMenSelect(id,objet){
	var valeur = objet.value;
	var index = objet.id;
	memSelect[id][index] = valeur;
	
}


