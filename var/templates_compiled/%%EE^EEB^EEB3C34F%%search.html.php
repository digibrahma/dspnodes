<?php /* Smarty version 2.6.18, created on 2015-10-05 13:50:01
         compiled from layout/search.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'layout/search.html', 54, false),array('function', 't', 'layout/search.html', 54, false),)), $this); ?>
<?php if ($this->_tpl_vars['uiPart'] == 'header'): ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html<?php if ($this->_tpl_vars['phpAds_TextDirection'] != 'ltr'): ?> dir="<?php echo $this->_tpl_vars['phpAds_TextDirection']; ?>
"<?php endif; ?>>
<head>
<title><?php echo $this->_tpl_vars['pageTitle']; ?>
</title>

<meta name="generator" content="<?php echo $this->_tpl_vars['metaGenerator']; ?>
" />
<meta name="robots" content="noindex, nofollow" />

<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['assetPath']; ?>
/css/chrome.css" />
<?php if ($this->_tpl_vars['phpAds_TextDirection'] != 'ltr'): ?><link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['assetPath']; ?>
/css/chrome-rtl.css" /><?php endif; ?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['assetPath']; ?>
/css/interface-<?php echo $this->_tpl_vars['phpAds_TextDirection']; ?>
.css" />

<script type='text/javascript'>
<!--<?php echo '
    function GoOpener(url, reload)
    {
        opener.location.href = url;
        opener.focus();

        if (reload == true)
        {
            document.search.submit();
        }
    }
//'; ?>
-->
</script>

</head>
<body>

<div id="oaHeader">

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "layout/branding.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<div id='oaSearch'>
		<form name='search' action='admin-search.php' method='post'>
	    <input type='hidden' name='client' value='<?php echo $this->_tpl_vars['client']; ?>
'>
	    <input type='hidden' name='campaign' value='<?php echo $this->_tpl_vars['campaign']; ?>
'>
	    <input type='hidden' name='banner' value='<?php echo $this->_tpl_vars['banner']; ?>
'>
	    <input type='hidden' name='zone' value='<?php echo $this->_tpl_vars['zone']; ?>
'>
	    <input type='hidden' name='affiliate' value='<?php echo $this->_tpl_vars['affiliate']; ?>
'>
	    <input type='hidden' name='compact' value='<?php echo $this->_tpl_vars['compact']; ?>
'>
	    <input value="<?php echo ((is_array($_tmp=$this->_tpl_vars['keyword'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" type='text' name='keyword' class='search' accesskey='<?php echo OA_Admin_Template::_function_t(array('key' => 'Search'), $this);?>
'><input class="submit" type="submit" value="&nbsp" >
    </form>
	</div>
</div>

<?php else: ?>

</body>
</html>
<?php endif; ?>