<h3><?php echo $this->translate('Search Filters')?></h3>

<ul class = "ynadvsearch_module_list">
<li class = "ynadvsearch_module">
<?php
	$active = '';
	if (isset($this->is_all) || $this->search == 'all') {
		$active = 'active';
	}
?>
<input class = "mods_checkbox_all" onclick='checkedAll();filterModule();' type="checkbox" id = "all_mods" name="all_mods" value="1" <?php echo ($active == 'active')? 'checked' : '';?>>
<?php echo $this->htmlLink($this->url(array('module'=> 'ynadvsearch', 'controller'=>'search', 'action'=>'index', 'search'=>'all', 'phrase'=>$this->query), 'default'),
                    ($this->translate('All Results')),
                    array('class'=>$active));
	 ?>
</li>
<?php
$modules = $this->modules;
foreach($modules as $module):
	 if ($module->name == "ynshare" || $module->name == "ynsharebutton")
		continue;
	 if ((($module->name == $this->search) || @in_array($module->name,$this->mods_array) )&& ($this->search != 'all') && !isset($this->is_all)) {
	 	$active = 'active';
	 }
	 else {
	 	$active = '';
	 }
	 ?>
	 <li class = "ynadvsearch_module">
	 <input onclick='uncheckAll();filterModule();' class = "mods_checkbox" type="checkbox" id = "<?php echo $module->name?>" name="<?php echo $module->name?>" value="1" <?php echo ($active == 'active'|| $this->search == 'all' )? 'checked' : '';?>>
	 <?php
	 if ($module->name == 'activity') {
	 	$title = $this->translate('Hash Tags');
	 }
	 else {
	 	$title = $this->translate($module->title);
	 }
	 echo $this->htmlLink($this->url(array('module'=> 'ynadvsearch', 'controller'=>'search', 'action'=>'index', 'search'=>$module->name, 'phrase'=>$this->query), 'default'),
                    ($title),
                    array('class'=>$active));
	 ?>
	 </li>

<?php endforeach;?>
</ul>
<style type = "text/css">
.ynadvsearch_module_list .active {
	font-weight: bold;
}
</style>

<script type="text/javascript">
window.addEvent('domready', function() {
	var	list = '<?php echo $this->listmods?>';
	var box = document.getElementById('all_mods');
	var flag_1 = 0;
	var flag_2 = 0;
	var flag_3 = 0;
	if (list != '') {
		var listmodules = list.split(',');
		for (var i in listmodules){
			flag_3++;
			$$('input.mods_checkbox').each(function(item){
				flag_1++;
				var value = item.name;
				if (value == listmodules[i]) {
					item.checked = true;
					flag_2++;
				}
			});
		}
		var flag_0 = 0;
		flag_0 = flag_1/flag_3;
		if (flag_0 == flag_2) {
			box.checked = true;
		}
		else {
			box.checked = false;
		}
	}
	var all = '<?php echo $this->search?>';
	if (all == 'all') {
		document.getElementById('all_mods').checked = true;
	}
});
function filterModule(){
    var query = '<?php echo $this->query?>';
    if (!query) {
        return;
    }

    var selected_mods = [];
    $$('input.mods_checkbox').each(function(item){
        var checked = item.get('checked');
        var value = item.get('name');
        if (item.checked == true){
            selected_mods.push(item.name);
        }
    });
    $('listmods').value = selected_mods;
    if (!$('all_mods').checked && !selected_mods.length) {
        $('ynadvsearch_searchresults').empty();
        return;
    }
    var listmods = ($('all_mods').checked) ? '' : selected_mods.toString();
    var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index'), 'default', true) ?>';
    $('ynadvsearch_searchresults').hide();
    $('ynadvsearch_loading').show();
    en4.core.request.send(new Request.HTML({
      url : url,
      data : {
           format : 'html',
           'name': 'ynadvsearch.search-results',
           'is_search': 1,
           'query': query,
           'listmods': listmods
      },
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
           var parent = $('ynadvsearch_searchresults').getParent();
           Elements.from(responseHTML).replaces(parent);
           $('ynadvsearch_loading').hide();
           $('ynadvsearch_searchresults').show();
      }
    }));
};
function checkedAll() {
	var box = document.getElementById('all_mods');
	$$('input.mods_checkbox').each(function(item){
		if (box.checked == true) {
			item.checked = true;
		}
		else {
			item.checked = false;
		}
    });
}
function uncheckAll() {
	var box = document.getElementById('all_mods');
	var flag_1 = 0;
	var flag_2 = 0;
	$$('input.mods_checkbox').each(function(item){
		flag_1++;
		if (item.checked == true) {
			flag_2++;
		}
	});
	if (flag_1 == flag_2) {
		box.checked = true;
	}
	else {
		box.checked = false;
	}
}


</script>
