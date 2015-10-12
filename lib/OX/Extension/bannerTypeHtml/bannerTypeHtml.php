<?php

/*
+---------------------------------------------------------------------------+
| Revive Adserver                                                           |
| http://www.revive-adserver.com                                            |
|                                                                           |
| Copyright: See the COPYRIGHT.txt file.                                    |
| License: GPLv2 or later, see the LICENSE.txt file.                        |
+---------------------------------------------------------------------------+
*/

require_once MAX_PATH . '/lib/OA.php';
require_once LIB_PATH . '/Plugin/Component.php';

/**
 *
 * @package    OpenXPlugin
 * @subpackage Plugins_BannerTypes
 * @author     Monique Szpak <monique.szpak@openx.org>
 * @abstract
 */
class Plugins_BannerTypeHTML extends OX_Component
{
    function getStorageType()
    {
        return 'html';
    }

    /**
     * Return the media (content) type
     *
     */
    function getContentType()
    {
        return 'html';
    }

    /**
     * Return type of plugin
     *
     * @return string A string describing the type of plugin.
     */
    function getOptionDescription()
    {
        return 'Generic HTML Banner';
    }

    /**
     * Append type-specific form elements to the base form
     *
     * @param object form
     * @param integer banner id
     */
    function buildForm(&$form, &$row)
    {
	$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
        $form->setAttribute("onSubmit", "return max_formValidateHtml(this.banner)");
        $header = $form->createElement('header', 'header_html', $GLOBALS['strHTMLBanner']." -  banner code");
        $header->setAttribute('icon', 'icon-banner-html.gif');
        $form->addElement($header);

        $adPlugins = OX_Component::getComponents('3rdPartyServers');
        $adPluginsNames = OX_Component::callOnComponents($adPlugins, 'getName');
        $adPluginsList = array();
        $adPluginsList[''] = $GLOBALS['strAdserverTypeGeneric'];
        $adPluginsList['none'] = $GLOBALS['strDoNotAlterHtml'];
        foreach($adPluginsNames as $adPluginKey => $adPluginName) {
            $adPluginsList[$adPluginKey] = $adPluginName;
        }

        $htmlG['textarea'] =  $form->createElement('textarea', 'htmltemplate', null,
            array(
                'class' =>'code', 'cols'=>'45', 'rows'=>'10', 'wrap'=>'off',
                'dir' => 'ltr', 'style'=>'width:550px;'
            ));


        $aSelectAttributes = array('id'=>'adserver', 'style' => 'margin-left: 15px;width:230px');
        $htmlG['select'] = HTML_QuickForm::createElement('select', 'adserver', $GLOBALS['strAlterHTML'], $adPluginsList, $aSelectAttributes);
        $form->addGroup($htmlG, 'html_banner_g', null, array("<br>", ""), false);

	$is_thirdparty['is_dj_html']=0;

	$is_thirdparty=mysql_fetch_array(mysql_query("select * from {$table_prefix}banners where bannerid='".$row['bannerid']."'"));

	if($is_thirdparty['is_dj_html']==2)
	{
	$selected='selected=selected';
	}


	

	$form->addElement('static', 'tar'," <table border='0' width='100%' cellpadding='0' cellspacing='0'>
				<tr><td height='25' colspan='3'><b>Enable Third Party HTML</b></td></tr>
				<tr><td height='25'>
				<select name='3rdparty' 'style'='margin-left: 15px;width:230px' onChange='thirdparty(this.value);'  tabindex='1' >
				<option value='1'>HTML Ad</option>
				<option value='2' $selected>Third Party HTML Ad</option>
				</select>
				</td>
				</tr>
				</table>");
	
	if($is_thirdparty['is_dj_html']!=2)
	{
        $form->addElement('header', 'header_b_links', "Banner link");
	$form->addElement('static','tar'," <div id='djaxinfo' width='100%'></div>");
        $form->addElement('text', 'url', $GLOBALS['strURL']);
        $form->addElement('text', 'target', $GLOBALS['strTarget']);
	}
	else
	{
	$form->addElement('header', 'header_b_links', "Banner link");
	$form->addElement('static','tar'," <div id='djaxinfo' width='100%'>Third party HTML wont support url and target</div>");
        $form->addElement('text', 'url','',array('style' => 'display:none'));
        $form->addElement('text', 'target','',array('style' => 'display:none'));
	
	}

        $form->addElement('header', 'header_b_display', 'Banner display');
        $sizeG['width'] = $form->createElement('text', 'width', $GLOBALS['strWidth'].":");
        $sizeG['width']->setSize(5);
        $sizeG['height'] = $form->createElement('text', 'height', $GLOBALS['strHeight'].":");
        $sizeG['height']->setSize(5);

        if (!empty($row['bannerid'])) {
            $sizeG['height']->setAttribute('onChange', 'oa_sizeChangeUpdateMessage("warning_change_banner_size");');
            $sizeG['width']->setAttribute('onChange', 'oa_sizeChangeUpdateMessage("warning_change_banner_size");');
        }
        $form->addGroup($sizeG, 'size', $GLOBALS['strSize'], "&nbsp;", false);

        $form->addElement('hidden', 'ext_bannertype', $this->getComponentIdentifier());

        //validation rules
        $translation = new OX_Translation();
        $widthRequiredRule = array($translation->translate($GLOBALS['strXRequiredField'], array($GLOBALS['strWidth'])), 'required');
        $heightRequiredRule = array($translation->translate($GLOBALS['strXRequiredField'], array($GLOBALS['strHeight'])), 'required');
        $numericRule = array($GLOBALS['strNumericField'] , 'numeric');

        $form->addGroupRule('size', array(
            'width' => array($widthRequiredRule, $numericRule),
            'height' => array($heightRequiredRule, $numericRule)));
    }

    function preprocessForm($insert, $bannerid, $aFields)
    {
        return true;
    }

    function processForm($insert, $bannerid, $aFields)
    {
        return true;
    }

    function validateForm(&$form)
    {
        return true;
    }

    function buildHtmlTemplate($aFields)
    {

    }

    /**
     * Modify the generated banner cache.
     *
     * @param string $buffer the banner cache.
     * @param array $noScript
     * @param array $banner
     * @return string
     */
    function getBannerCache($buffer, &$noScript, $banner)
    {
        return $buffer;
    }
}

?>
