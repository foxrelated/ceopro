
<?php if( $this->form ): ?>
    <?php echo $this->form->render($this) ?>

    <script type="text/javascript">

        function updateIconType(el, inputId)
        {
            if (el.value == 'icon_class') {
                $(inputId).show();
            } else {
                $(inputId).hide();
            }
        }

        window.addEvent('domready', function() {
            $('icon').onchange();
            $('hover_active_icon').onchange();
        });
    </script>

<?php elseif( $this->status ): ?>

    <div><?php echo $this->translate("Your changes have been saved.") ?></div>

    <script type="text/javascript">
        setTimeout(function() {
            parent.window.location.replace( '<?php echo $this->url(array('action' => 'index', 'name' => $this->selectedMenu->name)) ?>' )
        }, 500);
    </script>

<?php endif; ?>