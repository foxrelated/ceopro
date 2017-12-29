en4.Ultimatenews = {
  isFullContent : function (obj){
		if(obj.value == 0){
			$('characters-wrapper').style.display = 'none';
		}			
		else{
			$('characters-wrapper').style.display = 'block';
		}
  },
  loadComments : function(type, id, page){
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'Ultimatenews/viewcomments',
      data : {
        format : 'html',
        type : type,
        id : id,
        page : page
      }
    }), {
      'element' : $('comments'+id)
    });
  },
  loadContents : function(category, page, limit){	  
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'Ultimatenews/loaddata',
      data : {
        format : 'html',       
        nextpage : page,
        category : category,
        limit : limit
      }
    }), {
      'element' : $('layout_Ultimatenews_tab_Ultimatenews')
    });
  }
}