$(function(){

	/**************************** Homepage */
	
	var APP_ID =  $('meta[property="fb:app"]').attr('content');
	var BIT_CLIENT = $('meta[property="bt:client"]').attr('content');
	var BIT_USER = $('meta[property="bt:user"]').attr('content');
	var BIT_KEY = $('meta[property="bt:key"]').attr('content');
	
	
	/**** Result variables */
	var dataUrl 		= $('#datas-result').attr('data-url');
	var dataRoute 		= $('#datas-result').attr('data-route');
	var dataSecretKey 	= $('#datas-result').attr('data-secretkey');
	var dataFbMsg 		= $('#datas-result').attr('data-fbmsg');
	var dataTwMsg 		= $('#datas-result').attr('data-twmsg');
	var imgFb 			= $('meta[property="og:image"]').attr('content');
	var dataFbRequest 	= $('#datas-result').attr('data-fbrequest');
	var dataSocialLink 	= $('#datas-result').attr('data-sociallink');
	
	
	//$.getScript("http://platform.twitter.com/widgets.js");
	
	
	/**** Facebook init */
	window.fbAsyncInit = function() {
	  // init the FB JS SDK
	  FB.init({
	    appId      : APP_ID, // App ID from the App Dashboard
	    status     : true, // check the login status upon init?
	    cookie     : true, // set sessions cookies to allow your server to access the session?
	    xfbml      : true  // parse XFBML tags on this page?
	  });
	  FB.Canvas.setAutoGrow();
	  FB.Canvas.getPageInfo(function (pageInfo)
	  {
         $({y: pageInfo.scrollTop}).animate(
            {y: 0},
            {
                duration: 0,
                step: function (offset)
                {
                    FB.Canvas.scrollTo(0, offset);
                }
            }
         );
	  });
	};
	
	
	/**** Shorturl Twitter */
	$('.shorturl').mouseenter(function(){
		var txt = $(this).attr('data-txt');
		var url = $(this).attr('data-url');
		
		var shorturl = '';
	    $.getJSON(
	        BIT_CLIENT+"?", 
	        { 
	        	"login": BIT_USER,
	        	"apiKey": BIT_KEY,
	        	"longUrl": url,
	            "format": "json"
	        },
	        function(response)
	        {
	            shorturl = encodeURIComponent(response.data.url);
	            $('.shorturl').attr('href','https://twitter.com/intent/tweet?text='+txt+'&url='+shorturl);
	        }
	    );
	});

	
	/**** Social share */
    $(".grey-fb,.dark-fb").click(function(event){
    	event.preventDefault();
    	var link = $(this).attr('data-link');
    	var picture = $(this).attr('data-picture');
    	var caption = $(this).attr('data-caption');
    	var description = link;
    	displayFBUI(link,picture,caption,description);
	});
	
	
	/**** Facebook share - Result page */
	$("#fb-share").click(function(event){
    	event.preventDefault();
		var data=
		{
			method: 'feed',
			message: "",
			display: 'iframe',
			name: "Plateforme Playground",
			caption: dataFbMsg,
			description: dataUrl,
			picture: imgFb,
			link: dataSocialLink,
			//actions: [{ name: 'action_links text!', link: 'http://www.google.com' }],
		};
		FB.getLoginStatus(function(response) {
		      if (response.status === 'connected') {
		          //If you want the user's Facebook ID or their access token, this is how you get them.
		          var uid = response.authResponse.userID;
		          var access_token = response.authResponse.accessToken;
		          FB.ui(data, onFbPostCompleted);
		      } else {
		          //If they haven't, call the FB.login method
		          FB.login(function(response) {
		              if (response.authResponse) {
		                  //If you want the user's Facebook ID or their access token, this is how you get them.
		                  var uid = response.authResponse.userID;
		                  var access_token = response.authResponse.accessToken;
		                  FB.ui(data, onFbPostCompleted);
		              } else {
		                  //alert("You must install the application to share your greeting.");
		              }
		          }, {scope: 'publish_stream'});
		      }
		  });
	});
	
	/**** Facebook invit - Bounce page */
	$("#fb-request").click(function(event){
    	event.preventDefault();
		var data=
		{
			method: 'apprequests',
			message: dataFbRequest,
		}
		FB.getLoginStatus(function(response) {
			if (response.status === 'connected') {
		    	//If you want the user's Facebook ID or their access token, this is how you get them.
		        var uid = response.authResponse.userID;
		        var access_token = response.authResponse.accessToken;
		        FB.ui(data, onFbRequestCompleted);
		    } else {
		        //If they haven't, call the FB.login method
		        FB.login(function(response) {
		        	if (response.authResponse) {
		            	//If you want the user's Facebook ID or their access token, this is how you get them.
		                var uid = response.authResponse.userID;
		                var access_token = response.authResponse.accessToken;
		                FB.ui(data, onFbRequestCompleted);
		            } else {
		                //alert("You must install the application to share your greeting.");
		            }
		        }, {scope: 'publish_stream'});
		    }
		});
	});
	
	var oneshare = true;
	
	$("#google-plus").click(function(e){
		e.preventDefault();
		var gplus = window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=550');
		
		if(oneshare){
			if(dataRoute){
				var url = dataRoute;
			}
			else{
				var url = dataUrl;
			}
			var timer = setInterval(function() {   
			    if(gplus.closed) {  
			        clearInterval(timer); 
			        var request = $.ajax({
			            url: url + '/google?googleId=' + dataSecretKey,
			            type: 'GET',
			        }); 
			        oneshare = false;
			    }  
			}, 1000); 
		}
	});
	
	
	/**** Facebook */
	function displayFBUI(link,picture,caption,description) {
		FB.ui({
	        method: 'feed',
		    name: 'Playground',
		    link: link,
		    picture: picture,
		    caption: caption,
		    description: description
    	});
	}

	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	
	// Load the SDK's source Asynchronously
	// Note that the debug version is being actively developed and might
	// contain some type checks that are overly strict.
	// Please report such bugs using the bugs tool.
	//(function(d, debug){
	//	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	//    if (d.getElementById(id)) {return;}
	//    js = d.createElement('script'); js.id = id; js.async = true;
	//    js.src = "//connect.facebook.net/fr_FR/all" + (debug ? "/debug" : "") + ".js";
	//    ref.parentNode.insertBefore(js, ref);
	//}(document, /*debug*/ false));
	
	function onFbPostCompleted(response){
		if (response){
			//console.log(response);
			if (!response.error){
				if (response.id || response.post_id){
					if(dataRoute){
						var url = dataRoute;
					}
					else{
						var url = dataUrl;
					}
					var request = $.ajax({
			            url: url + '/fbshare?fbId=' + dataSecretKey,
			            type: 'GET',
			        });
				}
			}
		}
		// user cancelled
	}
	
	function onFbRequestCompleted(response) {
		if (response) {
			//console.log(response);
			if (response.error) {
				alert(response.error.message);
			}
			else {
				if (response.request)
					var request = $.ajax({
			            url: dataUrl + '/fbrequest?fbId=' + dataSecretKey,
			            type: 'GET',
			        });
				else
					alert(JSON.stringify(response));
			}
		}
		// user cancelled
	}
	
	
	/**** Google+ */
	function onGooglePlus(response){
		if (response){
			//console.log(response);
			if (!response.error){
				if (response.state){
					var request = $.ajax({
			            url: dataUrl + '/google?googleId=' + dataSecretKey,
			            type: 'GET',
			        });
			        //alert('ok');
				}
			}
		}
		// user cancelled
	}
	
	(function() {
	    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	    po.src = 'https://apis.google.com/js/plusone.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();
	
	/**** Twitter */
	$.getScript("http://platform.twitter.com/widgets.js", function(){
		function handleTweetEvent(event){
	    	if (event) {
	    		if(dataRoute){
					var url = dataRoute;
				}
				else{
					var url = dataUrl;
				}
	    		var request = $.ajax({
	            	url: url + '/tweet?tweetId=' + dataSecretKey,
	            	type: 'GET',
	         	});
	         	
	     	}
	   	}
	   	twttr.events.bind('tweet', handleTweetEvent);
	});
	
});