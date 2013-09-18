$(function(){
	
	/**************************** Header */
	/**** Menu */
	if($(window).width() >= 800){
		slidemenu('.link-connect','#slide-connexion');
		slidemenu('#user-account','#slide-connexion');
		slidemenu('#user-badge','#slide-badge');
	} else { // Responsive
		slideclick('#menu', '.barnav .mainnav', '#slide-connexion', '#slide-badge');
		slideclick('#user-account, .link-connect', '#slide-connexion', '.barnav .mainnav', '#slide-badge');
		slideclick('#user-badge', '#slide-badge', '.barnav .mainnav', '#slide-connexion');
	};
	
	
	/**** Adserving */
	if( $('#ad-top, #ad-sidebar').children().length > 0 ) {
		$('#ad-top, #ad-sidebar').addClass('ad-padding');
	}
	
	
	/**************************** Page FAQ */
	 $('#faq h3').click(function(){
	 	var toslide = $(this).next();
	 	var answers = $('#faq .content-block');
	 	if($(toslide).is(':visible')){
			$(answers).slideUp();
		} else {
			$(answers).slideUp();
			$(toslide).slideDown();
		}
		return false;
	 });
    
    
    /**************************** Colonne droite */
	/**** Leaderboard */
	$('.ranking ul .general').hide();
	$('.ranking .filter-bar .general').click(function(){
		$(this).children().addClass('active');
		$('.ranking .filter-bar .week a').removeClass('active');
		$('.ranking ul .week').hide();
		$('.ranking ul .general').show();
		return false;
	})
	$('.ranking .filter-bar .week').click(function(){
		$(this).children().addClass('active');
		$('.ranking .filter-bar .general a').removeClass('active');
		$('.ranking ul .general').hide();
		$('.ranking ul .week').show();
		return false;
	})
	
	
    /**************************** Page Gagnant */
   	/**** Slider */
    $('#winnerslider img').css('width', '568px');
   	$('#winnerslider img').css('height', 'auto');
    sliderphoto('#winnerslider');    
   	$('#winnerslider .nivo-main-image').css('width', '568px');
   	
   	
   	/**************************** Page Classement */
  	/**** Filters */
	$('#filter-leaderboard').change(function () {
        var url = $(this).val();
        if (url != '') {
            window.location = url;
        }
        return false;
    });
  
    var urloption = window.location.pathname;
	$("#filter-leaderboard option").each(function() {
	    var value = $(this).val();
	    if(urloption === value) {
	    	if($(this).text() != "Sélectionner"){
	    		$(this).attr('selected','selected');
	       		$('#uniform-filter-leaderboard span').text($(this).html());
	    	}
	    	
	    };
	});


    /**************************** Custom style Form */
    $("select, input[type=radio], input[type=checkbox]").uniform();
    
    var dataprofilfile = $('#avatarfile').attr('data-profilfile');
    $('#avatarfile input').attr('size','33');
    $("#avatarfile input[type=file]").uniform({
		fileButtonHtml: 'Parcourir...',
		fileDefaultHtml: dataprofilfile
	});
	
	/**** Sliders par défaut */
	$('.default-slider img').css('width', '568px');
   	$('.default-slider img').css('height', 'auto');
    sliderphoto('.default-slider');    
   	$('.default-slider .nivo-main-image').css('width', '568px');
   	

	/*
	$.each($('.preview-img'),function(){
		if($(this).find('.preview')){
			var valueOK = $(this).find('.preview').attr('data-value');
			$(this).next().next().find('.filename').text(valueOK);
		}
	});
	*/
	
	
	/**************************** Arrondi IE */
	/*if (window.PIE) {
        $('.rounded, .backgrey, .btn, .block-game .dark-overlay, .sidebar .tonnes-cadeaux, .sidebar .newsletter, .ranking .week a, .ranking .general a, input[type="text"], input[type="password"]').each(function() {
            PIE.attach(this);
        });
    };*/
	
});