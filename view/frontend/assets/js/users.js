$(function(){
	
	/**************************** Login */
	$("form#header-login").submit(function(event){
    	event.preventDefault();
        var $form = $(this);
        var inputs = $form.find("input, button");
        $(".input-login-error").text("");
        var datas = $form.serialize();
        inputs.prop("disabled", true);

        var request = $.ajax({
            url:  $(this).attr('action'),
            type: $(this).attr('method'),
            data: datas,
            dataType: 'json',
        }); 

        request.done(function (response, textStatus, jqXHR){
            if(response.success){
            	location.reload();
            } else{
                $(".input-login-error").text("Le nom d'utilisateur ou le mot de passe saisi est incorrect.");
            }
        });

        request.fail(function (jqXHR, textStatus, errorThrown){
            console.error("The following error occured: "+ textStatus, errorThrown);
        });

        request.always(function () {
            inputs.prop("disabled", false);
        });
        return false;
    });
    
    $("form#register-login").submit(function(event){
    	event.preventDefault();
        var $form = $(this);
        var inputs = $form.find("input, button");
        $(".input-login-error").text("");
        var datas = $form.serialize();
        inputs.prop("disabled", true);

        var request = $.ajax({
            url:  $(this).attr('action'),
            type: $(this).attr('method'),
            data: datas,
            dataType: 'json',
        }); 

        request.done(function (response, textStatus, jqXHR){
            if(response.success){
            	window.location = $('#login-part').attr('data-redirect');
            	//location.reload();
            } else{
                $(".input-login-error").text("Le nom d'utilisateur ou le mot de passe saisi est incorrect.");
            }
        });

        request.fail(function (jqXHR, textStatus, errorThrown){
            console.error("The following error occured: "+ textStatus, errorThrown);
        });

        request.always(function () {
            inputs.prop("disabled", false);
        });
        return false;
    });
    
    /**************************** Register */
	$('.update-login-block .validate').find('[name="newCredential"]').attr('id','password');
	$('.update-login-block .validate').find('[name="newCredential"]').addClass('security');
	$('.update-login-block .validate').find('[name="newCredentialVerify"]').attr('id','passwordVerify');
	$('.update-login-block .validate').find('[name="newCredentialVerify"]').attr('equalTo','#password');
	$('#register-form #passwordVerify').attr('equalTo','#password');
	$('#register-form #postalcode').attr('minlength',5);
	
	$('.validate').each(function () {
	    $(this).validate();
	});
	
	jQuery.validator.addMethod('security', function(value, element) {
		//min 6
		return this.optional(element) || /(?=.*[a-z]).{6,20}/.test(value);
	}, 'Niveau de sécrurité : Faible');  
	
	$('#password').keyup(function(){
        if($(this).val().match(/(?=.*[a-z]).{6,20}/)){
           setTimeout(function(){
            	 $('#password').parent().addClass('valid-form');
           });
        }
        else{
        	setTimeout(function(){
           		 $('#password').parent().removeClass('valid-form');
           });
        }
    });    
	
	$('#passwordVerify').keyup(function(){
		if($('#password').val() == $('#passwordVerify').val()){
			setTimeout(function(){
				$('#passwordVerify').parent().addClass('valid-form');
			});
		}else{
			setTimeout(function(){
				$('#passwordVerify').parent().removeClass('valid-form');
			});
		}
	});
	
	$('.validate input').keyup(function(){
		$(this).parent().removeClass('valid-form');
		if($(this).hasClass('valid')){
			$(this).parent().addClass('valid-form');
		}
		else{
			$(this).parent().removeClass('valid-form');
		}
		if($(this).val()== ''){
			$(this).parent().removeClass('valid-form');
		}
	});
	
	/**************************** Profile */
	/**** Form */
	jQuery.validator.addMethod('phonefr', function(value, element) {
		return this.optional(element) || /^(01|02|03|04|05|06|08)[0-9]{8}/.test(value);
	}, 'Le numéro n\'est pas valide.');
	$('#update-adresse .phone input').keyup(function(){
        if($(this).val().match(/^(01|02|03|04|05|06|08)[0-9]{8}/)){
           setTimeout(function(){
            	 $('#update-adresse .phone input').parent().addClass('valid-form');
           });
        }
        else{
        	setTimeout(function(){
           		 $('#update-adresse .phone input').parent().removeClass('valid-form');
           });
        }
    });
    
    jQuery.validator.addMethod('zipcodefr', function(value, element) {
		return this.optional(element) || /^(([0-8][0-9])|(9[0-5]))[0-9]{3}$/.test(value);
	}, 'Le numéro n\'est pas valide.');
	$('#update-adresse .zipcode input').keyup(function(){
        if($(this).val().match(/^(01|02|03|04|05|06|08)[0-9]{8}/)){
           setTimeout(function(){
            	 $('#update-adresse .zipcode input').parent().addClass('valid-form');
           });
        }
        else{
        	setTimeout(function(){
           		 $('#update-adresse .zipcode input').parent().removeClass('valid-form');
           });
        }
    });
	
	//delete account confirmation
	fade('#delete-account #del-confirm','#delete-account #del-input', '#delete-account #del-cancel');

	//success sending password (lostpass page)
	fade('#lost-submit','#response');
	
	/**** Form validation */
    checkFormError('#update-adresse');
    checkFormError('#update-login');
    //checkPointsCoordonnees('input', 'username');
    checkPointsCoordonnees('file', 'avatar');
    checkPointsCoordonnees('input', 'lastname');
    checkPointsCoordonnees('input', 'firstname');
    checkPointsCoordonnees('input', 'address');
    checkPointsCoordonnees('input', 'postal_code');
    checkPointsCoordonnees('input', 'city');
    checkPointsCoordonnees('input', 'telephone');
    checkPointsCoordonnees('select', 'birth_year');
    checkPointsCoordonnees('select', 'children');
    checkPointsCoordonnees('select', 'contact-question');    
    checkPointsCoordonnees('input', 'optin');
    checkPointsCoordonneesValid();
    checkPointsHobbyValid();
	
	
	/**************************** Module Newsletter */
	$("form#ajax-newsletter").submit(function(event){
    	event.preventDefault();
        var $form = $(this);
        var inputs = $form.find("input, button");
        $(".input-login-error").text("");
        var datas = $form.serialize();
        inputs.prop("disabled", true);

        var request = $.ajax({
            url:  $(this).attr('action'),
            type: $(this).attr('method'),
            data: datas,
            dataType: 'json',
        }); 

        request.done(function (response, textStatus, jqXHR){
            if(response.success){
            	$('#ajax-newsletter .btn').hide();
            	$('.bounce-newsform').hide();
           		$('.newsletter-connect .suscribe-success').fadeIn();
           		$('.bounce-newssuccess').fadeIn();
            } else{
                //$(".input-login-error").text("Le nom d'utilisateur ou le mot de passe saisi est incorrect.");
            }
        });

        request.fail(function (jqXHR, textStatus, errorThrown){
            console.error("The following error occured: "+ textStatus, errorThrown);
        });

        request.always(function () {
            inputs.prop("disabled", false);
        });
        return false;
    });
    
  	
  	/**************************** Activity */
  	/**** Filters */
	$('#filter-activity').change(function () {
        var url = $(this).val();
        if (url != '') {
            window.location = url;
        }
        return false;
    });
  
    var urloption = window.location.pathname;
	$("#filter-activity option").each(function() {
	    var value = $(this).val();
	    if(urloption === value) {
	    	if($(this).text() != "Vue d'ensemble"){
	    		$(this).attr('selected','selected');
	       		$('#uniform-filter-activity span').text($(this).html());
	    	}
	    	
	    };
	});
	
	/**** load more activity */
	$('#more-activity').click(function(){
		 var url = $(this).find('a').attr('href');
		 var incr = parseInt($(this).find('a').attr('data-incr'));
		 var total = parseInt($(this).find('a').attr('data-total'));
		 var viewTotal = $('.date').size();
		 
		 if(total > viewTotal){
			 $(this).prev().append('<div class="load-activity"></div>');
			 $('.load-activity:last').load(url+incr+' #list-activity' , function(){
			 	$(this).find('#list-activity').removeAttr('id');
			 	$('#more-activity a').attr('data-incr',incr+1);
			 });
		 }
		 else {
		 	$(this).fadeOut();
		 }
		 return false;
	});
	
    
    /**** REGISTER FORM LIVE CHECK */
   	/*
    checkLiveFormError('input', 'lastname', 'Veuillez renseigner votre nom.', 'btn-create-account');
    checkLiveFormError('input', 'firstname', 'Veuillez renseigner votre prénom.', 'btn-create-account');
    checkLiveFormError('input', 'email', 'Veuillez renseigner votre email.', 'btn-create-account');
    checkLiveFormError('input', 'password', 'Veuillez renseigner un mot de passe.', 'btn-create-account');
    checkLiveFormError('password', 'passwordVerify', 'Veuillez confirmez votre mot de passe.', 'btn-create-account', 'password');
    checkLiveFormError('select', 'birth_year', 'Veuillez renseigner votre année de naissance.', 'btn-create-account');
    checkLiveFormError('input', 'postal_code', 'Veuillez renseigner votre code postal.', 'btn-create-account');
	*/
    
});