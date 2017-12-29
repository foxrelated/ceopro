<script type="text/javascript">
  en4.core.runonce.add(function(){
      document.getElementById('selectall').addEvent('click', function(){
          check = this.checked;
          list = document.getElementsByName('users[]');
          for(var i=0;i<list.length;i++){
            list[i].checked = check;
          }
    })});
</script>

<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>