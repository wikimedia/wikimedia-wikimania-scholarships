function insertStamp(username) {
	notes = document.getElementById('notes');
	now = new Date();
        year = now.getUTCFullYear();
        month = now.getUTCMonth() + 1;
        day = now.getUTCDate();
        hours = now.getUTCHours();
        minutes = now.getUTCMinutes();
        notes.value = month + '/' + day + ' '
        + hours + ':' + (minutes < 10 ? '0' + minutes : minutes)
	+ ' ' + "<?= $username['username'] ?>"
	+ ": \n\n" + notes.value;
}

var menuActive = function() {
	var mainReq = window.location.toString().split(window.location.hostname)[1].split('?')[0];

	$('#review-tabs li a').each(function() {
		if ( mainReq == $(this).attr('href') ) {
			$(this).css({'color' : '#0080C0'});
		}
	});

}

$(document).ready(function() {
	menuActive();
	$('#dump').hide();
	$('#showfulldump').click(function() {
		$('#dump').toggle();
		return false;
	});
	$('#rankingstoggle').click(function() {
		$('#rankingitems').toggle();
		return false;
	});
	$('#rank-box').draggable().resizable();
	$('#grid').flexigrid({height:'auto',striped:false});
});
