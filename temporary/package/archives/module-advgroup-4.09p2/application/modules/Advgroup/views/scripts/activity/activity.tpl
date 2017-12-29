<script type="text/javascript">
  en4.core.runonce.add(function(){
    $('selectall').addEvent('click', function(event) {
      var el = $(event.target);
      $$('input[type=checkbox]').set('checked', el.get('checked'));
    })
  });
</script>

<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this);?>
