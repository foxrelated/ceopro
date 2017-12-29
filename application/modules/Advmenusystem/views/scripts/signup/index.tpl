<div class="advmenusystem-popup">    
  <div class="advmenusystem-popup-content">
    <div class='advmenusystem_lightbox' id='user_form_default_sea_lightbox'>
      <?php
      if($this->form){
        $this -> form -> setAction($this -> url());
        echo $this->partial($this->script[0], $this->script[1], array(
          'form' => $this->form
        )); 
      }
      ?>
      <script type="text/javascript">
        $('global_content_simple').addClass('advmenusystem-signup');
        $('global_content_simple').getElement('form').addClass('advmenusystem-user-signup-form');
      </script>
      <?php if($this->socialconnect_enable): ?>
        <div class="form-elements">
        <?php echo $this->content()->renderWidget('social-connect.quick-login'); ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script type="text/javascript">
  $$('.form-label').each(function(form_label){
      form_element = form_label.getNext();
      input = form_element.getElement('input');
      label = form_label.getElement('label');
      if(input && label)
        input.setProperty('placeholder', label.innerHTML);
    });
</script>

<script type="text/javascript">
  jQuery.noConflict();
  if( $("user_signup_form") ) $("user_signup_form").getElements(".form-errors").destroy();
  
  function skipForm() {
    document.getElementById("skip").value = "skipForm";
    $('SignupForm').submit();
  }
  
  function finishForm() {
    document.getElementById("nextStep").value = "finish";
  }
</script>