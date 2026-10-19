
window.addEventListener("load", initEventListener);

function openSidebar(id) { 
	var sb = document.getElementById(id);
	if( sb ) { sb.style.display = "block"; }
}
function closeSidebar(id) { 
	var sb = document.getElementById(id)
	// var w  = window.screen.width;
	var w = window.innerWidth;
	if( sb ) { 
		var className = sb.className;
		if( className != "content" || w < 1024 ) {		/* fuer "Screen" und "report" soll der Inhalt angezeigt werden. */ 
			// console.log("Close Sidebar id=" + id + ", className=" + className + ", width=" + w + "!");
			sb.style.display = "none"; 
		} else {
			// console.log("Do not close Sidebar id=" + id + ", className=" + className + ", with=" + w + "!")
		}
	}
}
function getSidebar(id) { 
	var sb = document.getElementById(id)
	if( sb ) { return sb.style.display; }
}
function toggleSidebar(id) {
	// console.log("Toggle Sidebar id=" + id + "!");
	if(getSidebar(id) == "none") { openSidebar(id); }
	else { closeSidebar(id); }
}
function closeAllSidebars() {
	// console.log("Close All Sidebars!");
	closeSidebar("sidebar");
}
function toggleSidebar1() { toggleSidebar("sidebar"); closeSidebar("sidebar2") }
function closeOutsideClick() {
	e = event;
	// console.log("Close Sidebar from Outside Click!");
	but1 = document.getElementById("menu");
	nav1 = document.getElementById("sidebar");
	if(!but1.contains(e.target) && !nav1.contains(e.target)) {
		if( nav1 ) { closeSidebar("sidebar"); }
	}
}

/* addEventListener() muss am Ende des Dokumentes aufgerufen werden, weil am Anfang die
Id von getElementById() noch nicht bekannt ist. D.h.
 - <script>...</script> Tags ans Ende der HTML Datei oder
 - window.addEventListener("load", initEventListener);
   Diese werden dann initialisiert!
*/

function initEventListener() {
	closeAllSidebars();
	document.getElementById("menu").addEventListener("click", toggleSidebar1);
	document.getElementById("menu_item").addEventListener("click", closeAllSidebars);

	const arr = document.getElementsByClassName("menu_item");
	for (let i = 0; i < arr.length; i++) {
		arr[i].addEventListener("click", closeAllSidebars);
	}
	document.addEventListener("click", closeOutsideClick);
	/* document.onclick = closeOutsideClick(event); => NOK, Geht nicht !!! */
}


