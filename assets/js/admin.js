jQuery(document).ready(function($){

	// Ajax 
	// perform tasks
	$('form#tasks').submit(function(event){
		// event.preventDefault;
		var submit_button = $( '#submit', $(this) );
		var progressbar = $( '#percent_progressbar', $(this) );
		submit_button.hide();
		progressbar.show();
		var data = $(this).serialize();
		// alert( data );

		var myInterval = setInterval(function() {
			$.post(
				ajaxurl,
				{
					'action': 'plancom_cp_percentage'
				}, 
				function(percent){
					$( "#percent_progressbar" ).progressbar({
						value: parseInt(percent)
					});
					// alert(percent);
				}
				);
		}, 1000);

		$.post(
			ajaxurl,
			{
				'action': 'plancom_perform_tasks',
				'data': data
			}, 
			function(response){
				if( response == 0 ) {
					alert('Tasks performed successfully.');
				} else {
					alert('The server responded: ' + response);
				}
				clearInterval(myInterval);
				submit_button.show();
				progressbar.hide();
			}
			);
		$(this)[0].reset();
		return false;
	});
	// save plugin settings
	$('form#plugin_settings').submit(function(event){
		// event.preventDefault;
		var submit_button = $( '#submit', $(this) );
		var progressbar = $( '.indeterminate-progressbar', $(this) );
		submit_button.hide();
		progressbar.show();
		var data = $(this).serialize();
		// alert( data );
		$.post(
			ajaxurl,
			{
				'action': 'plancom_save_options',
				'data': data
			}, 
			function(response){
				if( response == 0 ) {
					alert('Settings saved successfully.');
				} else {
					alert('The server responded: ' + response);
				}
				submit_button.show();
				progressbar.hide();
			}
			);
		return false;
	});

	// form
	$("#create_backup_zip").change(function(){
		var FTP = $("#ftp_backup");
		FTP.attr("disabled", ! $(this).is(':checked'));
		FTP.removeAttr("checked", ! $(this).is(':checked'));
	});

	// $("#cron_enabled").live('change', function(){
	// 	var cronDiv = $("#plancom_cron");
	// 	var elem = $(this);
	// 	cronDiv.find("input").each(function(){
	// 		$(this).attr('disabled', ! elem.is(':checked'));
	// 		$(this).removeAttr("checked", ! elem.is(':checked'));
	// 	});
	// });

});