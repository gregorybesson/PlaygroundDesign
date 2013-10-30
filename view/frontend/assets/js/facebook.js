$(document).ready(function(){
	setTimeout(function(){
		canvasScroll(0);
	},3000);
});

function popauth()
{
    /* Get the status of the user */
    FB.getLoginStatus(function (response)
    {
        if (response.status === 'connected') {
            console.log("Well done! You can use the application now");

                FB.api('/me', function(response) {
                
                });

        } else if (response.status === 'not_authorized') {
            fbLogin();
        } else {
            fbLogin();
        }
    });
}

/* Find if the user is logged in or not */
function fbLogin ()
{
    FB.login(function (response)
    {
        if (response.authResponse) {
            console.log("login success");
        } else {
            console.log("login fail");
        }
    }, {scope: 'email, user_likes, publish_stream'});
}

/* scroll to top */
function canvasScroll(){
	FB.Canvas.getPageInfo(function (pageInfo)
    {
        $({y: pageInfo.scrollTop}).animate(
            {y: y},
            {
                duration: 0,
                step: function (offset)
                {
                    FB.Canvas.scrollTo(0, offset);
                }
            }
        );
    });
}