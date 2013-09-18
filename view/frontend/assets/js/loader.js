$(function(){
	/************** LIVE PHOTO UPLOAD */
	$('#postForm input[type="file"]').change(function(e){
		e.preventDefault();
		var file = this.files[0];
	    var fd = new FormData();
	    var ajaxupload =  $('.photo-file:first').attr('data-url');
	    var canonical =  $('.photo-file:first').attr('data-canonical');
	    fd.append(this.name, file);
	
	    if(this.name != ''){
	        var request = $.ajax({
	           url: ajaxupload,
	           type: "POST",
	           data: fd,
	           xhr: function() {  
	               myXhr = $.ajaxSettings.xhr();
	               if(myXhr.upload){ 
	                   myXhr.upload.addEventListener('progress',startProgress, false); // for handling the progress of the upload
	               }
	               return myXhr;
	           },
	           processData: false,
	           contentType: false,
	        });
	        
	        /*
	        var progressBar = document.querySelector('progress');	
	        function startProgress(e){
	            if(e.lengthComputable){
	        	      progressBar.value = (e.loaded / e.total) * 100;
	        	      progressBar.textContent = progressBar.value;
	            }
	        };
	        */
	       
	       
	       
	       
	       
	       
	       
	        var progressBar = $(this).parent().prev();
	        var progressText = progressBar.find('.percent b');
	        
	        function startProgress(e){
	            if(e.lengthComputable){
	            	 var progressW = parseInt((e.loaded / e.total) * 100);
	            	 progressBar.fadeIn(function(){
	            	 	progressBar.find('.bar').css('width',progressW+'%');
	        	      	progressText.text(progressW+'%');
	            	 });    
	            }
	        };
	       
	        
	       
	        request.done(function (response, textStatus, jqXHR){
	        	var jsonResponse = jQuery.parseJSON(response);
	            if(jsonResponse.success){
	            	progressBar.fadeOut(function(){
	            		progressBar.prev().empty().prepend('<img class="preview" src="'+canonical+jsonResponse.fileUrl+'" alt=""/>');
	            	});
	            }
	        });
	        
	       
	       
	       
	       
	       
	       /* //WITHOUT UNIFORM
	       
	       var progressBar = $(this).parent().find('.wrap-progress');
	       var progressText = progressBar.find('.percent b');
	        
	        function startProgress(e){
	            if(e.lengthComputable){
	            	 var progressW = (e.loaded / e.total) * 100;
	            	 progressBar.fadeIn(function(){
	            	 	progressBar.find('.bar').css('width',progressW+'%');
	        	      	progressText.text(progressW+'%');
	            	 });    
	            }
	        };
	       
	
			request.done(function (response, textStatus, jqXHR){
	        	var jsonResponse = jQuery.parseJSON(response);
	            if(jsonResponse.success){
	            	//alert(jsonResponse.fileUrl);
	            	progressBar.fadeOut(function(){
	            		//progressBar.next().hide();
	            		progressBar.prev().empty().prepend('<img class="preview" src="'+canonical+jsonResponse.fileUrl+'" alt=""/>');
	            	});
	            }
	        });
	        */
			
			
			
	
	        request.fail(function (jqXHR, textStatus, errorThrown){
	            console.error("The following error occured: "+ textStatus, errorThrown);
	        });
	    }
	});
	
	// bind trash
	/*
	$('#postForm .trash').click(function(){
		var imgUp = $(this).parent().prev();
		var progressBar = $(this).parent().next();
			imgUp.fadeOut(function(){
				progressBar.fadeIn();
			});
		});
		*/
	
});