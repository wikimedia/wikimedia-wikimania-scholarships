$(document).ready(function() {
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
