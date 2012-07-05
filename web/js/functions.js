$(document).ready(function() {
	var key = 'ctrl';
	if( navigator.appVersion.indexOf("Mac") != -1 )
		key = 'meta';
		
	$(document).bind('keydown.'+key+'_s',function(){ $('form').submit(); return false; });
	$(document).bind('keydown.alt_'+key+'_x',function(){ location.href = $('#form_cancel').attr('href'); return false; });
	$(document).bind('keydown.'+key+'_e',function(){ location.href = $('#page_edit').attr('href'); return false; });
});
