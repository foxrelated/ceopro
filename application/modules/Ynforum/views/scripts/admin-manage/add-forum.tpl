<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
?>
<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
<script language="javascript" type="text/javascript">
    function changeCategory(value) {
        var parent_forum_combo = document.getElementById('parent_forum_id');
        if (value != '') {
            parent_forum_combo.options[0].disabled = false;
            parent_forum_combo.value = '';
            parent_forum_combo.hide();
        } else {
            parent_forum_combo.show();
            parent_forum_combo.options[0].disabled = true;
            parent_forum_id = '<?php echo $this->forum_id?>';
            if (!parent_forum_id) {
                parent_forum_combo.options[1].selected = true;
            } else {
                parent_forum_combo.value = parent_forum_id;
            }
        }
    }
    
    changeCategory(document.getElementById('category_id').value);
</script>    
