$(document).ready(function() {
	//Hotkeys
	var key = 'ctrl';
	if( navigator.appVersion.indexOf("Mac") != -1 )
		key = 'meta';
		
	$('#form_pageContent').bind('keydown.'+key+'_s',function(){ $('form').submit(); return false; });
	$(document).bind('keydown.alt_'+key+'_x',function(){ location.href = $('#form_cancel').attr('href'); return false; });
	$(document).bind('keydown.'+key+'_e',function(){ location.href = $('#page_edit').attr('href'); return false; });
	$(document).bind('keydown.alt_'+key+'_h',function(){ location.href = $('#menu_home').attr('href'); return false; });
	
	//Textaea height
	$(window).resize(function() {
		$('#form_pageContent').height( $(window).height() - 280);
	}).resize();
	
	//Tabs
	$('.nav-tabs a').click(function(e) {

		var mode = this.hash.slice(1);
		if( mode != 'edit' )
			$('#'+mode).load( 'view/'+mode, { 'content': $('#form_pageContent').val() } );
		
		if( mode )
			$('.nav-tabs .dropdown-toggle span').html( $(this).html() );
			
		$(this).tab('show');
		e.preventDefault();
	})
});
