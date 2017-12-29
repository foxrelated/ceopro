<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     LuanND
 */
?>
<link type="text/css" href="application/modules/Ynforum/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<script src="application/modules/Ynforum/externals/scripts/jquery-1.7.1.min.js"></script>
<script src="application/modules/Ynforum/externals/scripts/jquery-ui-1.8.17.custom.min.js"></script>
<script type = "text/javascript">
    var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
    var changeOrder = function(order, default_direction)
    {
        // Just change direction
        if( order == currentOrder ) 
        {
            $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
        } else {
            $('order').value = order;
            $('direction').value = default_direction;
        }
        $('filter_form').submit();
    }
    jQuery.noConflict();
    jQuery(document).ready(function()
    {
        var start_date_show = false;
        var end_date_show = false;
    	var ynEventCalendar= {        
    	        currentText: '<?php echo $this->string()->escapeJavascript($this->translate('Today')) ?>',
    	        monthNames: ['<?php echo $this->string()->escapeJavascript($this->translate('January')) ?>', 
    	            '<?php echo $this->string()->escapeJavascript($this->translate('February')) ?>', 
    	            '<?php echo $this->string()->escapeJavascript($this->translate('March')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('April')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('June')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('July')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('August')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('September')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('October')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('November')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('December')) ?>',
    	        ],
    	        monthNamesShort: ['<?php echo $this->string()->escapeJavascript($this->translate('Jan')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Feb')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Mar')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Apr')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Jun')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Jul')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Aug')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Sep')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Oct')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Nov')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Dec')) ?>',
    	        ],
    	        dayNames: ['<?php echo $this->string()->escapeJavascript($this->translate('Sunday')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Monday')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Tuesday')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Wednesday')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Thursday')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Friday')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Saturday')) ?>',            
    	        ],
    	        dayNamesShort: ['<?php echo $this->translate('Su') ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
    	            ],
    	        dayNamesMin: ['<?php echo $this->string()->escapeJavascript($this->translate('Su')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
    	            '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
    	            ],        
    	        firstDay: 0,
    	        //isRTL:yneventIsRightToLeft,
    	        isRTL: <?php echo $this->layout()->orientation == 'right-to-left'? 'true':'false' ?>,
    	        showMonthAfterYear: false,
    	        yearSuffix: ''
    	};
			
        // Datepicker
        jQuery('#start_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynforum/externals/images/calendar.png',
            buttonImageOnly: true
        });
        jQuery('#end_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynforum/externals/images/calendar.png',
            buttonImageOnly: true
        });
        jQuery('#start_date').click(function (){
            jQuery('#start_date').datepicker();
            if (start_date_show)
                jQuery('#start_date').datepicker('hide');
            else
                jQuery('#start_date').datepicker('show');
            start_date_show = !start_date_show;
        });
        jQuery('#end_date').click(function (){
            jQuery('#end_date').datepicker();
            if (end_date_show)
                jQuery('#end_date').datepicker('hide');
            else
                jQuery('#end_date').datepicker('show');
            end_date_show = !end_date_show;
        });
    });	
    function multiDelete()
    {
		var checkboxes = $$('td.checksub input[type=checkbox]:checked')[0];
		
		if (!checkboxes) {
		  alert("Please select a item to delete.");
		  return false;
		}
        return confirm("<?php echo $this->translate('Are you sure you want to delete the selected icons?'); ?>");
    }

    en4.core.runonce.add(function()
    {
		$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ 
			$$('td.checksub input[type=checkbox]').each(function(i){
	 			i.checked = $$('th.admin_table_short input[type=checkbox]')[0].checked;
			});
		});
		$$('td.checksub input[type=checkbox]').addEvent('click', function(){
			var checks = $$('td.checksub input[type=checkbox]');
			var flag = true;
			for (i = 0; i < checks.length; i++) {
				if (checks[i].checked == false) {
					flag = false;
				}
			}
			if (flag) {
				$$('th.admin_table_short input[type=checkbox]')[0].checked = true;
			}
			else {
				$$('th.admin_table_short input[type=checkbox]')[0].checked = false;
			}
		});
	});
</script>





<h2><?php echo $this->translate("Forums Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<div class='admin_search'>   
    <?php echo $this -> form -> render($this); ?>
</div>

<br />
<?php if (count($this->paginator)): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this -> url(); ?>" onSubmit="return multiDelete()">
        <div class="table_scroll">
            <table class='admin_table'>
                <thead>
                    <tr>
                        <th class='admin_table_short'><input  type='checkbox' class='checkbox' /></th>                       
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'DESC');">
                                <?php echo $this->translate("Post/Topic Title") ?>
                            </a>
                        </th>
                        <th>
                            <?php echo $this->translate("Content") ?>
                        </th>
                         <th>
                            <?php echo $this->translate("Status") ?>
                        </th>
                        <th>
                            
                            <?php echo $this->translate("Creator") ?>
                            
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'DESC');">
                                <?php echo $this->translate("Views") ?>
                            </a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');">
                                <?php echo $this->translate("Date") ?>
                            </a>
                        </th>                       
                        <th>

                            <?php echo $this->translate("Options") ?>

                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->paginator as $item): ?>
                         <tr>
                         	<td class="checksub"><input type='checkbox' class='checkbox' name='delete_<?php echo $item -> getIdentity(); ?>' value='<?php echo $item -> getIdentity(); ?>' /></td>
                            
                            <td><?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($item->getTitle(), 20))  ?></td>
                            <td><?php echo $this->string()->truncate($this->string()->stripTags($item->body), 200)  ?></td>
                            <td><?php
								if ($item -> approved == 1) {
									echo 'Approved';
								} elseif ($item -> approved == 0) {
									echo 'Pendding';
								} else {
									echo 'Denied';
								}
                            ?>
                            </td>
                            <td><?php echo $this -> htmlLink($item -> getOwner() -> getHref(), $item -> getOwner() -> getTitle()); ?></td>
                            <td><?php echo $item->view_count; ?></td>
                            <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
                            <td>
                                 
                                
                                <a href="<?php echo $this->url(array('topic_id' => $item->getIdentity()), 'ynforum_topic') ?>">
                                    <?php echo $this->translate("view") ?>
                                </a>   
                                                             
                                <?php if ($item -> approved != 1):?>
                                	|
                                	<?php echo $this->htmlLink(array('route' => 'ynforum_post', 'action' => 'approve', 'post_id' => $item->getIdentity(), 'admin' => 1), $this->translate('approve'), array('class' => 'smoothbox'))?>
                                
                                <?php endif;?>
                                
                                <?php if ($item -> approved != 2):?>
                                	|
                               		<?php echo $this->htmlLink(array('route' => 'ynforum_post', 'action' => 'deny', 'post_id' => $item->getIdentity(), 'admin' => 1), $this->translate('deny'), array('class' => 'smoothbox'))?>
                                 
                                <?php endif;?>
                                |
                                <?php echo $this->htmlLink(array('route' => 'ynforum_post', 'action' => 'delete', 'post_id' => $item->getIdentity(), 'admin' => 1), $this->translate('delete'), array('class' => 'smoothbox'))?>
                                
                            </td>
                         </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <br />

        <div class='buttons'>
            <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
        </div>
    </form>

    <br />

    <div>
        <?php echo $this -> paginationControl($this -> paginator); ?>
    </div>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no posts posted by your members yet.") ?>
        </span>
    </div>
<?php endif; ?>
