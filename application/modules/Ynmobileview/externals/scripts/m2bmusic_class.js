function _addPlaylist(songId) {
	var url = en4.core.baseUrl + "music/song/" + songId + "/append";
	Smoothbox.open(url);
}

function _changePlayCount(MusicID) 
{
	new Request.JSON({
	'format': 'json',
	'url' : en4.core.baseUrl + "music/song/" + MusicID + "/tally",
	'onSuccess' : function(responseJSON, responseText) 
		{
		}
	}).send();
}
