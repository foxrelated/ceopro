<?php
  if (APPLICATION_ENV == 'production')
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');
  else
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
      'postVar' : 'text',

      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
  });
</script>
<div class='global_form_popup'>
<?php
  echo $this->form->render($this);
?>
</div>

<script>
    function loadFeed(){
      var categoryparent_id = $('categoryparent').options[$('categoryparent').selectedIndex].value;
      var request = new Request.JSON({
      url: en4.core.baseUrl + "news/loadfeed/" + categoryparent_id,
      method: 'post',
      data : {
        'format' : 'json',
        'categoryparent': categoryparent_id
      } ,
        onComplete:function(responseObject)
        {
            if(responseObject && responseObject.html != "" && responseObject.html != null)
            {
                document.getElementById('category-element').innerHTML ='<select id= "category" name = "category" style="width:160px;margin-bottom:5px;">' + responseObject.html + '</select>' ;
            }else{
                document.getElementById('category-element').innerHTML ='<select id= "category" name = "category" style="width:160px;margin-bottom:5px;">' + '<option value="-10" label="No Feed" selected= "selected"><?php echo $this->translate('No Feed') ?></option>'+ '</select>' ;
            }
        }
    });
    request.send();
        
}
</script>
<style type="text/css">
.tabs > ul > li 
{
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul 
{  
 	display: table;
 	height: 65px;
}
ul.tag-autosuggest 
{
	position: absolute;
	padding: 0px;
	width: 300px;
	list-style: none;
	z-index: 50;
	border: 1px solid #ddd;
	margin: 0px;
	list-style: none;
	cursor: pointer;
	white-space: nowrap;
	background: #fff;
}
ul.tag-autosuggest > li {
	padding: 3px;
	overflow: hidden;
}
ul.tag-autosuggest > li.autocompleter-choices {
	font-size: .8em;
}
ul.tag-autosuggest > li.autocompleter-choices .autocompleter-choice {
	line-height: 25px;
}
.autocompleter-choice {
	cursor: pointer;
}
ul.tag-autosuggest > li span.autocompleter-queried {
	font-weight: bold;
}
</style>  