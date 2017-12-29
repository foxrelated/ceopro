<?php
$staticBaseUrl = $this->layout()->staticBaseUrl;
$url = $this->baseUrl() . '?m=lite&module=ynadvsearch&name=ynadvsearch';
$this->headScript()->appendFile($staticBaseUrl . 'application/modules/Ynadvsearch/externals/scripts/jquery-1.7.1.min.js');
?>
<div id="mini_ynadvsearch">
<form id = "mini_ynadvsearch_form" class="search_form" action="<?php echo $this->url(array(),'ynadvsearch_search') ?>">
    <input id="search_mini_field" type="text" name="query">
    <a href="javascript:void(0)" onclick="$('mini_ynadvsearch_form').submit()"><i class="fa fa-search"></i></a>
</form>
</div>

<script type="text/javascript">
var pos3 = 0;
window.addEvent('domready', function() {
	var position = $('search_mini_field').getPosition();
	pos3 =position.x;
});

var ynadvsearch3 = {
    is_space: false,
    input: null,
    ed: function () {
        Asset.javascript( '<?php echo $this->baseUrl() ?>' + '/application/modules/Ynadvsearch/externals/scripts/ynadvsearch.js', {
            onLoad: ynadvsearch3.eu
        });
    },
    cleanCss: function(e) {
        var p = e.parentNode;
        while (p && p.style != null) {
           // p.style.overflow = 'visible';
            p = p.parentNode;
        }
    },
    init: function () {
        var e = ynadvsearch3.input = document.id('search_mini_field');
        if (e) {
            ynadvsearch3.cleanCss(e);
            ynadvsearch3.ed();
        }
    }
};
ynadvsearch3.eu = function(event){
	 var item_type='';
	 var maxre = <?php echo $this->maxRe; ?>;
	 $$(".tag-autosearch3").each(function(e){
        var doc = document.body;
        doc.removeChild(e);
     });
     var count3 = 0;
     var header3 = -1;
     var prev_label3 = "";
     
	 new YnAdsAutocompleter.RequestAdSearch.JSON('search_mini_field', '<?php echo $url ?>', {
		  	'postVar': 'search',
	        'className': 'tag-autosearch3',
	        'choicesMatch': 'li.autocompleter-choices',
	        'cache': false,
	        'tokenFormat' : 'object',
	        'tokenValueKey' : 'label',
	        'maxChoices': 200,
	        'width': 480,
	        'pos' : pos3,
	        'left': true,
	        'postData' : {
				'maxre' : <?php echo $this->maxRe; ?>,
				'static_base_url':'<?php echo base64_encode($this->url(array(), 'default', true));?>'

		    },
	        'injectChoice': function(token){
	        		// show group
	        		if(token.type != 'menu') {
                        if($$('.tag-autosearch3 .group-results')[header3])
                        {
                            if(item_type !=token.type && token.type != 'see_more_link' && token.type != 'no_result_found_link' && token.type != 'hidden_link'){
                                if(count3 == 1)
                                {
                                    var str_result = '<?php echo $this->translate('Result') ?>';
                                }
                                else
                                {
                                    var str_result = '<?php echo $this->translate('Results') ?>';
                                }
                                $$('.tag-autosearch3 .group-results')[header3].set('html',prev_label3 + " " +"<div class='total_result_item'>" + count3 +" "+ str_result + "</div>");
                                count3 = 0;
                            }
                        }
                    }
                    
	        		if(token.type != 'menu' && item_type !=token.type && token.type != 'see_more_link' && token.type != 'no_result_found_link' && token.type != 'hidden_link'){
                        item_type = token.type;
                        var group_li = new Element('li', {'class': 'group-results','html': token.type_label}).inject(this.choices);
                        prev_label3 = token.type_label;
                        header3++;
                    }
                    
                    if (item_type !=token.type && token.type != 'see_more_link' && token.type != 'no_result_found_link' && token.type != 'hidden_link'){
                        item_type = token.type;
                        var group_li = new Element('li', {'class': 'group-results'}).inject(this.choices);
                    }
                    
                    if(token.type=='see_more_link') {
                        if($$('.tag-autosearch3 .group-results')[header3])
                        {
                            if(count3 == 1)
                            {
                                var str_result = '<?php echo $this->translate('Result') ?>';
                            }
                            else
                            {
                                var str_result = '<?php echo $this->translate('Results') ?>';
                            }
                            $$('.tag-autosearch3 .group-results')[header3].set('html',prev_label3 + " " +"<div class='total_result_item'>" + count3 +" "+ str_result + "</div>");
                        }
                    }
                    
					if (token.type == "no_result_found_link") {
						var choice = new Element('li', {'class': 'autocompleter-choices-nophoto', 'id':'noresult'});
					}
					else {
		  				var choice = new Element('li', {'class': 'autocompleter-choices', 'id':token.label});
					}
		  			var choice_span = new Element('div', {'class': 'result-info'}).inject(choice);
		  			var choice_a = new Element('a', {
		            	'class': 'autocompleter-choices-a',
		            	'href':  token.url,
		            	'html': token.label
		            }).inject(choice_span);
		            // photo
		  			if(typeof(token.photo)){
		  				choice_a.set('html',token.photo);
		            }
		            //label
		            var choice_label = new Element('span',{'html': token.label}).inject(choice_a);
		  			if(typeof(token.type) && (token.type=='see_more_link' || token.type == 'hidden_link')){
		  				if (token.type=='see_more_link') {
		  				    choice.addClass('see_more_link');
		  				}
		  				else {
		  				    choice.addClass('hidden_link');
			  			}
	        			new Element('span',{'class':'arrow'}).inject(choice_label);
	        			new Element('span',{
		        			'class':'ynserach_sub_text',
		        			'html': token.label2
			        	}).inject(choice)
		        	}
		        	// normal result
		  			else if (token.type !='no_result_found_link' && token.type!='menu'){
			  			//category
			            var choice_category= new Element('span',{'html': token.type_label,'class':'type'}).inject(choice_a);
			  		    count3 ++; 
			  		}
		  			this.addChoiceEvents(choice).inject(this.choices);
	                choice.store('autocompleteChoice', token);
	  		}, //injectChoice
	  		onChoiceSelect: function(choice){
		  		if (choice) {
    				var a_tag = choice.getElements('a');
    				if (a_tag[0]) {
    				    var url = a_tag[0].getProperty('href');
    				    document.location = url;
    				}
		  		}
		  	},
		  	onCommand: function(e){
                
                if(e.key == 'space')
                {
                	 ynadvsearch3.is_space = true;
                }
                else
                {
                    ynadvsearch3.is_space = false;
                }
           },
		  	onRequest: function(){
		  	    prev_label3 = "" ;
                count3 = 0;
                header3 = -1;
		  		this.cached = [];
		  		var re = /\s*((\S+\s*)*)/;
				var trimquery = document.id('search_mini_field').value;
				var l=0; var r=trimquery.length -1;
				while(l < trimquery.length && trimquery[l] == ' ')
				{	l++; }
				trimquery = trimquery.substring(l);
				if (trimquery && ynadvsearch3.is_space == false)
				{
			  		document.id('search_mini_field').setStyle('background', "url('application/modules/Ynadvsearch/externals/images/ajax-loader.gif') no-repeat right ");
				}

			},
			onComplete: function(response){
				document.id('search_mini_field').setStyle('background', '');
				item_type = '';
			}
	    });

};
<?php if (!$this->isMobile) : ?>
    window.addEvent('domready', ynadvsearch3.init);
<?php endif; ?>

function catchEnter(e)
{
	if (!e) e = window.event;
	var code = (e.keyCode) ? e.keyCode : e.which;
	if (code == 13 || code == 3) {
			var element = document.createElement("input");
			element.setAttribute("type", "hidden");
		    element.setAttribute("value", '1');
		    element.setAttribute("name", 'is_search');
		    document.id('mini_ynadvsearch_form').appendChild(element);
			document.id('mini_ynadvsearch_form').action = en4.core.baseUrl + 'ynadvsearch/search';
	}
}
window.onload = function() {
	if (document.getElementById('search_mini_field')) {
		document.id('search_mini_field').onkeypress = catchEnter;
	}
};
</script>