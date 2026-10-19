
// --------------- Wozu ? --------------------------
function loadImage(src) {
	log ('Load image:' + src);
	const img = new Image();
	img.src = src;
	try {
		img.decode(); // waits until image is decoded.
		document.getElementById("clock").append(img); // create img in html document.
	} catch (err) {
		error("Load image error! err=", err);
	}
}

function animate(timestamp) {
  // Animation basierend auf timestamp aktualisieren
  const element = document.getElementById('animated-element');
  element.style.transform = `translateX(${timestamp / 10}px)`;
  
  // Animation fortsetzen
  if (timestamp < 5000) {
    requestAnimationFrame(animate);
  }
}


// --------------------------------------------------------------------
// Automatische Wiedergabe muss erlaubt sein:
// - Im Browser neben der URL ist ein Einstellungsicon.
// - Mit der linken Maustaste auf das Icon klicken.
// - "Automatische Wiedergabe": "Audio und Video erlauben"
// - Die Einstellung ist dann auch unter 
//   "Einstellungen - Datenschutz & Sicherheit - Berechtigungen - Automatische Wiedergabe"
//   zu finden.
// - Javascript:
//   var audio = new Audio('images/universfield-sad-trumpet-278822.mp3');
//   audio.play();
// --------------------------------------------------------------------

/* =========================================== */
/* Call URL
*/

function call_url2(url) { 
	info("troet.js call_url2() url=" + url);
	fetch(url)
	.then(response => response.text())
	.then(html => {
		document.open();
		document.write(html);
		document.close();
	})
	.catch(err => { error('troet.js call_url2() Fetch error:'+ err); });
	// .then(data => document.querySelector('body').innerHTML=data);
}
function call_url(url) { 
	info("troet.js call_url() Get url=" + url);
	window.location.href = url;
}

/* =========================================== */
/* Timer for Troet game.
*/

function timer() {
  i = 20;
  if (i > 0) {
    i--;
    var timeout = window.setTimeout('countdown(' + i + ')', 1000); // Funktion verzögert aufrufen
  }
}

/* =========================================== */
/* Get Client Information */

function init() {
	dev = document.getElementById('device');
	var name;
	/* name = getLocation(); */
	name = getUseragent();
	dev.textContent = name;
	info("troet.js init() userAgent=" + name);
}

function getLocation() {
	var loc = window.location;					// http://localhost/troetphp/index.php
	return loc;
}
function getUseragent() {
	var userAgent = navigator.userAgent;		// Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0

	var userAgentLow = userAgent.toLowerCase();
	var myName;
	if(userAgentLow.match('iphone')) myName = "iPhone";
	else if(userAgentLow.match('ipad')) myName = "iPad";
	else if(userAgentLow.match('android'))	myName = "Android";	
	else if(userAgentLow.match('linux')) myName = "Linux";	
	else if(userAgentLow.match('windows phone')) myName = "Windows_Phone";
	else myName = "Unknown";

	return myName;
}

/* =========================================== */
/* Get Screen size of Browser window */

function sizes() {
	var w = window.innerWidth;
	var h = window.innerHeight;
	fetch("http://localhost/troet?width=" + w + "&height=" + h);
}

/* ============================================ */

