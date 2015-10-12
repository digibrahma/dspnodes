<?php /* Smarty version 2.6.18, created on 2015-10-05 13:50:01
         compiled from admin-search.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'admin-search.html', 22, false),array('function', 't', 'admin-search.html', 25, false),array('function', 'cycle', 'admin-search.html', 67, false),array('function', 'rv_add_session_token', 'admin-search.html', 92, false),array('function', 'phpAds_DelConfirm', 'admin-search.html', 92, false),array('function', 'oa_icon', 'admin-search.html', 135, false),)), $this); ?>

<!-- Top -->
<br />

<!-- Search selection -->
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
    <tr><td width='20'>&nbsp;</td><td>

    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
        <form name='searchselection' action='admin-search.php'>
        <input type='hidden' name='keyword' value="<?php echo ((is_array($_tmp=$this->_tpl_vars['keyword'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
        <tr>
            <td nowrap><input type='checkbox' id='client' name='client' value='t'<?php if ($this->_tpl_vars['client']): ?> checked="checked"<?php endif; ?> onClick='this.form.submit()'>
                <label for='client'><?php echo OA_Admin_Template::_function_t(array('str' => 'Clients'), $this);?>
</label>&nbsp;&nbsp;&nbsp;</td>
            <td nowrap><input type='checkbox' id='campaign' name='campaign' value='t'<?php if ($this->_tpl_vars['campaign']): ?> checked="checked"<?php endif; ?> onClick='this.form.submit()'>
                <label for='campaign'><?php echo OA_Admin_Template::_function_t(array('str' => 'Campaign'), $this);?>
</label>&nbsp;&nbsp;&nbsp;</td>
            <td nowrap><input type='checkbox' id='banner' name='banner' value='t'<?php if ($this->_tpl_vars['banner']): ?> checked="checked"<?php endif; ?> onClick='this.form.submit()'>
                <label for='banner'><?php echo OA_Admin_Template::_function_t(array('str' => 'Banners'), $this);?>
</label>&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
            <td nowrap><input type='checkbox' id='affiliate' name='affiliate' value='t'<?php if ($this->_tpl_vars['affiliate']): ?> checked="checked"<?php endif; ?> onClick='this.form.submit()'>
                <label for='affiliate'><?php echo OA_Admin_Template::_function_t(array('str' => 'Affiliates'), $this);?>
</label>&nbsp;&nbsp;&nbsp;</td>
            <td nowrap><input type='checkbox' id='zone' name='zone' value='t'<?php if ($this->_tpl_vars['zone']): ?> checked="checked"<?php endif; ?> onClick='this.form.submit()'>
                <label for='zone'><?php echo OA_Admin_Template::_function_t(array('str' => 'Zones'), $this);?>
</label>&nbsp;&nbsp;&nbsp;</td>
            <td width='100%'>&nbsp;</td>
            <td nowrap align='right'><input type='checkbox' id='compact' name='compact' value='t'<?php if ($this->_tpl_vars['compact']): ?> checked="checked"<?php endif; ?> onClick='this.form.submit()'>
                <label for='compact'><?php echo OA_Admin_Template::_function_t(array('str' => 'Compact'), $this);?>
</label>&nbsp;&nbsp;&nbsp;</td>
        </tr>
        </form>
    </table>

    </td><td width='20'>&nbsp;</td></tr>
</table>

<!-- Seperator -->
<img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/break-el.gif' height='1' width='100%' vspace='5'>
<br /><br />

<!-- Search Results -->
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
<tr><td width='20'>&nbsp;</td><td>

<?php if ($this->_tpl_vars['matchesFound']): ?>
<table width='100%' border='0' align='center' cellspacing='0' cellpadding='0'>
    <tr height='25'>
        <td height='25'><b>&nbsp;&nbsp;<?php echo OA_Admin_Template::_function_t(array('str' => 'Name'), $this);?>
</b></td>
        <td height='25'><b><?php echo OA_Admin_Template::_function_t(array('str' => 'ID'), $this);?>
</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td height='25'>&nbsp;</td>
        <td height='25'>&nbsp;</td>
        <td height='25'>&nbsp;</td>
    </tr>

    <tr height='1'><td colspan='6' bgcolor='#888888'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/break.gif' height='1' width='100%'></td></tr>
<?php endif; ?>

<?php echo smarty_function_cycle(array('name' => 'bgcolor','values' => "#FFFFFF,#F6F6F6",'assign' => 'bgColor'), $this);?>

<?php $this->assign('addSeparator', 0); ?>
<?php $_from = $this->_tpl_vars['aClients']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row_clients']):
?>
<?php if ($this->_tpl_vars['addSeparator']): ?>
    <tr height='1'><td colspan='6' bgcolor='#888888'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/break-l.gif' height='1' width='100%'></td></tr>
<?php else: ?>
    <?php $this->assign('addSeparator', 1); ?>
<?php endif; ?>
    <tr height='25' bgcolor="<?php echo smarty_function_cycle(array('name' => 'bgcolor'), $this);?>
">

        <td height='25'>
            &nbsp;&nbsp;
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-advertiser.gif' align='absmiddle'>&nbsp;
            <a href='JavaScript:GoOpener("advertiser-edit.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientname'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>
        </td>

        <td height='25'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>

        <td>&nbsp;</td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-overview.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Overview'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("advertiser-campaigns.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo OA_Admin_Template::_function_t(array('str' => 'Overview'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-recycle.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("advertiser-delete.php?<?php echo OA_Admin_Template::_add_session_token(array(), $this);?>
&clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
", true)'<?php echo OA_Admin_Template::_function_phpAds_DelConfirm(array('str' => 'ConfirmDeleteClient'), $this);?>
><?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <a href='JavaScript:GoOpener("stats.php?entity=advertiser&breakdown=history&clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-statistics.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
'>&nbsp;<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
    </tr>

<?php $_from = $this->_tpl_vars['row_clients']['campaigns']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row_c_expand']):
?>
            <tr height='1'><td colspan='6' bgcolor='#888888'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/break-el.gif' height='1' width='100%'></td></tr>

            <tr height='25' bgcolor="<?php echo smarty_function_cycle(array('name' => 'bgcolor'), $this);?>
">

        <td height='25'>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-campaign.gif' align='absmiddle'>&nbsp;
            <a href='JavaScript:GoOpener("campaign-edit.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_c_expand']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_c_expand']['campaignname'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>
        </td>

        <td height='25'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_c_expand']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>

        <td>&nbsp;</td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-overview.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Overview'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("campaign-banners.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_c_expand']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo OA_Admin_Template::_function_t(array('str' => 'Overview'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-recycle.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("campaign-delete.php?<?php echo OA_Admin_Template::_add_session_token(array(), $this);?>
&clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_c_expand']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
", true)'<?php echo OA_Admin_Template::_function_phpAds_DelConfirm(array('str' => 'ConfirmDeleteCampaign'), $this);?>
><?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <a href='JavaScript:GoOpener("stats.php?entity=campaign&breakdown=history&clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_c_expand']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-statistics.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
'>&nbsp;<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
    </tr>

<?php $_from = $this->_tpl_vars['row_c_expand']['banners']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row_b_expand']):
?>
    <tr height='1'><td colspan='6' bgcolor='#888888'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/break-el.gif' height='1' width='100%'></td></tr>

    <tr height='25' bgcolor="<?php echo smarty_function_cycle(array('name' => 'bgcolor'), $this);?>
">

        <td height='25'>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <img src="<?php echo OA_Admin_Template::_function_oa_icon(array('banner' => $this->_tpl_vars['row_b_expand']), $this);?>
" align="absmiddle" />
            <a href='JavaScript:GoOpener("banner-edit.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&bannerid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>
        </td>

        <td height='25'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>

        <td>&nbsp;</td>

        <td height='25'>
            <a href='JavaScript:GoOpener("banner-acl.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&bannerid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-acl.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'ACL'), $this);?>
'>&nbsp;<?php echo OA_Admin_Template::_function_t(array('str' => 'ACL'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-recycle.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("banner-delete.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&<?php echo OA_Admin_Template::_add_session_token(array(), $this);?>
&bannerid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
", true)'<?php echo OA_Admin_Template::_function_phpAds_DelConfirm(array('str' => 'ConfirmDeleteBanner'), $this);?>
><?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <a href='JavaScript:GoOpener("stats.php?entity=banner&breakdown=history&clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_clients']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&bannerid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-statistics.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
'>&nbsp;<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
    </tr>

<?php endforeach; endif; unset($_from); ?>
<?php endforeach; endif; unset($_from); ?>
<?php endforeach; endif; unset($_from); ?>


<?php $_from = $this->_tpl_vars['aCampaigns']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row_campaigns']):
?>
<?php if ($this->_tpl_vars['addSeparator']): ?>
    <tr height='1'><td colspan='6' bgcolor='#888888'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/break-l.gif' height='1' width='100%'></td></tr>
<?php else: ?>
    <?php $this->assign('addSeparator', 1); ?>
<?php endif; ?>

    <tr height='25' bgcolor="<?php echo smarty_function_cycle(array('name' => 'bgcolor'), $this);?>
">

        <td height='25'>
            &nbsp;&nbsp;
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-campaign.gif' align='absmiddle'>&nbsp;
            <a href='JavaScript:GoOpener("campaign-edit.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['campaignname'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>
        </td>

        <td height='25'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>

        <td>&nbsp;</td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-overview.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Overview'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("campaign-banners.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo OA_Admin_Template::_function_t(array('str' => 'Overview'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-recycle.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("campaign-delete.php?<?php echo OA_Admin_Template::_add_session_token(array(), $this);?>
&clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
", true)'<?php echo OA_Admin_Template::_function_phpAds_DelConfirm(array('str' => 'ConfirmDeleteCampaign'), $this);?>
><?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-statistics.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("stats.php?entity=campaign&breakdown=history&clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
    </tr>

<?php $_from = $this->_tpl_vars['row_campaigns']['banners']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row_b_expand']):
?>
    <tr height='1'><td colspan='6' bgcolor='#888888'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/break-el.gif' height='1' width='100%'></td></tr>

    <tr height='25' bgcolor="<?php echo smarty_function_cycle(array('name' => 'bgcolor'), $this);?>
">

        <td height='25'>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <img src="<?php echo OA_Admin_Template::_function_oa_icon(array('banner' => $this->_tpl_vars['row_b_expand']), $this);?>
" align="absmiddle" />
            <a href='JavaScript:GoOpener("banner-edit.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&bannerid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>
        </td>

        <td height='25'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>

        <td>&nbsp;</td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-acl.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'ACL'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("banner-acl.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&bannerid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo OA_Admin_Template::_function_t(array('str' => 'ACL'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-recycle.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("banner-delete.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&<?php echo OA_Admin_Template::_add_session_token(array(), $this);?>
&bannerid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
", true)'<?php echo OA_Admin_Template::_function_phpAds_DelConfirm(array('str' => 'ConfirmDeleteBanner'), $this);?>
><?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-statistics.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("stats.php?entity=banner&breakdown=history&clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_campaigns']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&bannerid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_b_expand']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td></tr>

<?php endforeach; endif; unset($_from); ?>
<?php endforeach; endif; unset($_from); ?>


<?php $_from = $this->_tpl_vars['aBanners']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row_banners']):
?>
<?php if ($this->_tpl_vars['addSeparator']): ?>
    <tr height='1'><td colspan='6' bgcolor='#888888'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/break-l.gif' height='1' width='100%'></td></tr>
<?php else: ?>
    <?php $this->assign('addSeparator', 1); ?>
<?php endif; ?>

    <tr height='25' bgcolor="<?php echo smarty_function_cycle(array('name' => 'bgcolor'), $this);?>
">

        <td height='25'>
            &nbsp;&nbsp;
            <img src="<?php echo OA_Admin_Template::_function_oa_icon(array('banner' => $this->_tpl_vars['row_banners']), $this);?>
" align="absmiddle" />
            <a href='JavaScript:GoOpener("banner-edit.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&bannerid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>
        </td>

        <td height='25'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>

        <td>&nbsp;</td>

        <td height='25'>
            <a href='JavaScript:GoOpener("banner-acl.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&bannerid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-acl.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'ACL'), $this);?>
'>&nbsp;<?php echo OA_Admin_Template::_function_t(array('str' => 'ACL'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-recycle.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("banner-delete.php?clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&<?php echo OA_Admin_Template::_add_session_token(array(), $this);?>
&bannerid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
", true)'<?php echo OA_Admin_Template::_function_phpAds_DelConfirm(array('str' => 'ConfirmDeleteBanner'), $this);?>
><?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <a href='JavaScript:GoOpener("stats.php?entity=banner&breakdown=history&clientid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['clientid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&campaignid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['campaignid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&bannerid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_banners']['bannerid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-statistics.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
'>&nbsp;<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td></tr>

<?php endforeach; endif; unset($_from); ?>


<?php $_from = $this->_tpl_vars['aAffiliates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row_affiliates']):
?>
<?php if ($this->_tpl_vars['addSeparator']): ?>
    <tr height='1'><td colspan='6' bgcolor='#888888'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/break-l.gif' height='1' width='100%'></td></tr>
<?php else: ?>
    <?php $this->assign('addSeparator', 1); ?>
<?php endif; ?>

    <tr height='25' bgcolor="<?php echo smarty_function_cycle(array('name' => 'bgcolor'), $this);?>
">

        <td height='25'>
            &nbsp;&nbsp;
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-affiliate.gif' align='absmiddle'>&nbsp;
            <a href='JavaScript:GoOpener("affiliate-edit.php?affiliateid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_affiliates']['affiliateid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_affiliates']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>
        </td>

        <td height='25'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_affiliates']['affiliateid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>

        <td>&nbsp;</td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-overview.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Overview'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("affiliate-zones.php?affiliateid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_affiliates']['affiliateid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo OA_Admin_Template::_function_t(array('str' => 'Overview'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-recycle.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("affiliate-delete.php?<?php echo OA_Admin_Template::_add_session_token(array(), $this);?>
&affiliateid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_affiliates']['affiliateid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
", true)'<?php echo OA_Admin_Template::_function_phpAds_DelConfirm(array('str' => 'ConfirmDeleteAffiliate'), $this);?>
><?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-statistics.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("stats.php?entity=affiliate&breakdown=history&affiliateid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_affiliates']['affiliateid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
    </tr>

<?php $_from = $this->_tpl_vars['row_affiliates']['zones']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row_z_expand']):
?>
    <tr height='1'><td colspan='6' bgcolor='#888888'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/break-el.gif' height='1' width='100%'></td></tr>

    <tr height='25' bgcolor="<?php echo smarty_function_cycle(array('name' => 'bgcolor'), $this);?>
">

        <td height='25'>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-zone.gif' align='absmiddle'>&nbsp;
            <a href='JavaScript:GoOpener("zone-edit.php?affiliateid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_z_expand']['affiliateid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&zoneid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_z_expand']['zoneid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_z_expand']['zonename'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>
        </td>

        <td height='25'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_z_expand']['zoneid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>

        <td>&nbsp;</td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-zone-linked.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'IncludedBanners'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("zone-include.php?affiliateid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_z_expand']['affiliateid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&zoneid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_z_expand']['zoneid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo OA_Admin_Template::_function_t(array('str' => 'IncludedBanners'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-recycle.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("zone-delete.php?<?php echo OA_Admin_Template::_add_session_token(array(), $this);?>
&affiliateid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_z_expand']['affiliateid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&zoneid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_z_expand']['zoneid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
", true)'<?php echo OA_Admin_Template::_function_phpAds_DelConfirm(array('str' => 'ConfirmDeleteZone'), $this);?>
><?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <a href='JavaScript:GoOpener("stats.php?entity=zone&breakdown=history&affiliateid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_affiliates']['affiliateid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&zoneid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_z_expand']['zoneid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-statistics.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
'>&nbsp;<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td></tr>

<?php endforeach; endif; unset($_from); ?>
<?php endforeach; endif; unset($_from); ?>


<?php $_from = $this->_tpl_vars['aZones']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row_zones']):
?>
<?php if ($this->_tpl_vars['addSeparator']): ?>
    <tr height='1'><td colspan='6' bgcolor='#888888'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/break-l.gif' height='1' width='100%'></td></tr>
<?php else: ?>
    <?php $this->assign('addSeparator', 1); ?>
<?php endif; ?>

    <tr height='25' bgcolor="<?php echo smarty_function_cycle(array('name' => 'bgcolor'), $this);?>
">

        <td height='25'>
            &nbsp;&nbsp;
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-zone.gif' align='absmiddle'>&nbsp;
            <a href='JavaScript:GoOpener("zone-edit.php?affiliateid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_zones']['affiliateid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&zoneid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_zones']['zoneid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_zones']['zonename'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>
        </td>

        <td height='25'><?php echo ((is_array($_tmp=$this->_tpl_vars['row_zones']['zoneid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>

        <td>&nbsp;</td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-zone-linked.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'IncludedBanners'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("zone-include.php?affiliateid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_zones']['affiliateid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&zoneid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_zones']['zoneid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><?php echo OA_Admin_Template::_function_t(array('str' => 'IncludedBanners'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-recycle.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
'>&nbsp;<a href='JavaScript:GoOpener("zone-delete.php?<?php echo OA_Admin_Template::_add_session_token(array(), $this);?>
&affiliateid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_zones']['affiliateid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&zoneid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_zones']['zoneid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
", true)'<?php echo OA_Admin_Template::_function_phpAds_DelConfirm(array('str' => 'ConfirmDeleteZone'), $this);?>
><?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>

        <td height='25'>
            <a href='JavaScript:GoOpener("stats.php?entity=zone&breakdown=history&affiliateid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_zones']['affiliateid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&zoneid=<?php echo ((is_array($_tmp=$this->_tpl_vars['row_zones']['zoneid'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'js') : smarty_modifier_escape($_tmp, 'js')); ?>
")'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/icon-statistics.gif' border='0' align='absmiddle' alt='<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
'>&nbsp;<?php echo OA_Admin_Template::_function_t(array('str' => 'Stats'), $this);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
    </tr>

<?php endforeach; endif; unset($_from); ?>

<?php if ($this->_tpl_vars['matchesFound']): ?>
    <tr height='1'><td colspan='6' bgcolor='#888888'><img src='<?php echo ((is_array($_tmp=$this->_tpl_vars['assetPath'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/images/break.gif' height='1' width='100%'></td></tr>
<?php else: ?>
    <?php echo OA_Admin_Template::_function_t(array('str' => 'NoMatchesFound'), $this);?>

<?php endif; ?>
</table>

</td><td width='20'>&nbsp;</td></tr>
</table>

<br /><br />