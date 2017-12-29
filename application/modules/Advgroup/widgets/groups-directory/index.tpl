 <script type="text/javascript">
 		//process paginator of widget
        en4.core.runonce.add(function(){
            <?php if (!$this->renderOne): ?>
                var anchor = $('advgroup_group_directory');
                $('directory_group_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
                $('directory_group_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

                $('directory_group_previous').removeEvents('click').addEvent('click', function(){
                    en4.core.request.send(new Request.HTML({
                        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                        data : {
                            format : 'html',
                            subject : en4.core.subject.guid,
                            page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                    	},
                    	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        	Elements.from(responseHTML).replaces(anchor);  
                        	eval(responseJavaScript);                      	
                    	}
                	}));
                });

                $('directory_group_next').removeEvents('click').addEvent('click', function(){
                    en4.core.request.send(new Request.HTML({
                        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                        data : {
                            format : 'html',
                            subject : en4.core.subject.guid,
                            page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                        },
                        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        	Elements.from(responseHTML).replaces(anchor);
                        	eval(responseJavaScript);                      	
                    	}
                    })); 
                });            
            <?php endif; ?>
        });
 		
        window.addEvent('domready', function() {
     	   $$('.advgroup-group-sub-group').set('styles', {
     	        display : 'none'
     	    });
     	   $$('.advgroup-group-collapse-control').addEvent('click', function(event) {
     	        var row = this.getParent('li');
     	        var rowSubCategories = row.getAllNext('li');
     	        if (this.hasClass('advgroup-group-collapsed')) {
     	            this.removeClass('advgroup-group-collapsed');
     	            this.addClass('advgroup-group-no-collapsed')
     	            for(i = 0; i < rowSubCategories.length; i++) {
     	                if (!rowSubCategories[i].hasClass('advgroup-group-sub-group'))
                      {
     	                    break;
     	                } 
                      else
                      {
     	                    rowSubCategories[i].set('styles', {
     	                        display : 'block'
     	                    });
     	                }
     	            }
     	        } else {
     	            this.removeClass('advgroup-group-no-collapsed');
     	            this.addClass('advgroup-group-collapsed');
     	            for(i = 0; i < rowSubCategories.length; i++) {
     	                if (!rowSubCategories[i].hasClass('advgroup-group-sub-group'))
                      {
     	                    break;
     	                }
                      else
                      {
     	                    rowSubCategories[i].set('styles', {
     	                        display : 'none'
     	                    });
     	                }
     	            }
     	        }
     	    }); 
    	});
    </script>
<div id = 'advgroup_group_directory'>
<ul class="generic_list_widget"  style="padding-bottom:0px;">
<?php if( count($this->paginator) > 0 ): ?>  
	<?php foreach ($this->paginator as $group): ?>
            <li class="advgroup_group_row">
	                <?php if(count($group->getAllSubGroups()) > 0) : ?>
	                       <span class="advgroup-group-collapse-control advgroup-group-collapsed"></span>
	                <?php else : ?>
	                       <span class="advgroup-group-collapse-nocontrol"></span>
	                <?php endif; ?>
	                <div class="advgroup_info">
						<b><?php echo $group; ?></b>	
						<?php echo $this->translate('led by');?>
						<?php echo $group->getOwner();?>   
						-
						<?php echo $this->translate(array('%s member', '%s members', $group->member_count), $this->locale()->toNumber($group->member_count)) ?>
						,
						<?php echo $this->translate('last updated: %s', $this->timestamp($group->modified_date)) ?>   
					</div>
					<div class="advgroup_description"> 
						<span class="advgroup-group-collapse-nocontrol"></span>
						<?php echo Engine_Api::_()->advgroup()->subPhrase(strip_tags($group->description),75);?>
					</div>
            </li>
           	<?php foreach ($group->getAllSubGroups() as $subGroup) : ?>
                <li class="advgroup-group-sub-group" >
                    <div class="advgroup_info">
						<b><?php echo $subGroup; ?></b>	
						<?php echo $this->translate('led by');?>
						<?php echo $subGroup->getOwner();?>   
						-
						<?php echo $this->translate(array('%s member', '%s members', $subGroup->member_count), $this->locale()->toNumber($subGroup->member_count)) ?>
						,
						<?php echo $this->translate('last updated: %s', $this->timestamp($subGroup->modified_date)) ?>   
					</div>
					<div class="advgroup_description"> 
						<?php echo Engine_Api::_()->advgroup()->subPhrase(strip_tags($subGroup->description),75);?>
					</div>
                </li>
            <?php endforeach ?>
        <?php endforeach; ?>
</ul>
<!-- Paginator previous & next  -->
        <div id="directory_group_previous" class="paginator_previous">
            <?php
            	echo $this->htmlLink('javascript:void(0)', $this->translate('Previous'), array(
            		'onclick' => '',
            		'class'   => 'buttonlink icon_previous'
            	));
            ?>
        </div>
        <div id="directory_group_next" class="paginator_next">
            <?php
            	echo $this->htmlLink('javascript:void(0)' ,$this->translate('Next'), array(
            		'onclick' => '',
            		'class'   => 'buttonlink icon_next'
            	));
            ?>
        </div>
        <div class="clear"></div>
<?php endif; ?>
</div>

