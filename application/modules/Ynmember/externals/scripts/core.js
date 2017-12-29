var ynmember = {
	like_review: function (review_id, type, id, comment_id) {
	    en4.core.request.send(new Request.JSON({
		      url : en4.core.baseUrl + 'core/comment/like',
		      data : {
		        format : 'json',
		        type : type,
		        id : id,
		        comment_id : comment_id
		      }
		    }), {
		      'element' : $('comments-'+review_id)
		});
	},
	unlike_review: function (review_id, type, id, comment_id) {
	    en4.core.request.send(new Request.JSON({
	        url : en4.core.baseUrl + 'core/comment/unlike',
	        data : {
	          format : 'json',
	          type : type,
	          id : id,
	          comment_id : comment_id
	        }
	      }), {
	        'element' : $('comments-'+review_id)
	      });
	},
	set_useful: function (review_id, value, inline)
	{
		en4.core.request.send(new Request.HTML({
	        url : en4.core.baseUrl + 'adv-members/review/useful',
	        data : {
	          review_id : review_id,
	          value: value,
	          inline: inline,
	        }
	      }), {
	        'element' : $('ynmember_useful_'+review_id)
	      });
		console.log($('ynmember_useful_'+review_id));
	}
};