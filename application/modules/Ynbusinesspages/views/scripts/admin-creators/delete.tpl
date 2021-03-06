<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo $this->translate("Remove Creator?") ?></h3>
        <p><?php echo $this->translate("Are you sure that you want to remove this Creator?") ?></p>
        <br />
        <p>
            <input type="hidden" name="confirm" value="<?php echo $this->creator_id?>"/>
            <button type='submit'><?php echo $this->translate("Remove") ?></button>
            <?php echo $this->translate(" or ") ?> 
            <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
            <?php echo $this->translate("cancel") ?></a>
        </p>
    </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
    TB_close();
</script>
<?php endif; ?>