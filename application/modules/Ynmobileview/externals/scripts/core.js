(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;

ynmobileview = {
	like : function(action_id) 
	{
		en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'ynmobileview/feed/like',
        data : {
          format     : 'json',
           action_id : action_id,
           subject : en4.core.subject.guid
        },
        'onComplete' : function(data)
        {
             $('feed_item_option_like_' + action_id).className = 'feed_item_option_unlike';
	      	 $('feed_item_option_like_' + action_id).innerHTML = '<a onclick="javascript:ynmobileview.unlike('+ action_id +');" href="javascript:void(0);"><i class="feed_item_option_icon"> </i><strong>'+ data.title +'</strong></a>';
	      	 $('total_likes_activity_item_' + action_id).innerHTML = data.str_totalLike;
        }
      }));
  },

  unlike : function(action_id) 
  {
  	en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'ynmobileview/feed/unlike',
        data : {
          format     : 'json',
           action_id : action_id,
           subject : en4.core.subject.guid
        },
        'onComplete' : function(data)
        {
             $('feed_item_option_like_' + action_id).className = 'feed_item_option_like';
	      	 $('feed_item_option_like_' + action_id).innerHTML = '<a onclick="javascript:ynmobileview.like('+ action_id +');" href="javascript:void(0);"><i class="feed_item_option_icon"> </i><strong>'+ data.title +'</strong></a>';
	      	 $('total_likes_activity_item_' + action_id).innerHTML = data.str_totalLike;
        }
      }));
  }, 
};

YnmobiUpdateHandler = new Class({

  Implements : [Events, Options],
  options : {
      debug : true,
      baseUrl : '/',
      identity : false,
      delay : 5000,
      admin : false,
      idleTimeout : 600000,
      last_id : 0,
      next_id : null,
      subject_guid : null,
      showImmediately : false
    },

  state : true,

  activestate : 1,

  fresh : true,

  lastEventTime : false,

  title: document.title,
  
  //loopId : false,
  
  initialize : function(options) {
    this.setOptions(options);
  },

  start : function() {
    this.state = true;

    // Do idle checking
    this.idleWatcher = new IdleWatcher(this, {timeout : this.options.idleTimeout});
    this.idleWatcher.register();
    this.addEvents({
      'onStateActive' : function() {
        this._log('activity loop onStateActive');
        this.activestate = 1;
        this.state = true;
      }.bind(this),
      'onStateIdle' : function() {
        this._log('activity loop onStateIdle');
        this.activestate = 0;
        this.state = false;
      }.bind(this)
    });
    this.loop();
    //this.loopId = this.loop.periodical(this.options.delay, this);
  },

  stop : function() {
    this.state = false;
  },

  checkFeedUpdate : function(action_id, subject_guid){
    if( en4.core.request.isRequestActive() ) return;
    
    function getAllElementsWithAttribute(attribute) {
      var matchingElements = [];
      var values = [];
      var allElements = document.getElementsByTagName('*');
      for (var i = 0; i < allElements.length; i++) {
        if (allElements[i].getAttribute(attribute)) {
          // Element exists with attribute. Add to array.
          matchingElements.push(allElements[i]);
          values.push(allElements[i].getAttribute(attribute));
          }
        }
      return values;
    }
    var list = getAllElementsWithAttribute('data-activity-feed-item');
    this.options.last_id = Math.max.apply( Math, list );
    min_id = this.options.last_id + 1;
      
    var req = new Request.HTML({
      url : en4.core.baseUrl + 'widget/index/name/ynmobileview.mobi-feed',
      data : {
        'format' : 'html',
        'minid' : min_id,
        'feedOnly' : true,
        'nolayout' : true,
        'subject' : this.options.subject_guid,
        'getUpdate' : true        
      }
    });    
    en4.core.request.send(req, {
      'element' : $('activity-feed'),
      'updateHtmlMode' : 'prepend'           
      }
    );
   
        
    
    req.addEvent('complete', function() {
      (function() {
        if( this.options.showImmediately && $('feed-update').getChildren().length > 0 ) {
          $('feed-update').setStyle('display', 'none');
          $('feed-update').empty();
          this.getFeedUpdate(this.options.next_id);
          }
        }).delay(50, this);
    }.bind(this));
    
   
   
   // Start LOCAL STORAGE STUFF   
   if(localStorage) {
     var pageTitle = document.title;
     //@TODO Refill Locally Stored Activity Feed
     
     // For each activity-item, get the item ID number Data attribute and add it to an array
     var feed  = document.getElementById('activity-feed');
     // For every <li> in Feed, get the Feed Item Attribute and add it to an array
     var items = feed.getElementsByTagName("li");
     var itemObject = { };
     // Loop through each item in array to get the InnerHTML of each Activity Feed Item
     var c = 0;
     for (var i = 0; i < items.length; ++i) {       
       if(items[i].getAttribute('data-activity-feed-item') != null){
         var itemId = items[i].getAttribute('data-activity-feed-item');
         itemObject[c] = {id: itemId, content : document.getElementById('activity-item-'+itemId).innerHTML };
         c++;
         }
       }
     // Serialize itemObject as JSON string
     var activityFeedJSON = JSON.stringify(itemObject);    
     localStorage.setItem(pageTitle+'-activity-feed-widget', activityFeedJSON);    
   }
   
   
   // Reconstruct JSON Object, Find Highest ID
   if(localStorage.getItem(pageTitle+'-activity-feed-widget')) {
     var storedFeedJSON = localStorage.getItem(pageTitle+'-activity-feed-widget');
     var storedObj = eval ("(" + storedFeedJSON + ")");
     
     //alert(storedObj[0].id); // Highest Feed ID
    // @TODO use this at min_id when fetching new Activity Feed Items
   }
   // END LOCAL STORAGE STUFF
   
  
   return req;  
  },

  getFeedUpdate : function(last_id){
    if( en4.core.request.isRequestActive() ) return;
    var min_id = this.options.last_id + 1;
    this.options.last_id = last_id;
    document.title = this.title;
    var req = new Request.HTML({
      url : en4.core.baseUrl + 'widget/index/name/ynmobileview.mobi-feed',
      data : {
        'format' : 'html',
        'minid' : min_id,
        'feedOnly' : true,
        'nolayout' : true,
        'getUpdate' : true,
        'subject' : this.options.subject_guid
      }
    });
    en4.core.request.send(req, {
      'element' : $('activity-feed'),
      'updateHtmlMode' : 'prepend'
    });
    return req;
  },

  loop : function() {
    this._log('activity update loop start');
    
    if( !this.state ) {
      this.loop.delay(this.options.delay, this);
      return;
    }

    try {
      this.checkFeedUpdate().addEvent('complete', function() {
        try {
          this._log('activity loop req complete');
          this.loop.delay(this.options.delay, this);
        } catch( e ) {
          this.loop.delay(this.options.delay, this);
          this._log(e);
        }
      }.bind(this));
    } catch( e ) {
      this.loop.delay(this.options.delay, this);
      this._log(e);
    }
    
    this._log('activity update loop stop');
  },
  
  // Utility
  _log : function(object) {
    if( !this.options.debug ) {
      return;
    }

    try {
      if( 'console' in window && typeof(console) && 'log' in console ) {
        //console.log(object);
      }
    } catch( e ) {
      // Silence
    }
  }
});
})(); // END NAMESPACE