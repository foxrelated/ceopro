<?php if( (!empty($this->feedOnly) || !$this->endOfFeed ) &&
    (empty($this->getUpdate) && empty($this->checkUpdate)) ): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      
      var activity_count = <?php echo sprintf('%d', $this->activityCount) ?>;
      var next_id = <?php echo sprintf('%d', $this->nextid) ?>;
      var subject_guid = '<?php echo $this->subjectGuid ?>';
      var endOfFeed = <?php echo ( $this->endOfFeed ? 'true' : 'false' ) ?>;

      var activityViewMore = window.activityViewMore = function(next_id, subject_guid) {
        if( en4.core.request.isRequestActive() ) return;
        
        var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';         
        $('feed_viewmore').style.display = 'none';
        $('feed_loading').style.display = '';
        
          var request = new Request.HTML({
          url : url,
          data : {
            format : 'html', 
            'maxid' : next_id,
            'feedOnly' : true,
            'nolayout' : true,
            'subject' : subject_guid
          },
          evalScripts : true,
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            Elements.from(responseHTML).inject($('activity-feed'));
            en4.core.runonce.trigger();
            Smoothbox.bind($('activity-feed'));
          }
        });
       request.send();
      }
      
      if( next_id > 0 && !endOfFeed ) {
        $('feed_viewmore').style.display = '';
        $('feed_loading').style.display = 'none';
        $('feed_viewmore_link').removeEvents('click').addEvent('click', function(event){
          event.stop();
          activityViewMore(next_id, subject_guid);
        });
      } else {
        $('feed_viewmore').style.display = 'none';
        $('feed_loading').style.display = 'none';
      }
      
    });
  </script>
<?php endif; ?>
<?php if( empty($this->feedOnly) && empty($this->checkUpdate)): 
$session = new Zend_Session_Namespace('mobile');
$session->count = 0;?> 
<script type="text/javascript">
	 $('global_wrapper').addClass('ynmb_feed_background');
</script>
<div class="ynmb_NewsFeed">
<?php endif;?>
	<?php if( $this->enableComposer && (empty($this->feedOnly) && empty($this->checkUpdate))): ?>
	<div class="ynmb_composer">
		<?php $request = Zend_Controller_Front::getInstance() -> getRequest();
		$controller = $request -> getControllerName();
		$action = $request -> getActionName();
		if($action == 'userhome' && $controller == 'index'):?>
		<div class="ynmb_composer_linksTitle">
			<table class="ynmb_composer_table">
				<tbody>
					<tr>
						<td>
							<div>
								<a href="" class="ynmb_composerLinks">
									<i class="ynmb_composerIcon ynmb_statusLink"></i>
									<span class="ynmb_composerLink_text ynmb_composerLink_active"><?php echo $this->translate("Status") ?></span>
								</a>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php endif;?>
		<div class="ynmb_composer_post">
			<i class="ynmb_composer_arrow"></i>
			<form method="post" action="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'post'), 'default', true) ?>" class="activity ynmb_composerStatus" enctype="application/x-www-form-urlencoded" id="activity-form">
			      <textarea id="activity_body" cols="1" rows="1" name="body" placeholder="<?php echo $this->escape($this->translate("What's on your mind?")) ?>"></textarea>
			      <input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
			      <?php if( $this->viewer() && $this->subject() && !$this->viewer()->isSelf($this->subject())): ?>
			        <input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
			      <?php endif; ?>
			      <?php if( $this->formToken ): ?>
			        <input type="hidden" name="token" value="<?php echo $this->formToken ?>" />
			      <?php endif ?>
			      <div id="compose-menu" class="compose-menu" style="display: none">
			        <button id="compose-submit" type="submit"><?php echo $this->translate("Share") ?></button>
			      </div>
		    </form>
		</div> 
	<?php
      $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
    ?>
    <script type="text/javascript">
      var composeInstance; 
      $$('.layout_ynmobileview_mobi_feed')[0].style.display = '';
      en4.core.runonce.add(function() {
        // @todo integrate this into the composer
        if( true ) {
          composeInstance = new Composer('activity_body', {
            menuElement : 'compose-menu',
            baseHref : '<?php echo $this->baseUrl() ?>',
            lang : {
              "What's on your mind?" : '<?php echo $this->string()->escapeJavascript($this->translate("What's on your mind?")) ?>'
            }
          });
        }
      });
    </script>
    <?php foreach( $this->composePartials as $partial ): ?>
      		<?php echo $this->partial($partial[0], $partial[1]) ?>
    	<?php endforeach; ?>
    </div> 
	<?php endif;?>
	<?php if( empty($this->feedOnly) && empty($this->checkUpdate)): ?>
	<div class="ynmb_feedStory_wrapper">
	<?php endif;?>
		<?php $activity_moderate = "";
		$group_owner ="";
		$group = "";
		try
		{
			if( Engine_Api::_()->core()->hasSubject('group') ) 
			{
		    	$group = Engine_Api::_()->core()->getSubject('group');   
			} 
		}
		catch( Exception $e)
		{      
		}
		if ($group) 
		{
			 $table = NULL;
			 if(Engine_Api::_()->hasModuleBootstrap("advgroup"))
			 {
			    $table = Engine_Api::_()->getDbtable('groups', 'advgroup');
			 }
			 else 
			 {
				  $table = Engine_Api::_()->getDbtable('groups', 'group');
			 }
		    $select = $table->select()
		         ->where('group_id = ?', $group->getIdentity())
		         ->limit(1);
		
		    $row = $table->fetchRow($select);
		    $group_owner = $row['user_id'];
		}
		if($this->viewer->getIdentity())
		{
		     $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $this->viewer->level_id, 'activity');
		}
		$data =  array(
			  'action_id' => $this->action_id,
			  'getUpdate' => $this->getUpdate,
		      'actions' => $this->activity,
		      'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
		      'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
		      'activity_group' => $group_owner,
		      'activity_moderate' =>$activity_moderate
		    );
		if( !empty($this->feedOnly) && empty($this->checkUpdate)): // Simple feed only for AJAX
		  if( null == $this->activity || (!is_array($this->activity) && !($this->activity instanceof Zend_Db_Table_Rowset_Abstract)) ) 
		  {
		      return;
		  }
		  echo $this->partial('_activityText.tpl', 'ynmobileview', $data);
		  return; // Do no render the rest of the script in this mode
		endif; ?>
		
		<?php if( !empty($this->checkUpdate) ): // if this is for the live update
		  if ($this->activityCount)
		  echo "<script type='text/javascript'>
		          document.title = '($this->activityCount) ' + activityUpdateHandler.title;
		          activityUpdateHandler.options.next_id = ".$this->firstid.";
		        </script>
		
		        <div class='tip'>
		          <span>
		            <a href='javascript:void(0);' onclick='javascript:activityUpdateHandler.getFeedUpdate(".$this->firstid.");$(\"feed-update\").empty();'>
		              {$this->translate(array(
		                  '%d new update is available - click this to show it.',
		                  '%d new updates are available - click this to show them.',
		                  $this->activityCount),
		                $this->activityCount)}
		            </a>
		          </span>
		        </div>";
		  return; // Do no render the rest of the script in this mode
		endif; ?>
		
		<?php if( !empty($this->getUpdate) ): // if this is for the get live update ?>
		   <script type="text/javascript">
		     activityUpdateHandler.options.last_id = <?php echo sprintf('%d', $this->firstid) ?>;
		   </script>
		<?php endif; ?>
		
		<?php if ($this->updateSettings && !$this->action_id): // wrap this code around a php if statement to check if there is live feed update turned on ?>
		  <script type="text/javascript">
		    var activityUpdateHandler;
		    en4.core.runonce.add(function() {
		      try {
		          activityUpdateHandler = new YnmobiUpdateHandler({
		            'baseUrl' : en4.core.baseUrl,
		            'basePath' : en4.core.basePath,
		            'identity' : 4,
		            'delay' : <?php echo $this->updateSettings;?>,
		            'last_id': <?php echo sprintf('%d', $this->firstid) ?>,
		            'subject_guid' : '<?php echo $this->subjectGuid ?>'
		          });
		          setTimeout("activityUpdateHandler.start()",1250);
		          window._activityUpdateHandler = activityUpdateHandler;
		      } catch( e ) {
		       
		      }
		    });
		  </script>
		<?php endif;?>
		
		<?php if( $this->post_failed == 1 ): ?>
		  <div class="tip">
		    <span>
		      <?php $url = $this->url(array('module' => 'user', 'controller' => 'settings', 'action' => 'privacy'), 'default', true) ?>
		      <?php echo $this->translate('The post was not added to the feed. Please check your %1$sprivacy settings%2$s.', '<a href="'.$url.'">', '</a>') ?>
		    </span>
		  </div>
		<?php endif; ?>
		
		<?php // If requesting a single action and it doesn't exist, show error ?>
		<?php if( !$this->activity ): ?>
		  <?php if( $this->action_id ): ?>
		    <h2><?php echo $this->translate("Activity Item Not Found") ?></h2>
		    <p>
		      <?php echo $this->translate("The page you have attempted to access could not be found.") ?>
		    </p>
		    <?php if( empty($this->feedOnly) && empty($this->checkUpdate)): ?> 
		    	</div>
		    </div>
		     <?php endif; ?>	
		  <?php return; else: ?>
		    <div class="tip">
		      <span>
		        <?php echo $this->translate("Nothing has been posted here yet - be the first!") ?>
		      </span>
		    </div>
		    <?php if( empty($this->feedOnly) && empty($this->checkUpdate)): ?> 
		    	</div>
		    </div>
		     <?php endif; ?>	
		  <?php return; endif; ?>
		<?php endif; ?>
		
		<div id="feed-update"></div>
		<?php
		echo $this->partial('_activityText.tpl', 'ynmobileview', $data); ?>
		
		<div class="feed_viewmore" id="feed_viewmore" style="display: none;">
		  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
		    'id' => 'feed_viewmore_link',
		    'class' => 'buttonlink icon_viewmore'
		  )) ?>
		</div>
		
		<div class="feed_viewmore" id="feed_loading" style="display: none;">
		  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left;margin-right: 5px;' />
		  <?php echo $this->translate("Loading ...") ?>
		</div>
	</div>
</div>

