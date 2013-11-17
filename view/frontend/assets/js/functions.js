function getBaseURL() {
    var url = location.href;  // entire url including querystring - also: window.location.href;
    var baseURL = url.substring(0, url.indexOf('/', 14));

    if (baseURL.indexOf('http://localhost') != -1) {
        // Base Url for localhost
        var url = location.href;  // window.location.href;
        var pathname = location.pathname;  // window.location.pathname;
        var index1 = url.indexOf(pathname);
        var index2 = url.indexOf("/", index1 + 1);
        var baseLocalUrl = url.substr(0, index2);

        return baseLocalUrl + "/";
    }
    else {
        // Root Url for domain name
        return baseURL + "/";
    }
    
}

function slidemenu(binder,binded){
	$(binder).hover(function() {
	    clearTimeout($(this).data('mouseovertimer'));
	    clearTimeout($(binded).data('mouseovertimer'));
	    $(binded).slideDown('normal');
	}, function() {
	    var $this = $(this);
	    $this.data('mouseovertimer', setTimeout(function(){
	        $(binded).slideUp('normal');
	    }, 700));
	});
	$(binded).hover(function() {
	    clearTimeout($(binder).data('mouseovertimer'));
	}, function(){
	    var $this = $(this);
	    if($(binded+' input').is(':focus')){
	    	$this.data('mouseovertimer', setTimeout(function(){
		        $this.slideUp('normal');
		    }, 50000));
	    } else {
		    $this.data('mouseovertimer', setTimeout(function(){
		        $this.slideUp('normal');
		    }, 700));
		}
	});
}

function slideup(binder,binded){
	$(binder).mouseout(function(){
		$(binded).slideUp();
		return false;
	});
}

function fade(binder, binded1, binded2){
	if(typeof binded2 == undefined){binded2 = 0}
    $(binded1).css({display: 'none'});
    if(binded2 != 0){
        $(binded2).css({display: 'none'});
    }
    $(binder).click(function(){
		$(binder).hide();
        $(binded1).fadeIn().css('display','inline-block');
        if(binded2 != 0){
		    $(binded2).fadeIn().css('display','inline-block');
        }
		return false;
	});
}

function slideclick(binder,binded,firstSlide,secondSlide){
	$(binder).click(function(){
		$(firstSlide).slideUp();
		$(secondSlide).slideUp();
		$(binded).slideToggle();
		return false;
	});
}

function sliderphoto(sliderid){
	$(sliderid).nivoSlider({
        effect: 'fade', // Specify sets like: 'fold,fade,sliceDown'
        slices: 15, // For slice animations
        boxCols: 8, // For box animations
        boxRows: 4, // For box animations
        animSpeed: 500, // Slide transition speed
        pauseTime: 3000, // How long each slide will show
        startSlide: 0, // Set starting Slide (0 index)
        directionNav: true, // Next & Prev navigation
        controlNav: false, // 1,2,3... navigation
        controlNavThumbs: false, // Use thumbnails for Control Nav
        pauseOnHover: true, // Stop animation while hovering
        manualAdvance: false, // Force manual transitions
        prevText: 'Prev', // Prev directionNav text
        nextText: 'Next', // Next directionNav text
        randomStart: false, // Start on a random slide
        beforeChange: function(){}, // Triggers before a slide transition
        afterChange: function(){}, // Triggers after a slide transition
        slideshowEnd: function(){}, // Triggers after all slides have been shown
        lastSlide: function(){}, // Triggers when last slide is shown
        afterLoad: function(){} // Triggers when slider has loaded
    });
}

function postvotecount(buttonlink, buttonpostcheck, postnumber, votesnumber, buttonpost, button, votesnumberlink, alreadyvoted){
	$(buttonlink).click(function(e){
		var target = $(this);
        if(target.hasClass(buttonpostcheck)){
        	
        }else{
        	e.preventDefault();
            var nb_vote_number = parseInt($(this).parent().find(postnumber).text());
            
            var request = $.ajax({
                url:  target.attr('href'),
                type: 'POST',
                data: '',
                dataType: 'json',
            }); 

            request.done(function (response, textStatus, jqXHR){
            	
                if(response.success){
                	nb_vote_number++;
                	target.parent().find(postnumber).text(nb_vote_number);
                	target.parent().removeClass(button).addClass(votesnumber);
                	target.removeClass(buttonpost).addClass(buttonpostcheck).css({cursor: 'default'});
                } else{
                    //$(".input-login-error").text("Le nom d'utilisateur ou le mot de passe saisi est incorrect.");
                    target.parent().find(alreadyvoted).css({display: 'block'});
                }
            });

            request.fail(function (jqXHR, textStatus, errorThrown){
                console.error("The following error occured: "+ textStatus, errorThrown);
            });
        }
        return false;
    });
}

function checkFormError(form){
    var first_error = true;
    $(form + ' ul li').each(function(){
        $(this).addClass('red');
        $(this).parent().parent().parent().find('p').addClass('red');
        if(first_error){
            $('html,body').animate({
                    scrollTop: $(this).parent().parent().parent().parent().offset().top},
                'slow');
            first_error = false;
        }
    });
}

function checkPointsCoordonnees(type, selector){
    switch (type) {
        case ('input') :
            if($('input[name="'+selector+'"]').val() !== ''){
                $('input[name="'+selector+'"]').parent().next().children('p').remove();
            }
        break;
        case ('file') :
        	var avatarimg = $('#sidebar-user .avatar img').attr('src');
        	var avatarf = $('#sidebar-user .avatar').attr('data-noavatarf');
        	var avatarh = $('#sidebar-user .avatar').attr('data-noavatarh');
            if( (avatarimg !== avatarf) && (avatarimg !== avatarh) ) {
                $('input[name="'+selector+'"]').parent().parent().next().children('p').remove();
            }
        break;
        case ('select') :
            if($('select[name="'+selector+'"] option:selected').val() !== ''){
                $('select[name="'+selector+'"]').parent().parent().next().children('p').remove();
            }
        break;
    }
}

function checkPointsCoordonneesValid(){
    if($('#update-adresse .orange-points').length === 0){
        $('#motto-gagner-points-coordonnees').hide();
    }
}

function checkPointsHobbyValid() {
    if($('#update-hobby .hobbies input').is(':checked')){
    	$('#motto-gagner-points-hobby').hide();
    }
}

function isFormValid(btn) {
    if($('body').find('.check-error').length > 0) {
        $('#'+btn).find('button[type="submit"]').attr('type', 'button');
    }else{
        $('#'+btn).find('button[type="button"]').attr('type', 'submit');
    }
}