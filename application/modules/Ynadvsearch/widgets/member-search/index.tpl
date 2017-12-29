<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    'topLevelId' => (int) @$this->topLevelId,
    'topLevelValue' => (int) @$this->topLevelValue
  ))
?>
<?php
    echo $this->form->render($this)
?>
<script type="text/javascript">
    function searchMembers() {
        $('filter_form').submit();
    }
</script>