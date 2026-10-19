
var tosubmit = ['action','id'];
function add(name) {
	console.log("Add " + name);
    if(tosubmit.indexOf(name) == -1) tosubmit.push(name);
}

function is_changed(name) {
    for(var k = 0; k < tosubmit.length; k++)
        if(name == tosubmit[k]) return name && true;
    return false;
}

function beforeSubmit() {
    var allInputs = document.getElementsByTagName("input");
    var allSelects = document.getElementsByTagName("select");
    var allTextAreas = document.getElementsByTagName("textarea");
    for(var k = 0; k < allInputs.length; k++) {
        var name = allInputs[k].name;
        if(!is_changed(name)) {
			console.log("Not Changed " + name);
			allInputs[k].disabled = true;
		}
		else {
			console.log("Changed " + name);
		}
    }
    for(var k = 0; k < allSelects.length; k++) {
        var name = allSelects[k].name;
        if(!is_changed(name)) {
			allSelects[k].disabled = true;
		}
    }
    for(var k = 0; k < allTextAreas.length; k++) {
        var name = allTextAreas[k].name;
        if(!is_changed(name)) {
			allTextAreas[k].disabled = true;
		}
    }
}

