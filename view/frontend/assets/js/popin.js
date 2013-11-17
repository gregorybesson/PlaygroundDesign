$(function(){
	$('#container').prepend('<div id="overlay"></div><div id="wrap-popin"><div id="close"></div><div id="popin"></div></div>');
	$('#reglement a').click(function(){
		showpopin($(this));
		return false;
	});
	
	

	
});

function showpopin(link){
	$('#popin').load(link.attr('href')+' #terms',function(){
		$('#popin #terms').addClass('scroll-pane');
		
		$('.fb-target a').each(function() {
			$(this).attr('target', '_blank');
		});
	
		$('#overlay').fadeIn();
		$('#wrap-popin').fadeIn();
		//$('.scroll-pane').jScrollPane();
		$('.scroll-pane').each(function(){
			$(this).jScrollPane({
				showArrows: $(this).is('.arrow')
			});
			var api = $(this).data('jsp');
			var throttleTimeout;
			$(window).bind('resize', function(){
				if ($.browser.msie) {
					// IE fires multiple resize events while you are dragging the browser window which
					// causes it to crash if you try to update the scrollpane on every one. So we need
					// to throttle it to fire a maximum of once every 50 milliseconds...
					if (!throttleTimeout) {
						throttleTimeout = setTimeout(function(){
							api.reinitialise();
							throttleTimeout = null;
						}, 50);
					}
				} else {
					api.reinitialise();
				}
			});
		})
	});
	
	$('#close, #overlay').click(function(){
		$('#wrap-popin').fadeOut();
		$('#overlay').fadeOut();
	})
}
