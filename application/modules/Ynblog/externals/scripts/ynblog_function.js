function openPage(url, vHeight, vWidth)
{ 
     window.location  = url;
     return false;
}

function openPageOpener(url)
{
	opener= window.open(url);
	opener.focus();
	return false;
}
function getKeyDown(evt)
{
    evt = evt || window.event;
    var charCode = evt.keyCode || evt.which;
    if (charCode == 13) {
       
        return do_search();
    }

}
function roundNumber(num, dec)
{
        var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
        return result;
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
    }
    return "";
}
