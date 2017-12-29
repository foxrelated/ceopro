<?php
    $googleApi = Engine_Api::_()->getApi('settings', 'core') -> getSetting('yncore.google.api.key', 'AIzaSyBUNcrjiTBFrc5NS9b_yrmH6TNRRUkSQzM');
    $this->headScript()->appendFile('https://maps.googleapis.com/maps/api/js?libraries=places&key=' . $googleApi)
?>
<?php echo $this->form->render($this) ?>
<script type="text/javascript">
function initialize() 
{
    var input = document.getElementById('location');
    var autocomplete = new google.maps.places.Autocomplete(input);
}
google.maps.event.addDomListener(window, 'load', initialize);
</script>


