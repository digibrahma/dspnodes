<?php /* Smarty version 2.6.18, created on 2015-10-10 10:40:55
         compiled from affiliate-mobilezone-index-list.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'ox_column_class', 'affiliate-mobilezone-index-list.html', 55, false),array('function', 'ox_column_title', 'affiliate-mobilezone-index-list.html', 56, false),array('function', 't', 'affiliate-mobilezone-index-list.html', 62, false),array('function', 'cycle', 'affiliate-mobilezone-index-list.html', 102, false),array('function', 'ox_zone_icon', 'affiliate-mobilezone-index-list.html', 112, false),array('function', 'ox_entity_id', 'affiliate-mobilezone-index-list.html', 116, false),array('function', 'ox_zone_size', 'affiliate-mobilezone-index-list.html', 119, false),array('modifier', 'count', 'affiliate-mobilezone-index-list.html', 70, false),array('modifier', 'escape', 'affiliate-mobilezone-index-list.html', 112, false),array('modifier', 'default', 'affiliate-mobilezone-index-list.html', 122, false),)), $this); ?>


<div class='tableWrapper'>
    <div class='tableHeader'>
        <ul class='tableActions'>
            <?php if ($this->_tpl_vars['canAdd']): ?>
            <li>
                <?php if ($this->_tpl_vars['affiliateId'] != -1): ?>
                <a href='mobilezone-edit.php?affiliateid=<?php echo $this->_tpl_vars['affiliateId']; ?>
' class='inlineIcon iconZoneAdd'>Add New Mobile Zone</a>
                <?php else: ?>
                <span class='inlineIcon iconZoneAddDisabled'>Add New Mobile Zone</span>
                <?php endif; ?>
            </li>
            <?php endif; ?>
         
        </ul>

        <div class='clear'></div>
        <div class='corner left'></div>
        <div class='corner right'></div>
    </div>

    <table cellspacing='0' summary=''>
        <thead>
            <tr>
              <th class='first toggleAll'>
                  <input type='checkbox' />
                </th>
                <th class='<?php echo OA_Admin_Template::_function_ox_column_class(array('item' => 'name','order' => 'up','default' => 1), $this);?>
'>
                    <?php echo OA_Admin_Template::_function_ox_column_title(array('item' => 'name','order' => 'up','default' => 1,'str' => 'Name','url' => "affiliate-mobilezones.php"), $this);?>

                </th>
                <th class='<?php echo OA_Admin_Template::_function_ox_column_class(array('item' => 'size','order' => 'up','default' => 0), $this);?>
'>
                    <?php echo OA_Admin_Template::_function_ox_column_title(array('item' => 'size','order' => 'up','default' => 0,'str' => 'Size','url' => "affiliate-mobilezones.php"), $this);?>

                </th>
                <th>
                  <?php echo OA_Admin_Template::_function_t(array('str' => 'Description'), $this);?>

                </th>
                <th class='last alignRight'>&nbsp;
                  
                </th>
            </tr>
        </thead>

<?php if (! count($this->_tpl_vars['from'])): ?>
        <tbody>
            <tr class='odd'>
                <td colspan='5'>&nbsp;</td>
            </tr>
            <tr class='even'>
                <td colspan='5' class="hasPanel">
                    <div class='tableMessage'>
                        <div class='panel'>
                            <?php if ($this->_tpl_vars['affiliateId'] != -1): ?>
                                There are Currently No Mobile Zones Available for this Website
                            <?php else: ?>
                                <?php echo OA_Admin_Template::_function_t(array('str' => 'NoMobileZonesAddWebsite'), $this);?>

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
                <td colspan='5'>&nbsp;</td>
            </tr>
        </tbody>
        
<?php else: ?>
        <tbody>
    <?php echo smarty_function_cycle(array('name' => 'bgcolor','values' => "even,odd",'assign' => 'bgColor','reset' => 1), $this);?>

    <?php $_from = $this->_tpl_vars['from']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['zoneId'] => $this->_tpl_vars['zone']):
?>
<?php if ($this->_tpl_vars['zone']['masterzone'] != -1 && $this->_tpl_vars['zone']['masterzone'] != -2 && $this->_tpl_vars['zone']['masterzone'] != -3 && $this->_tpl_vars['zone']['masterzone'] != -4): ?>
        <?php echo smarty_function_cycle(array('name' => 'bgcolor','assign' => 'bgColor'), $this);?>

            <tr class='<?php echo $this->_tpl_vars['bgColor']; ?>
'>
              <td class='toggleSelection'>
                  <input type='checkbox' value='<?php echo $this->_tpl_vars['zoneId']; ?>
' />
                </td>
                <td>
                  <?php if ($this->_tpl_vars['canEdit']): ?>
                      <a href='mobilezone-edit.php?affiliateid=<?php echo $this->_tpl_vars['affiliateId']; ?>
&zoneid=<?php echo $this->_tpl_vars['zoneId']; ?>
' class='inlineIcon <?php echo OA_Admin_Template::_function_ox_zone_icon(array('delivery' => $this->_tpl_vars['zone']['delivery'],'active' => $this->_tpl_vars['zone']['active'],'warning' => $this->_tpl_vars['zone']['lowPriorityWarning']), $this);?>
'><?php echo ((is_array($_tmp=$this->_tpl_vars['zone']['zonename'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>
                    <?php else: ?>
                      <span class='inlineIcon <?php echo OA_Admin_Template::_function_ox_zone_icon(array('delivery' => $this->_tpl_vars['zone']['delivery'],'active' => $this->_tpl_vars['zone']['active'],'warning' => $this->_tpl_vars['zone']['lowPriorityWarning']), $this);?>
'><?php echo ((is_array($_tmp=$this->_tpl_vars['zone']['zonename'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</span>
                    <?php endif; ?>
                  <?php echo OA_Admin_Template::_function_ox_entity_id(array('type' => 'Zone','id' => $this->_tpl_vars['zoneId']), $this);?>
 
                </td>
                <td>
                    <?php echo OA_Admin_Template::_function_ox_zone_size(array('width' => $this->_tpl_vars['zone']['width'],'height' => $this->_tpl_vars['zone']['height'],'delivery' => $this->_tpl_vars['zone']['delivery']), $this);?>

                </td>
                <td>
                    <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['zone']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')))) ? $this->_run_mod_handler('default', true, $_tmp, '&nbsp;') : smarty_modifier_default($_tmp, '&nbsp;')); ?>

                </td>
                <td class='alignRight'>
                    <ul class='rowActions'>

                       
                    </ul>
                </td>
            </tr><?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
       </tbody>
<?php endif; ?>
    </table>
</div>