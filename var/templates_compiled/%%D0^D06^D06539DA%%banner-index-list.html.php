<?php /* Smarty version 2.6.18, created on 2015-10-01 10:40:01
         compiled from banner-index-list.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 't', 'banner-index-list.html', 19, false),array('function', 'ox_column_class', 'banner-index-list.html', 95, false),array('function', 'ox_column_title', 'banner-index-list.html', 96, false),array('function', 'cycle', 'banner-index-list.html', 151, false),array('function', 'ox_banner_icon', 'banner-index-list.html', 167, false),array('function', 'ox_entity_id', 'banner-index-list.html', 172, false),array('function', 'ox_banner_size', 'banner-index-list.html', 246, false),array('modifier', 'count', 'banner-index-list.html', 110, false),array('modifier', 'escape', 'banner-index-list.html', 167, false),)), $this); ?>

<div class='tableWrapper'>
    <div class='tableHeader'>
        <ul class='tableActions'>
            <?php if ($this->_tpl_vars['isManager']): ?>
            <li>
                <?php if ($this->_tpl_vars['clientId'] == -1 || $this->_tpl_vars['campaignId'] == -1): ?>
                <span class='inlineIcon iconBannerAddDisabled'><?php echo OA_Admin_Template::_function_t(array('str' => 'AddBanner'), $this);?>
</span>

		  <span class='inlineIcon iconBannerAddDisabled'><?php echo OA_Admin_Template::_function_t(array('str' => 'AddMobileBanner'), $this);?>
</span>

                <?php else: ?>
                <a href='banner-edit.php?clientid=<?php echo $this->_tpl_vars['clientId']; ?>
&campaignid=<?php echo $this->_tpl_vars['campaignId']; ?>
' class='inlineIcon iconBannerAdd'><?php echo OA_Admin_Template::_function_t(array('str' => 'AddBanner'), $this);?>
</a>
           <a href='mobilebanner-edit.php?clientid=<?php echo $this->_tpl_vars['clientId']; ?>
&campaignid=<?php echo $this->_tpl_vars['campaignId']; ?>
' class='inlineIcon iconBannerAdd'>Add Mobile Banner</a>
                <?php endif; ?>
            </li>
            <?php endif; ?>
            <?php if ($this->_tpl_vars['canDelete']): ?>
            <li class='inactive activeIfSelected'>
                <a id='deleteSelection' href='#' class='inlineIcon iconDelete'><?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
</a>

                <?php echo '
                <script type=\'text/javascript\'>
                <!--

                $(\'#deleteSelection\').click(function(event) {
					event.preventDefault();

					if (!$(this).parents(\'li\').hasClass(\'inactive\')) {
						var ids = [];
						$(this).parents(\'.tableWrapper\').find(\'.toggleSelection input:checked\').each(function() {
							ids.push(this.value);
						});

						if (!tablePreferences.warningBeforeDelete || confirm("'; ?>
<?php echo OA_Admin_Template::_function_t(array('str' => 'ConfirmDeleteBanners'), $this);?>
<?php echo '")) {
    						window.location = \'banner-delete.php?clientid='; ?>
<?php echo $this->_tpl_vars['clientId']; ?>
<?php echo '&campaignid='; ?>
<?php echo $this->_tpl_vars['campaignId']; ?>
<?php echo '&bannerid=\' + ids.join(\',\');
						}
					}
                });

                //-->
                </script>
                '; ?>

            </li>
            <?php endif; ?>
        </ul>

        <ul class='tableFilters alignRight'>
            <li>
                <div class='label'>
                    Show
                </div>

                <div class='dropDown'>
                    <span><span><?php if ($this->_tpl_vars['hideinactive']): ?>Active banners<?php else: ?>All banners<?php endif; ?></span></span>

                    <div class='panel'>
                        <div>
                            <ul>
                                <li><a href='campaign-banners.php?clientid=<?php echo $this->_tpl_vars['clientId']; ?>
&campaignid=<?php echo $this->_tpl_vars['campaignId']; ?>
&hideinactive=0'>All banners</a></li>
                                <li><a href='campaign-banners.php?clientid=<?php echo $this->_tpl_vars['clientId']; ?>
&campaignid=<?php echo $this->_tpl_vars['campaignId']; ?>
&hideinactive=1'>Active banners</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class='mask'></div>
                </div>
            </li>
        </ul>

        <div class='clear'></div>
        <div class='corner left'></div>
        <div class='corner right'></div>
    </div>

    <table cellspacing='0' summary=''>
        <thead>
            <tr>
                <?php if ($this->_tpl_vars['isManager']): ?>
                <th class='first toggleAll'>
                    <input type='checkbox' />
                </th>
                <?php endif; ?>
                 <th class='<?php echo OA_Admin_Template::_function_ox_column_class(array('item' => 'name','order' => 'up','default' => 1), $this);?>
'>
                    <?php echo OA_Admin_Template::_function_ox_column_title(array('item' => 'name','order' => 'up','default' => 1,'str' => 'Name','url' => "campaign-banners.php"), $this);?>

                </th>               
                <th>
                  <?php echo OA_Admin_Template::_function_t(array('str' => 'Type'), $this);?>

                </th>  
                <th>&nbsp;

                </th>
                <th class='last alignRight'>&nbsp;

                </th>
            </tr>
        </thead>

<?php if (! count($this->_tpl_vars['from'])): ?>
        <tbody>
            <tr class='odd'>
                <td colspan='4'>&nbsp;</td>
            </tr>
            <tr class='even'>
                <td colspan='4' class="hasPanel">
                    <div class='tableMessage'>
                        <div class='panel'>

                            <?php if ($this->_tpl_vars['clientId'] != -1): ?>
                            	<?php if ($this->_tpl_vars['campaignId'] != -1): ?>
                                    <?php if ($this->_tpl_vars['hideinactive']): ?>
                                        <?php echo $this->_tpl_vars['aCount']['banners_hidden']; ?>
 <?php echo OA_Admin_Template::_function_t(array('str' => 'InactiveBannersHidden'), $this);?>

                                    <?php else: ?>
                                        <?php echo OA_Admin_Template::_function_t(array('str' => 'NoBanners'), $this);?>

                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php echo OA_Admin_Template::_function_t(array('str' => 'NoBannersAddCampaign','values' => $this->_tpl_vars['clientId']), $this);?>

                                <?php endif; ?>
                            <?php else: ?>
                                <?php echo OA_Admin_Template::_function_t(array('str' => 'NoBannersAddAdvertiser'), $this);?>

                            <?php endif; ?>

                            <div class='corner top-left'></div>
                            <div class='corner top-right'></div>
                            <div class='corner bottom-left'></div>
                            <div class='corner bottom-right'></div>
                        </div>
                    </div>

                    &nbsp;
                </td>
            </tr>
            <tr class='odd'>
                <td colspan='4'>&nbsp;</td>
            </tr>
      </tbody>

<?php else: ?>
        <tbody>
    <?php echo smarty_function_cycle(array('name' => 'bgcolor','values' => "even,odd",'assign' => 'bgColor','reset' => 1), $this);?>


    <?php $_from = $this->_tpl_vars['from']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['bannerId'] => $this->_tpl_vars['banner']):
?>
<!--=======================================================Modified By DAC021===============================================================-->
  <?php if ($this->_tpl_vars['banner']['masterbanner'] == -1 || $this->_tpl_vars['banner']['masterbanner'] == -2 || $this->_tpl_vars['banner']['masterbanner'] == -3 || $this->_tpl_vars['banner']['masterbanner'] == -4): ?>
<!--=======================================================Modified By DAC021===============================================================-->
        <?php echo smarty_function_cycle(array('name' => 'bgcolor','assign' => 'bgColor'), $this);?>

            <tr class='<?php echo $this->_tpl_vars['bgColor']; ?>
'>
                <?php if ($this->_tpl_vars['isManager']): ?>
                <td class='toggleSelection'>
                    <input type='checkbox' value='<?php echo $this->_tpl_vars['bannerId']; ?>
' />
                </td>
                <?php endif; ?>
                <td>
                  <?php if ($this->_tpl_vars['canEdit']): ?>
		 	      
                      <a href='banner-edit.php?clientid=<?php echo $this->_tpl_vars['clientId']; ?>
&campaignid=<?php echo $this->_tpl_vars['campaignId']; ?>
&bannerid=<?php echo $this->_tpl_vars['bannerId']; ?>
' class='inlineIcon <?php echo OA_Admin_Template::_function_ox_banner_icon(array('type' => $this->_tpl_vars['banner']['type'],'active' => $this->_tpl_vars['banner']['active']), $this);?>
'><?php echo ((is_array($_tmp=$this->_tpl_vars['banner']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>
				
                    <?php else: ?>
                      <span class='inlineIcon <?php echo OA_Admin_Template::_function_ox_banner_icon(array('type' => $this->_tpl_vars['banner']['type'],'active' => $this->_tpl_vars['banner']['active']), $this);?>
'><?php echo ((is_array($_tmp=$this->_tpl_vars['banner']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</span>
                    <?php endif; ?>
                  <?php echo OA_Admin_Template::_function_ox_entity_id(array('type' => 'Banner','id' => $this->_tpl_vars['bannerId']), $this);?>
 
                </td>
                
                
               <!--extra type-->
                <td>
                <?php if ($this->_tpl_vars['banner']['mobitype'] == mobile): ?>
                
                      Mobile  Banner
                      
                 <?php endif; ?>

		<?php if ($this->_tpl_vars['banner']['mobitype'] == html): ?>
                
                      Mobile HTML Banner
                      
                 <?php endif; ?>

                 <?php if ($this->_tpl_vars['banner']['mobitype'] == ''): ?>
                
                      Mobile Text Banner
                      
                 <?php endif; ?>  
                <?php if ($this->_tpl_vars['banner']['mobitype'] == web): ?>
                
                      Web  Banner
                      
                 <?php endif; ?>                                
                </td>                
                
                
                <td class='alignRight verticalActions'>
                    <ul class='rowActions'>
<!--=======================================================Modified By DAC021===============================================================-->
		<?php if ($this->_tpl_vars['banner']['mobitype'] == web): ?>
<!--=======================================================Modified By DAC021===============================================================-->
                      <?php if ($this->_tpl_vars['canACL']): ?>
                        <li>
                            <a href='banner-acl.php?clientid=<?php echo $this->_tpl_vars['clientId']; ?>
&campaignid=<?php echo $this->_tpl_vars['campaignId']; ?>
&bannerid=<?php echo $this->_tpl_vars['bannerId']; ?>
&mobitype=web' class='inlineIcon iconBannerApplyLimitations'><?php echo OA_Admin_Template::_function_t(array('str' => 'ACL'), $this);?>
 </a>
                        </li>
                        <?php endif; ?>
<!--=======================================================Modified By DAC021===============================================================-->
		<?php else: ?>
                      <?php if ($this->_tpl_vars['canACL']): ?>
                        <li>
                             <a href='banner-acl.php?clientid=<?php echo $this->_tpl_vars['clientId']; ?>
&campaignid=<?php echo $this->_tpl_vars['campaignId']; ?>
&bannerid=<?php echo $this->_tpl_vars['bannerId']; ?>
&mobitype=web' class='inlineIcon iconBannerApplyLimitations'><?php echo OA_Admin_Template::_function_t(array('str' => 'ACL'), $this);?>
</a>
                        </li>
                        
   		<?php if ($this->_tpl_vars['banner']['mobitype'] == mobile): ?>                     
                        <li>
                            <a href='campaign-mobilebanner.php?clientid=<?php echo $this->_tpl_vars['clientId']; ?>
&campaignid=<?php echo $this->_tpl_vars['campaignId']; ?>
&bannerid=<?php echo $this->_tpl_vars['bannerId']; ?>
' class='inlineIcon iconBanners'>Mapping Mobile Banners</a>
                        </li>
                 <?php endif; ?>                        
                        <?php endif; ?>
		<?php endif; ?>
<!--=======================================================Modified By DAC021===============================================================-->
                        <?php if (! $this->_tpl_vars['banner']['active'] && $this->_tpl_vars['canActivate']): ?>
                        <li>
                            <a href='banner-activate.php?clientid=<?php echo $this->_tpl_vars['clientId']; ?>
&campaignid=<?php echo $this->_tpl_vars['campaignId']; ?>
&bannerid=<?php echo $this->_tpl_vars['bannerId']; ?>
&value=1' class='inlineIcon iconActivate'><?php echo OA_Admin_Template::_function_t(array('str' => 'Activate'), $this);?>
</a>
                        </li>
                        <?php endif; ?>
                        <?php if ($this->_tpl_vars['banner']['active'] && $this->_tpl_vars['canDeactivate']): ?>
                        <li>
                            <a href='banner-activate.php?clientid=<?php echo $this->_tpl_vars['clientId']; ?>
&campaignid=<?php echo $this->_tpl_vars['campaignId']; ?>
&bannerid=<?php echo $this->_tpl_vars['bannerId']; ?>
&value=0' class='inlineIcon iconDeactivate'><?php echo OA_Admin_Template::_function_t(array('str' => 'Deactivate'), $this);?>
</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </td>
                <td class='hasPanel'>
                    <div class='panel'>
                        <table cellspacing='0' summary=''>
                            <?php if ($this->_tpl_vars['banner']['type'] != 'txt'): ?>
                            <tr>
                                <th><?php echo OA_Admin_Template::_function_t(array('str' => 'Size'), $this);?>
</th>
                                <td><?php echo OA_Admin_Template::_function_ox_banner_size(array('width' => $this->_tpl_vars['banner']['width'],'height' => $this->_tpl_vars['banner']['height']), $this);?>
</td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th><?php echo OA_Admin_Template::_function_t(array('str' => 'Url'), $this);?>
</th>
                                <td><?php echo $this->_tpl_vars['banner']['url']; ?>
</td>
                            </tr>
                            <tr>
                                <th><?php echo OA_Admin_Template::_function_t(array('str' => 'Weight'), $this);?>
</th>
                                <td><?php echo $this->_tpl_vars['banner']['weight']; ?>
</td>
                            </tr>
                        </table>

                        <div class='corner top-left'></div>
                        <div class='corner top-right'></div>
                        <div class='corner bottom-left'></div>
                        <div class='corner bottom-right'></div>
                    </div>
                </td>
            </tr>
<?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
       </tbody>
<?php endif; ?>
    </table>
</div>