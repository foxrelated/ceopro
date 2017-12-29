var ynmultilisting = {
	set_useful: function (review_id, value, inline)
	{
		 var request = new Request.HTML({
	        url : en4.core.baseUrl + 'multi-listing/review/useful',
	        data : {
	          review_id : review_id,
	          value: value,
	          inline: inline
	        },
	        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $$('.ynmultilisting_useful_'+ review_id).each(function(el) {
                	el.innerHTML = responseHTML;
                });
           }
	      });
	      request.send();
	}
};