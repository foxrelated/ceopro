
   window.addEvent('load', function() {
   new DatePicker('.date_toggled', {
		pickerClass: 'datepicker_jqui',
		allowEmpty: true,
		toggleElements: '.date_toggler',
      inputOutputFormat: 'Y-m-d'
	});
});


function getdate()
{ 
  
  // var sday =$('sday').get('value');
  var temp1 = document.getElementById('date1-element').firstChild.getNext().value;
  var temp2 = document.getElementById('date2-element').firstChild.getNext().value;
  var sday =document.getElementById('sday').get('value');
  var eday =document.getElementById('eday').get('value');
  var start_day = document.getElementById('From_Date');
  var end_day = document.getElementById('To_Date');
  start_day.value= temp1;
  end_day.value= temp2; 
}

