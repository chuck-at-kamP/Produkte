
var DEBUG = false;
// var DEBUG = true;

function log(text,val) { if(DEBUG) { if(!val) val=" "; console.log(new Date().toLocaleString() + " DBUG - " + text,val); } }
function info(text,val) { if(!val) val=" "; console.log(new Date().toLocaleString() + " INFO - " + text,val); }
function warn(text,val) { if(!val) val=" "; console.warn(new Date().toLocaleString() + " WARN - " + text,val); }
function error(text,val) { if(!val) val=" "; console.error(new Date().toLocaleString() + " ERR  - " + text,val); }

