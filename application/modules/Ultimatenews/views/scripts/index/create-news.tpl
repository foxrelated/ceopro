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
<?php echo $this->form->render($this) ?>
<script>
    function selectedCategory()
    {    
        var obj = document.getElementById("category");
        for(i=0; i<obj.options.length; i++)
        {
            if(obj.options[i].value == <?php echo ($_SESSION['keysearch']['category']) ? $_SESSION['keysearch']['category'] : "-1" ?>)
            {
                obj.options[i].selected = true;
            }
        }
        var obj = document.getElementById("categoryparent");
        for(i=0; i<obj.options.length; i++)
        {
            if(obj.options[i].value == <?php echo ($_SESSION['keysearch']['category_parent']) ? $_SESSION['keysearch']['category_parent'] : "-1" ?>)
            {
                obj.options[i].selected = true;
            }
        }
        var objInput =document.getElementById('search');
        if (objInput)
            objInput.value =  <?php if($_SESSION['keysearch']['keyword']!=""){ echo "'".$_SESSION['keysearch']['keyword']."'";}else{echo "''";}?>  ;
    }
    selectedCategory();

    function loadFeed(){
      var categoryparent_id = $('categoryparent').options[$('categoryparent').selectedIndex].value;
      var request = new Request.JSON({
      url: en4.core.baseUrl + "news/loadfeed/"+categoryparent_id,
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
                document.getElementById('category-element').innerHTML ='<select id= "category" name = "category" style="width:160px;margin-bottom:5px;">' + '<option value="" label="No Feed" selected= "selected"><?php echo $this->translate('No Feed') ?></option>'+ '</select>' ;
            }
        }
    });
    request.send();
}
</script>