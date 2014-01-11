jQuery(document).ready(function($){

	// jQuery UI

	// tabs
	$(function() {
		$( ".tabs" ).tabs({
			beforeLoad: function( event, ui ) {
				ui.jqXHR.error(function() {
					ui.panel.html( "Couldn't load this tab. We'll try to fix this as soon as possible." );
				});
			}
		});
	});
	// progress bar
	$( ".indeterminate-progressbar" ).hide().progressbar({
		value: false
	});
	// spinner
	$( "#spinner" ).spinner({
		spin: function( event, ui ) {
			if ( ui.value > 100 ) {
				$( this ).spinner( "value", 1 );
				return false;
			} else if ( ui.value < 1 ) {
				$( this ).spinner( "value", 100 );
				return false;
			}
		}
	});
	//menu
	$( ".menu" ).menu();

	 $( ".accordion" ).accordion({
      collapsible: true,
      active: false
    });
	
});