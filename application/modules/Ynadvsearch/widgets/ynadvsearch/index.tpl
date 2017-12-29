<?php
$staticBaseUrl = $this->layout()->staticBaseUrl;
$url = $this->baseUrl() . '?m=lite&module=ynadvsearch&name=ynadvsearch';
?>
<script type="text/javascript">
var pos = 0;
window.addEvent('domready', function() {
    if (typeof jQuery != 'undefined') jQuery.noConflict();
    if ($('global_search_form')) {
    	var position = $('global_search_form').getCoordinates();
    	var right = <?php echo $this->align ?>;
    	if (right === 1) {
    		pos = - 480 + position.width - 5;
    	}
	}
});

var ynadvsearch = {
    is_space: false,
    input: null,
    ed: function () {
        Asset.javascript( '<?php echo $this->baseUrl() ?>' + '/application/modules/Ynadvsearch/externals/scripts/ynadvsearch.js', {
            onLoad: ynadvsearch.eu
        });
    },
    cleanCss: function(e) {
        var p = e.parentNode;
        while (p && p.style != null) {
            p = p.parentNode;
        }
    },
    init: function () {
        var e = ynadvsearch.input = document.id('global_search_field');
        if (e) {
            ynadvsearch.cleanCss(e);
            ynadvsearch.ed();
        }
    }
};
ynadvsearch.eu = function(event){
	 var item_type='';
	 var maxre = <?php echo $this->maxRe; ?>;
	 $$(".tag-autosearch").each(function(e){
		    var doc = document.body;
		    doc.removeChild(e);
		});
	 var count = 0;
	 var header = -1;
	 var prev_label = "";
	 new YnAdsAutocompleter.RequestAdSearch.JSON('global_search_field', '<?php echo $url ?>', {
		  	'postVar': 'search',
	        'className': 'tag-autosearch',
	        'choicesMatch': 'li.autocompleter-choices',
	        'cache': false,
	        'tokenFormat' : 'object',
	        'tokenValueKey' : 'label',
	        'maxChoices': 200,
	        'width': 480,
	        'pos' : pos,
	        'postData' : {
				'maxre' : <?php echo $this->maxRe; ?>,
				'static_base_url':'<?php echo base64_encode($this->url(array(), 'default', true));?>'
		    },
	        'injectChoice': function(token){
				// show group
				if(token.type != 'menu')
				{
					if($$('.group-results')[header])
					{
						if(item_type !=token.type && token.type != 'see_more_link' && token.type != 'no_result_found_link' && token.type != 'hidden_link'){
							if(count == 1)
							{
								var str_result = '<?php echo $this->translate('Result') ?>';
							}
							else
							{
								var str_result = '<?php echo $this->translate('Results') ?>';
							}
							$$('.group-results')[header].set('html',prev_label + " " +"<div class='total_result_item'>" + count +" "+ str_result + "</div>");
							count = 0;
						}
					}
				}
				if(token.type != 'menu' && item_type !=token.type && token.type != 'see_more_link' && token.type != 'no_result_found_link' && token.type != 'hidden_link'){
					item_type = token.type;
					var group_li = new Element('li', {'class': 'group-results','html': token.type_label}).inject(this.choices);
					prev_label = token.type_label;
					header++;
				}

				if (item_type !=token.type && token.type != 'see_more_link' && token.type != 'no_result_found_link' && token.type != 'hidden_link'){
					item_type = token.type;
					var group_li = new Element('li', {'class': 'group-results'}).inject(this.choices);
				}

				if(token.type=='see_more_link')
				{
					if($$('.group-results')[header])
					{
						if(count > 1)
						{
							var str_result = '<?php echo $this->translate('Results') ?>';
						}
						else
						{
							var str_result = '<?php echo $this->translate('Result') ?>';
						}
						$$('.group-results')[header].set('html',prev_label + " " +"<div class='total_result_item'>" + count +" "+ str_result + "</div>");
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
				//TODO class type
				// normal result
				else if (token.type !='no_result_found_link' && token.type!='menu'){
					//category
					var choice_category= new Element('span',{'html': token.type_label,'class':'type'}).inject(choice_a);
					count ++;
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
                	 ynadvsearch.is_space = true;
                }
                else
                {
                    ynadvsearch.is_space = false;
                }
           },
		  	onRequest: function(){
		  		prev_label = "" ;
		  		count = 0;
	 			header = -1;
		  		this.cached = [];
				var trimquery = document.id('global_search_field').value;
				var l=0; var r=trimquery.length -1;
				while(l < trimquery.length && trimquery[l] == ' ')
				{	l++; }
				trimquery = trimquery.substring(l);
				if (trimquery && ynadvsearch.is_space == false)
				{
			  		document.id('global_search_field').setStyle('background', "url('application/modules/Ynadvsearch/externals/images/ajax-loader.gif') no-repeat right ");
				}

			},
			onComplete: function(response){
				document.id('global_search_field').setStyle('background', '');
				item_type = '';
			}
	    });

};

function catchEnter(e)
{
	if (!e) e = window.event;
	var code = (e.keyCode) ? e.keyCode : e.which;
	if (code == 13 || code == 3) {
		// check conflict with hashtag search
		var hasHashtag = false;
		if($$('.ynhashtag-autosearch-global').length || $$('.ynhashtag-autosearch-form').length) {
			var query = document.id('global_search_field').value;
			var temp_query = query.replace(/\s/g, '');
			var pos = temp_query.lastIndexOf('#');
			if (pos == 0)
			{
				hasHashtag = true;
				query = query.replace(/\s+#/ig, '#');
				var hashtag_text = query.replace(/\#/, '');
				if(hashtag_text && query.length > 1) {
					document.id('global_search_form').action = en4.core.baseUrl + 'hashtag/' + hashtag_text;
				}
			}
		}
		if(hasHashtag == false) {
			var element = document.createElement("input");
			element.setAttribute("type", "hidden");
			element.setAttribute("value", '1');
			element.setAttribute("name", 'is_search');
			this.appendChild(element);
			document.id('global_search_form').action = '<?php echo $this -> url(array(), 'ynadvsearch_search', true)?>';
		}
	}
}

window.addEvent('domready', function(){
    if ($('global_search_form')) {
		ynadvsearch.init();
	}
	if (document.getElementById('global_search_field')) {
		document.id('global_search_field').onkeypress = catchEnter;
	}
});

</script>