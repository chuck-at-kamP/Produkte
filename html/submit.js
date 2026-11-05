
var tosubmit = ['action','id'];
function add(name) {
	console.log("Add " + name);
    if(tosubmit.indexOf(name) == -1) tosubmit.push(name);
}
function addSelect(name) {
	console.log("Add " + name);
    if(tosubmit.indexOf(name) == -1) tosubmit.push(name);
}
function addSubmit(name,obj) {
	console.log("Add " + name);
    if(tosubmit.indexOf(name) == -1) tosubmit.push(name);
	console.log("Submit " + name);
	add_and_submit();
}

function add_and_submit() {
	console.log("Add and Submit");
	var form = document.getElementById('edit-form');
	var input = document.createElement('input');
	input.setAttribute('name', "submit_category");
	input.setAttribute('value', "changed");
	input.setAttribute('type', "text");
	form.appendChild(input);
    if(tosubmit.indexOf(name) == -1) tosubmit.push("submit_category");
	beforeSubmit();
	form.submit();
}

function is_changed(name) {
    for(var k = 0; k < tosubmit.length; k++)
        if(name == tosubmit[k]) return name && true;
    return false;
}

function beforeSubmit() {
	console.log("before submit");
    var allInputs = document.getElementsByTagName("input");
    var allSelects = document.getElementsByTagName("select");
    var allTextAreas = document.getElementsByTagName("textarea");
    for(var k = 0; k < allInputs.length; k++) {
        var name = allInputs[k].name;
        if(!is_changed(name)) {
			allInputs[k].disabled = true;
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

