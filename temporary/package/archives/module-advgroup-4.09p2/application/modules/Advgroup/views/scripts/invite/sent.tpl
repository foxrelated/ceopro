<div class="global_form_popup">
<h2>
  <?php echo $this->translate(array("Invitation Sent","Invitations Sent",$this->emails_sent)) ?>
</h2>
<?php if($this->ers) :?>
<p>
  <?php echo $this->ers ?>
</p>
<?php else :?>
<p>
  <?php echo $this->translate(array('If the person you invited decide to join, he/she will be automatically added to your group.',
                                    'If the persons you invited decide to join, they will be automatically added to your group.',
                                    $this->emails_sent)) ?>
</p>
<br/>

<?php if (!empty($this->form->invalid_emails)): ?>
  <p><?php echo $this->translate('Invites were not sent to these email addresses because they do not appear to be valid:') ?></p>
  <ul>
    <?php foreach ($this->form->invalid_emails as $email): ?>
    <li><?php echo $email ?></li>
    <?php endforeach ?>
  </ul>
  <br/>
<?php endif ?>

<?php if (!empty($this->already_members)): ?>
  <p>
    <?php echo $this->translate('Some of the email addresses you provided belong to existing members:') ?>
    <?php foreach ($this->already_members as $user): ?>
      <a href="<?php echo $this->baseUrl();?>/profile/<?php echo $user->username ?>" target="_blank"><?php echo $user->username ?></a>
    <?php endforeach ?>
  </p>
  <br/>
<?php endif ?>
<?php endif ?>
<?php
$href = "javascript:void(0);";
$session = new Zend_Session_Namespace('mobile');
if ($session -> mobile)
{
	$href = $this -> url(array('id' => $this->group -> getIdentity()), 'group_profile', true); 
}
?>
<a class="buttonlink icon_back" onclick="parent.Smoothbox.close();" href="<?php echo $href?>"><?php echo $this->translate('OK, thanks !') ?></a>
</div>