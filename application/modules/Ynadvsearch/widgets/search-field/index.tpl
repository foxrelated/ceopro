<?php
	$url = preg_replace('/phrase\/.+/', '', $this->url());
	$pos = strrpos($url, '/query/');

	if ($pos > 0) {
		$url = substr($url,0, $pos);
	}

	$suggest_url = $this->baseUrl() . '?m=lite&module=ynadvsearch&name=ynadvsearch';
?>
<script type="text/javascript">
	var modules = [];
	function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    }

    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
        }
        return "";
    } 
</script>

<form id="search_form" onsubmit="return <?php echo ($this->action == 'index') ? 'getListMods()' : ''?>" class="ynadvsearch-main-form search_form" method="post" action="<?php if ($this->action == 'index' || $this->module != 'ynadvsearch') echo $this->url(array('controller'=>'search', 'action'=>'index'),'ynadvsearch_search', true); else echo $url; ?>">
	<div id="searchform" class="ynadvsearch-form-box">
		<input type="hidden" name="listmods" id="listmods" value ="" />
		<input type="hidden" name="is_all" id="is_all" value = "" />
		<div class="ynadvsearch-form-select">
			<select id="tab_search" onchange="change_tab(this)">
				<?php foreach ($this->tabs as $tab) : ?>
					<?php if ($tab):?>
						<option id="<?php echo $tab['params']['action'];?>-tab" value="<?php echo $this->url($tab['params'], $tab['route'], true);?>"><?php echo ($tab['label']); ?></option>
					<?php endif; ?>
				<?php endforeach;?>
			</select>
		</div>
		<div class="ynadvsearch-form-input">
			<input id="query" type="text" value="<?php echo $this->query?>" placeholder="<?php echo $this->translate('Search key');?>" name="query">    
		</div>
	</div>

	<div class = "button_submit_2">
		<button id="search_btn" type="submit" name="search_btn" value="submit"><?php echo $this->translate('Search');?></button>
	</div>
</form>

<?php if($this->profile_search_enabled):?>
	<div class =" button_submit_3">
		<button id ="profile_search_button" onclick="window.location=en4.core.baseUrl+'profilesearch?full_search=<?php echo $this->query?>&tab_id=tab_1&page=1&sub_tab_id=&alpha_search='">
			<?php echo $this->translate('Advanced Member Search');?>
		</button>
	</div>
<?php endif;?>

<script type="text/javascript">
	function getListMods() {
		var selected_mods = [];
		$$('input.mods_checkbox').each(function(item){
			var checked = item.get('checked');
			var value = item.get('name');
			if (item.checked == true){
				selected_mods.push(item.name);
			}
		});
		$('listmods').value = selected_mods;
		if ($('all_mods').checked == true) {
			$('is_all').value = 'all';
		}
	}

	
	function change_tab(obj) {
		var search = $('query').get('value');
		var link = obj.getElements('option:selected').get('id');
		if (search)
			if (link != 'index-tab') 
				window.location.assign(obj.get('value')+'/query/'+search);
			else
				window.location.assign(obj.get('value')+'?query='+search);
		else
			window.location.assign(obj.get('value'));
	}
</script>

<script type='text/javascript'>
	var pos2 = 0;

window.addEvent('domready', function() {
	<?php if(($this->action == 'mp-musicalbums-search') || ($this->action == 'mp-musicplaylists-search')) :?>
		$('mp-music-search-tab').set('selected', true);
	<?php else:?>   
		$('<?php echo $this->action?>-tab').set('selected', true);
	<?php endif;?>
	$$('.ynadvsearch_main_search').getParent().addClass('active');

	var position = $('query').getPosition();
	pos2 = position.x;

	$$('input.mods_checkbox').each(function(item){
		var checked = item.get('checked');
		value = item.get('name');
		if (item.checked == true){
			modules.push(item.name);
		}
	
		item.addEvent('click', function() {
			if (item.checked == true){
				modules.push(item.name);
			}
			else {
				var index = modules.indexOf(item.name);
				modules.splice(index, 1);
			}
			
			var child = $$('.tag-autosearch2');
			if (child.length > 0)
				child[0].parentNode.removeChild(child[0]);
			$('query').removeEvents('blur');
			$('query').removeEvents('click');
			$('query').removeEvents('focus');
			$('query').removeEvents('keydown');
			$('query').removeEvents('keyup');
			ynadvsearch2.init();
		});
	});
});

var ynadvsearch2 = {
	is_space: false,
	input: null,
	ed: function () {
		Asset.javascript( '<?php echo $this->baseUrl() ?>' + '/application/modules/Ynadvsearch/externals/scripts/ynadvsearch.js', {
			onLoad: ynadvsearch2.eu
		});
	},
	
	cleanCss: function(e) {
		var p = e.parentNode;
		while (p && p.style != null) {
			p = p.parentNode;
		}
	},
	
	init: function () {
		var e = ynadvsearch2.input = document.id('query');
		if (e) {
			ynadvsearch2.cleanCss(e);
			ynadvsearch2.ed();
		}
	}
};

ynadvsearch2.eu = function(event){
	var item_type='';
	var maxre = <?php echo $this->maxRe; ?>;
	$$(".tag-autosearch2").each(function(e){
		var doc = document.body;
		doc.removeChild(e);
	});
	var count2 = 0;
	var header2 = -1;
	var prev_label2 = "";
	new YnAdsAutocompleter.RequestAdSearch.JSON('query', '<?php echo $suggest_url ?>', {
		'postVar': 'search',
		'className': 'tag-autosearch2',
		'choicesMatch': 'li.autocompleter-choices',
		'cache': false,
		'tokenFormat' : 'object',
		'tokenValueKey' : 'label',
		'maxChoices': 200,
		'width': 480,
		'left': true,
		'pos' : pos2,
		'postData' : {
			'maxre' : <?php echo $this->maxRe; ?>,
			'static_base_url':'<?php echo base64_encode($this->url(array(), 'default', true));?>',
			'action': '<?php echo $this->action?>',
			<?php if ($this->action == 'index') : ?>
			'modules': modules.join(','),
		<?php endif; ?>
	},
	
	'injectChoice': function(token){
		// show group
		if(token.type != 'menu') {
			if($$('.tag-autosearch2 .group-results')[header2])
			{
				if(item_type !=token.type && token.type != 'see_more_link' && token.type != 'no_result_found_link' && token.type != 'hidden_link'){
					if(count2 == 1)
					{
						var str_result = '<?php echo $this->translate('Result') ?>';
					}
					else
					{
						var str_result = '<?php echo $this->translate('Results') ?>';
					}
					$$('.tag-autosearch2 .group-results')[header2].set('html',prev_label2 + " " +"<div class='total_result_item'>" + count2 +" "+ str_result + "</div>");
					count2 = 0;
				}
			}
		}
		
		if(token.type != 'menu' && item_type !=token.type && token.type != 'see_more_link' && token.type != 'no_result_found_link' && token.type != 'hidden_link'){
			item_type = token.type;
			var group_li = new Element('li', {'class': 'group-results','html': token.type_label}).inject(this.choices);
			prev_label2 = token.type_label;
			header2++;
		}
		
		if (item_type !=token.type && token.type != 'see_more_link' && token.type != 'no_result_found_link' && token.type != 'hidden_link'){
			item_type = token.type;
			var group_li = new Element('li', {'class': 'group-results'}).inject(this.choices);
		}
		
		if(token.type=='see_more_link') {
			if($$('.tag-autosearch2 .group-results')[header2])
			{
				if(count2 == 1)
				{
					var str_result = '<?php echo $this->translate('Result') ?>';
				}
				else
				{
					var str_result = '<?php echo $this->translate('Results') ?>';
				}
				$$('.tag-autosearch2 .group-results')[header2].set('html',prev_label2 + " " +"<div class='total_result_item'>" + count2 +" "+ str_result + "</div>");
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
			'href':  (token.type=='see_more_link' || token.type=='no_result_found_link') ? 'javascript:void(0)': token.url,
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
			count2 ++;
		}
		
			this.addChoiceEvents(choice).inject(this.choices);
			choice.store('autocompleteChoice', token);
		}, 
		//injectChoice
		onChoiceSelect: function(choice){
			if (choice) {
				if (choice.hasClass('see_more_link') || choice.get('id') == 'noresult') {
					<?php echo ($this->action == 'index') ? 'getListMods();$("search_form").submit()' : ''?>
				}
				else {
					var a_tag = choice.getElements('a');
					if (a_tag[0]) {
						var url = a_tag[0].getProperty('href');
						document.location = url;
					}
				}
			}
		},
		onCommand: function(e){
			if(e.key == 'space')
			{
				ynadvsearch2.is_space = true;
			}
			else
			{
				ynadvsearch2.is_space = false;
			}
		},
		onRequest: function(){
			prev_label2 = "" ;
			count2 = 0;
			header2 = -1;
			this.cached = [];
			var trimquery = document.id('query').value;
			var l=0; var r=trimquery.length -1;
			while(l < trimquery.length && trimquery[l] == ' ')
			{   l++; }
			trimquery = trimquery.substring(l);
			if (trimquery && ynadvsearch2.is_space == false)
			{
				document.id('query').setStyle('background', "url('application/modules/Ynadvsearch/externals/images/ajax-loader.gif') no-repeat right ");
			}
		},
		onComplete: function(response){
			document.id('query').setStyle('background', '');
			item_type = '';
		}
	});
};
<?php if (!$this->isMobile) : ?>
window.addEvent('domready', ynadvsearch2.init);
<?php endif; ?>

<?php if ( defined('YNRESPONSIVE_ACTIVE') ) : ?>
	window.addEvent('domready', function(){
		$$('body')[0].addClass('<?php echo YNRESPONSIVE_ACTIVE; ?>');
	});
<?php endif; ?>

</script>

