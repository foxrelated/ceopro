<style>
    #ynfbppUiOverlay {
        left:20%;
        display: none;
    }
</style>
<div class="uiYnfbppDialogOverlay" id="ynfbppUiOverlay">
    <div class="uiYnfbppOverlayContent" id="ynfbppUiOverlayContent">
        <div class="uiContextualDialogContent">
            <div class="uiYnfbppHovercardStage clearfix">
                <div class="uiYnfbpp_avarta_owner">
                </div>
                <div class="uiYnfbpp_main_info">
                    <div class="uiYnfbpp_info_content">
                        <h2 class="uiYnfbpp_title ">
                            <a href="javascript:void(0)"><?php echo $this->translate('Item Title') ?></a>
                        </h2>
                        <ul class="uiYnfbpp_personal_info clearfix">
                            <li class="uiYnfbpp_overflow">
                                <span class="ynicon yn-user"></span>
                                <span><?php echo $this->translate('Owner') ?>:</span>
                                <a href="javascript:void(0);"><?php echo $this->translate('Sample user') ?></a>
                            </li>
                            <li class="uiYnfbpp_overflow">
                                <span class="ynicon yn-folder"></span>
                                <span><?php echo $this->translate('Category') ?>:</span>
                                <span class="uiYnfbpp_cate"><?php echo $this->translate('Sample category') ?></span>
                            </li>
                            <li class="uiYnfbpp_fullwidth uiYnfbpp_overflow">
                                <span class="ynicon yn-map-marker"></span>
                                <span class="uiYnfbpp_local"><?php echo $this->translate('Sample location') ?></span>
                            </li>
                            <li class="uiYnfbpp_personal_decs">
                                <span><?php echo $this->translate('YNFBPP_ADMIN_SETTINGS_POPUP_DESCRIPTION_SAMPLE') ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="uiYnfbppHovercardFooter clearfix">
            <ul class="uiYnfbppListHorizontal">
                <li class="uiYnfbppListItem">
                    <a href="javascript:void(0);"><?php echo $this->translate('Action 1') ?></a>
                </li>
                <li class="uiYnfbppListItem">
                    <a href="javascript:void(0);"><?php echo $this->translate('Action 2') ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>