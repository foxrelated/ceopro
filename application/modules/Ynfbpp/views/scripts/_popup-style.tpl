<?php $settings =  Engine_Api::_() -> getApi('settings', 'core'); ?>

<style>
    #ynfbppUiOverlayContent {
        border-color: <?php echo $settings->getSetting('ynfbpp.popup.bordercolor', '#EAEAEA') ?>;
    }
    .uiYnfbppContextualDialogArrow {
        border-bottom-color: <?php echo $settings->getSetting('ynfbpp.popup.bordercolor', '#EAEAEA') ?>;
    }
    .uiContextualDialogContent {
        background-color: <?php echo $settings->getSetting('ynfbpp.popup.backgroundcolor', '#FFFFFF') ?>;
    }
    .uiYnfbppHovercardFooter {
        background-color: <?php echo $settings->getSetting('ynfbpp.popup.footercolor', '#F8F8F8') ?>;
    }
    .uiYnfbppDialogDirDown .uiYnfbppContextualDialogArrow_child {
        border-bottom-color: <?php echo $settings->getSetting('ynfbpp.popup.backgroundcolor', '#FFFFFF') . '!important'?>;
    }
    .uiYnfbppDialogDirUp .uiYnfbppContextualDialogArrow_child {
        border-bottom-color: <?php echo $settings->getSetting('ynfbpp.popup.footercolor', '#FFFFFF') . '!important'?>;
    }
</style>