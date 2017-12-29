function ynblogRenderViewMode(identity, default_view_mode, available_modes) {
    var myCookieViewMode = getCookie('ynblog-modeview-' + identity);
    if ( myCookieViewMode == '' || available_modes.indexOf(myCookieViewMode.split(/[-_]/)[1]) == -1) {
        myCookieViewMode = 'ynblog_' + default_view_mode + '-view';
    }

    $$('#ynblog-view-mode-button-' + identity + ' > span[rel=' + myCookieViewMode + ']').addClass('active');
    $$('#ynblog-content-mode-views-' + identity).addClass(myCookieViewMode);

    // Set click viewMode
    $$('#ynblog-view-mode-button-' + identity + ' > span').addEvent('click', function(){
        var viewmode = this.get('rel');
        var content = $('ynblog-content-mode-views-' + identity);

        setCookie('ynblog-modeview-' + identity, viewmode, 1);

        // set class active
        $$('#ynblog-view-mode-button-' + identity + ' > span').removeClass('active');
        this.addClass('active');

        content
            .removeClass('ynblog_list-view')
            .removeClass('ynblog_grid-view');

        content.addClass( viewmode );
    });
}