$(function(){
	
	/**************************** Homepage - Slider */
    $('.carousel').carousel({
        pause: ""
    });
    
    $('.carousel-inner .item:first').addClass('active');
    
    
    /**************************** Facebook : target:_blank */
	$('.fb-target a').each(function() {
		$(this).attr('target', '_blank');
	}); 
    
	/**************************** Game - Instant gagnant */
	/**** Grattage */
	if($(window).width() > 550){
		$("#wScratchgame").wScratchPad({
			width   : 211,
	    	height  : 166,
	    	size 	: 15,
	    	image2  : $('#wScratchgame').attr('data-scratchthis'),
	    	color 	: '#fff',
			overlay : 'none',
			firsttext 	: $('#wScratchgame div').attr('data-firsttxt'),
			middletext 	: $('#wScratchgame div').attr('data-middletxt'),
			lasttext 	: $('#wScratchgame div').attr('data-lasttxt'),
			classscratch : $('#wScratchgame div').attr('class'),
			scratchDown: function(e, percent){$("#wScratchgame").attr('data-percentscratched', percent)},
	        scratchMove: function(e, percent){$("#wScratchgame").attr('data-percentscratched', percent)},
	        scratchUp: function(e, percent){$("#wScratchgame").attr('data-percentscratched', percent)}
		});
	} else {
		$("#wScratchgame").wScratchPad({
			width   : 140,
	    	height  : 90,
	    	size 	: 10,
	    	image2  : $('#wScratchgame').attr('data-scratchthismobile'),
	    	color 	: '#fff',
			overlay : 'none',
			firsttext 	: $('#wScratchgame div').attr('data-firsttxt'),
			middletext 	: $('#wScratchgame div').attr('data-middletxt'),
			lasttext 	: $('#wScratchgame div').attr('data-lasttxt'),
			classscratch : $('#wScratchgame div').attr('class'),
			scratchDown: function(e, percent){$("#wScratchgame").attr('data-percentscratched', percent)},
	        scratchMove: function(e, percent){$("#wScratchgame").attr('data-percentscratched', percent)},
	        scratchUp: function(e, percent){$("#wScratchgame").attr('data-percentscratched', percent)}
		});
	};
	
	/**** Temps d'affichage du résulat */
	$('#wScratchgame canvas').mousedown(function(){
		$('#wScratchgame .scratchcontent').show();
	})
	
	/**** Navigation - Mécanique */
	$('#play-instantwin .btn a').bind('click', false);
	$("#wScratchgame canvas").mouseup(function(){
		var sp = $("#wScratchgame").attr('data-percentscratched');
		if(sp >= 10){
			$('#play-instantwin .btn').removeClass('btn-warning-inactive');
	    	$('#play-instantwin .btn').addClass('btn-warning');
	    	$('#play-instantwin .btn a').unbind('click', false);
	    	
	    	$('#play-instantwin .btn-warning.success').click(function(){
				$('#play-instantwin').hide();
				$('html, body').animate({ scrollTop: 0 }, 0);
				$('#result-instantwin').fadeIn();
				return false;
			});
	    }
	});
	
	
	/**************************** Game - Post & vote */
	$('.alert-link').click(function(){
		$(this).parent().submit();
		return false;
	});
	
    /**** Sliders Photo contest */
    var countslider = $('.nivoSlider').size();
	$.each($('.nivoSlider'),function(){
		 sliderphoto(this);
	});
	for(var i=0 ; i<=countslider ; i++){
        sliderphoto('#slider'+i);
    }
    
    /**** Style input file */
    $('#photokitchen-form input[type=file]').uniform({
		fileButtonHtml: 'Parcourir...',
		fileDefaultHtml: 'Photo'
	});
	
	$(".photo-file input[type=file]").uniform({
		fileButtonHtml: 'Parcourir...',
		fileDefaultHtml: 'Photo'
	});
	
	var photoFile = $('.game-postvote .photo-file').size();
	if(photoFile <= 1){
		$('.picto .star').hide();
		PostVoteInput('.game-postvote .photo-file input:file', '.game-postvote .photo-file .filename', '.game-postvote .photo-file .picto');
	} else {
		var i=1;
		$.each($('.game-postvote .photo-file'),function(){
			$(this).addClass('input'+i);
			PostVoteInput('.input'+i+' input:file', '.input'+i+' .filename', '.input'+i+' .picto');
			i++;
		});
	}
	
	function PostVoteInput(inputfile, filename, picto){
		var ivalue = $(inputfile).attr('value');
		if (ivalue != undefined){
			var isplitted = ivalue.split('/');
			var ilast = '';
			if (isplitted.length > 0) {
				ilast = isplitted[isplitted.length-1];
			}
			$(filename).html(ilast);
		}
		if ($(filename).html() == 'Photo'){
			$(picto).hide();
		}
	}
	
	
   	/**** Count characters form */
   	$('#photomsg').limiter('400','#counter-photomsg');
   	
   	$.each($('.form-textarea textarea'), function(){
   		var maxlength = $(this).attr('maxlength');
   		var charleft = $(this).parent().next().find('.character-left');
   		if(typeof maxlength !== 'undefined'){
   			charleft.text(maxlength);
   			charleft.parent().fadeIn();
   		}
   		$(this).limiter(maxlength, charleft);
   	});
   	
   	
   	/**** Vote Ajax */
    postvotecount('.nb-votes a.logged', 'btn-post-vote-check', '.nb-post-vote-number', 'nb-votes-check', 'btn-post-vote', 'nb-votes', 'nb-votes-check a', '.already-voted');
    
    /**** LIVE PHOTO UPLOAD */
    $('#photocontest-create-form input[type="file"]').change(function(){
        var filename = $(this).val();
        $('#uploadform').append('<input name="file" type="file" value="' + filename + '">');
        $('#uploadform-id').val($(this).parent().parent().find('.uploadphotoid').val());
        $('#uploadform').submit();
        $(this).hide();
    });
        
    
    /**************************** Game - Quizz */
    /**** Navigation - Mécanique */
	$('.game-quiz .page').first().addClass('active');
	$('.end').hide();
	if($('.page:first').hasClass('active')){
		$('.previous').hide();
	}
	if($('.page').last().hasClass('active')){
		$('.next').hide();
		$('.end').show();
	}
	$('#next').click(function() {
		var idfirst = $('.game-quiz .page.active').attr('id');
		$('#'+idfirst).removeClass('active');
		$('#'+idfirst).next('.page').addClass('active');
		if($('.page').last().hasClass('active')){
			$('.next').hide();
			$('.end').show();
		}
		$('.previous').show();
	});
	$('#previous').click(function() {
		var idfirst = $('.game-quiz .page.active').attr('id');
		$('#'+idfirst).removeClass('active');
		$('#'+idfirst).prev('.page').addClass('active');
		if($('.page:first').hasClass('active')){
			$('.previous').hide();
		}
		$('.next').show();
		$('.end').hide();
	});
	
	/**** Timer */
	$(document).ready(function () {
		var Timerquiz = new (function() {
		    var $countdown,
		        incrementTime = 70,
		        currentTime = parseInt($('.timer').text()),
		        updateTimer = function() {
		            $countdown.html(formatTime(currentTime));
		            if (currentTime == 0) {
		                Timerquiz.Timer.stop();
		                timerComplete();
		                return;
		            }
		            currentTime -= incrementTime / 10;
		            if (currentTime < 0) currentTime = 0;
		        },
		        timerComplete = function() {		            
		            alert('Le temps imparti est écoulé !');
		            // caution : the submit button in the form HAVE TO be different from "name"
		            // see : http://bugs.jquery.com/ticket/4652
		            $('form:first').submit();
		        },
		        init = function() {
		            $countdown = $('.timer');
		            Timerquiz.Timer = $.timer(updateTimer, incrementTime, true);
		        };
		    $(init);
		}); 
	});
	
	function pad(number, length) {
	    var str = '' + number;
	    while (str.length < length) {str = '0' + str;}
	    return str;
	}
	function formatTime(time) {
	    var min = parseInt(time / 6000),
	        sec = parseInt(time / 100) - (min * 60),
	        hundredths = pad(time - (sec * 100) - (min * 6000), 2);
	    return (min > 0 ? pad(min, 2) : "00") + ":" + pad(sec, 2) + ":" + hundredths;
	}
	
	/**************************** Commun : Envoi mail */
    $('.more-invit').click(function(){
    	$('#mail-send input').attr('value', '');
		$(this).parent().fadeOut(function(){
	  		$('#mail-send').fadeIn();
	  	});
  	});
	
});