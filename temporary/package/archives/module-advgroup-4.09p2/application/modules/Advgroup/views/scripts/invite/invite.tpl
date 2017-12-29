<script type="text/javascript">
    function tab_switch_type(element,content){
        if( element.tagName.toLowerCase() == 'a' ) {
            element = element.getParent('li');
        }
        var myContainer = element.getParent('.tabs_parent').getParent();
        myContainer.getElements('ul > li').removeClass('active');
        document.getElementById('manual_content').style.display = 'none';
        document.getElementById('file_content').style.display = 'none';
        element.addClass('active');
        document.getElementById(content).style.display = 'block';
    }
</script>
<div class="tabs_alt tabs_parent">
    <ul id="main_tabs">
        <li class="active"><a onclick="tab_switch_type($(this),'manual_content');" href="javascript:void(0);"><?php echo $this->translate('Invite by manually typing emails') ?></a></li>
        <li><a onclick="tab_switch_type($(this),'file_content');" href="javascript:void(0);"><?php echo $this->translate('Invite by uploading a CSV file') ?></a></li>
    </ul>
</div>
<table>
    <tr>
        <td id="manual_content" class="ymb_invite_manual" style="display: block; padding-left:10px;">
            <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
        </td>
    </tr>
    <tr>
        <td id="file_content" style="display: none; padding-left:10px;">
            <div id="uploadcsvform3" >
            	<?php $session = new Zend_Session_Namespace('mobile');?>
                <form method="post" action="<?php echo $this->url(); ?><?php if(!$session->mobile) echo "?format=smoothbox"?>"  class="global_form_popup" enctype="multipart/form-data" onsubmit="sending_request();">
                    <h3><?php echo $this->translate("Invite Your Friends");?></h3>
                    <?php if ($this->settings->getSetting('invite.allowCustomMessage', 1) > 0) : ?>
                        <div class="form-wrapper" id="message-wrapper">
                            <div class="form-label" id="message-label"><label class="optional" for="message"><?php echo $this->translate("Custom Message");?></label></div>
                            <div class="form-element" id="message-element">
                                <textarea rows="6" cols="45" id="message" name="message" style="width:450px;"><?php echo $this->settings->getSetting('invite.message', '%invite_url%'); ?></textarea>
                                <p class="description"><?php echo $this->translate("(Use %invite_url% to add a link to our sign up page)");?></p>
                            </div>
                        </div>
                    <?php endif ?>
                    <br>
                    <?php echo $this->translate('File to upload:'); ?> <input type='file' name='csvfile' class='text'>
                    <br>
                    <br>
                    <button type="submit" name="submit_button"><?php echo $this->translate('Send Invites'); ?></button><?php echo $this->translate(' or'); ?>
                    <a name="cancel" id="cancel" type="button" href="javascript:void(0);" onclick="parent.Smoothbox.close();">cancel</a>
                    <input type="hidden" name="csv_upload" value="csv"/>
                </form>
            </div>
        </td>
    </tr>
</table>
