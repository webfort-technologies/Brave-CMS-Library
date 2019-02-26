
    	$(document).ready(function()
    	{
	    	$(".brave-uploader").change(function()
	    	{
	    		var elementId = $(this).attr("id");
	    		console.log(elementId); 
	    		if ($(this)[0].files && $(this)[0].files[0]) {
	                var reader = new FileReader();
	                var element = $(this);
	                reader.onloadend = function (e) {
	                	console.log(elementId); 
	                	console.log(reader.result);
					    $('#'+elementId+"_placeholder").attr("src",reader.result); 
	                };
	                reader.readAsDataURL($(this)[0].files[0]);
	            }
	    	}); // Change ends here.
    	}); // Document Ready Ends