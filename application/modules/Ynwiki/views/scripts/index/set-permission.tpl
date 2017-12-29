<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php 
$menu = $this->partial('_menu.tpl', array());  
echo $menu;
?>
<?php echo $this->form->render($this);?>   
<script type="text/javascript">
/*function changeOption(type,obj)
{
    alert(obj.value);
}
en4.core.runonce.add(function()
{
new Autocompleter.Request.JSON('users', '<?php echo $this->url(array('module'=>'ynwiki','controller' => 'index', 'action' => 'suggest-user'), 'default', true) ?>', {
  'postVar' : 'text',
  'customChoices' : true,
  'minLength': 1,
  'selectMode': 'pick',
  'autocompleteType': 'tag',
  'className': 'user-autosuggest',
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
*/
</script>