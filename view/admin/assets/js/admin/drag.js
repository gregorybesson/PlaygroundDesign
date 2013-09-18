// TODO update position with one post, not with a 'each'
  $(function() {	
	   $("#sort-table").sortable({
      update: function(event, ui) {
      	
  		var i = 1;
  		
  		$.each($(this).find('tr'),function(){
  			var url = $(this).attr('data-url');
  			var active = $(this).attr('data-active');
  			
  			$.post(
			    url,
    	    	{isActive:active,position:i},
    	    	function(returnVal) {
    	    	    // TODO
    	    	}
	    	 );
  			
  			i++;
  		});

      }
   });
  $( ".sort-table" ).disableSelection();

	    
  });