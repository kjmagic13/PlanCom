jQuery(document).ready(function($){

	$('.row1, .row2, .library').sortable({
		connectWith: '.connectedSortable',
		placeholder: "ui-state-highlight",
		cancel: ".disabled"
	}).disableSelection();

	// submit icons
	$('#update_icons').click(function(){
		var obj = {};
		$('.row1').each(function(){
			var role = $(this).data('role');
			obj[role] = {};
			obj[role]['row1'] = [];
			$(this).find('li').each(function(){
				obj[role]['row1'].push( $(this).attr('title') );
			});
		});
		$('.row2').each(function(){
			var role = $(this).data('role');
			obj[role]['row2'] = [];
			$(this).find('li').each(function(){
				obj[role]['row2'].push( $(this).attr('title') );
			});
		});
		$('.library').each(function(){
			var role = $(this).data('role');
			obj[role]['library'] = [];
			$(this).find('li').each(function(){
				obj[role]['library'].push( $(this).attr('title') );
			});
		});
		$('.formats').each(function(){
			var role = $(this).data('role');
			obj[role]['formats'] = [];
			$(this).find('input').each(function(){
				if( $(this).is(':checked') ) {
					obj[role]['formats'].push( $(this).attr('name') );
				}
			});
		});
		$.post(
			ajaxurl,
			{
				'action': 'plancom_save_wysiwyg',
				'data': obj
			}, 
			function(response){
				if( response == 0 ) {
					alert('Icons saved successfully.');
				} else {
					alert('The server responded: ' + response);
				}
			});
		return false;
	});

	// reset icons
	$('.reset_icons').on('click', function(){
		var role = $(this).data('role');
		if( confirm('Are you sure?') ){
			$.post(
				ajaxurl,
				{
					'action': 'plancom_reset_mce_icons',
					'data': role
				}, 
				function(response){
					if( response == 0 ) {
						alert('Icons reset successfully.');
						window.location.reload(true);
					} else {
						alert('The server responded: ' + response);
					}
				});
		}
		return false
	});

});