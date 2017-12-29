en4.core.runonce.add(function() 
{
  // ANNOUNCEMENT HIGHLIGHT
  $$('a.ynforum_announcement_highlight').addEvent('click', function() {
    var url_part    = this.href.split('/');
    var annou_id = 0;
    $each(url_part, function(val, i) {
      if (val == 'announcement_id')
        annou_id = url_part[i+1];
    });
    new Request.JSON({
      method: 'post',
      url: this.href,
      noCache: true,
      data: {
        'announcement_id': annou_id,
        'format': 'json'
      },
      onSuccess: function(json)
      {
        var link = $$('#ynforum_announcement_item_' + annou_id + ' a.ynforum_announcement_highlight')[0];
        if (json && json.success) 
        {
          $$('a.ynforum_announcement_highlight')
            .set('text', en4.core.language.translate('Highlight'))
            .addClass('icon_ynforum_announcement_highlight')
            .removeClass('icon_ynforum_announcement_unhighlight')
            ;
          if( json.enabled && link ) 
          {
            link
              .set('text', en4.core.language.translate('Un-highlight'))
              .addClass('icon_ynforum_announcement_unhighlight')
              .removeClass('icon_ynforum_announcement_highlight')
              ;
          }
        }
      }
    }).send();
    return false;
  });
   // EVENT HIGHLIGHT
  $$('a.ynforum_event_highlight').addEvent('click', function() {
    var url_part    = this.href.split('/');
    var event_id = 0;
    $each(url_part, function(val, i) {
      if (val == 'event_id')
        event_id = url_part[i+1];
    });
    new Request.JSON({
      method: 'post',
      url: this.href,
      noCache: true,
      data: {
        'event_id': event_id,
        'format': 'json'
      },
      onSuccess: function(json)
      {
        var link = $$('#ynforum_event_item_' + event_id + ' a.ynforum_event_highlight')[0];
        if (json && json.success) 
        {
          if( json.enabled && link ) 
          {
            link
              .set('text', en4.core.language.translate('Un-highlight'))
              .addClass('icon_ynforum_event_unhighlight')
              .removeClass('icon_ynforum_event_highlight')
              ;
          }
          else if(link)
          {
          	link
              .set('text', en4.core.language.translate('Highlight'))
              .addClass('icon_ynforum_event_highlight')
              .removeClass('icon_ynforum_event_unhighlight')
              ;
          }
        }
      }
    }).send();
    return false;
  });
  // GROUP HIGHLIGHT
  $$('a.ynforum_group_highlight').addEvent('click', function() {
    var url_part    = this.href.split('/');
    var group_id = 0;
    $each(url_part, function(val, i) {
      if (val == 'group_id')
        group_id = url_part[i+1];
    });
    new Request.JSON({
      method: 'post',
      url: this.href,
      noCache: true,
      data: {
        'group_id': group_id,
        'format': 'json'
      },
      onSuccess: function(json)
      {
        var link = $$('#ynforum_group_item_' + group_id + ' a.ynforum_group_highlight')[0];
        if (json && json.success) 
        {
          if( json.enabled && link ) 
          {
            link
              .set('text', en4.core.language.translate('Un-highlight'))
              .addClass('icon_ynforum_group_unhighlight')
              .removeClass('icon_ynforum_group_highlight')
              ;
          }
          else if(link)
          {
          	link
              .set('text', en4.core.language.translate('Highlight'))
              .addClass('icon_ynforum_group_highlight')
              .removeClass('icon_ynforum_group_unhighlight')
              ;
          }
        }
      }
    }).send();
    return false;
  });
  // POLL HIGHLIGHT
  $$('a.ynforum_poll_highlight').addEvent('click', function() {
    var url_part    = this.href.split('/');
    var poll_id = 0;
    $each(url_part, function(val, i) {
      if (val == 'poll_id')
        poll_id = url_part[i+1];
    });
    new Request.JSON({
      method: 'post',
      url: this.href,
      noCache: true,
      data: {
        'poll_id': poll_id,
        'format': 'json'
      },
      onSuccess: function(json)
      {
        var link = $$('#ynforum_poll_item_' + poll_id + ' a.ynforum_poll_highlight')[0];
        if (json && json.success) 
        {
          if( json.enabled && link ) 
          {
            link
              .set('text', en4.core.language.translate('Un-highlight'))
              .addClass('icon_ynforum_poll_unhighlight')
              .removeClass('icon_ynforum_poll_highlight')
              ;
          }
          else if(link)
          { 
          	link
              .set('text', en4.core.language.translate('Highlight'))
              .addClass('icon_ynforum_poll_highlight')
              .removeClass('icon_ynforum_poll_unhighlight')
              ;
          }
        }
      }
    }).send();
    return false;
  });
 });