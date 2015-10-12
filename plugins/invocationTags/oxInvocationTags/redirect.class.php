<?php

require_once LIB_PATH . '/Extension/invocationTags/InvocationTags.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';

/**
 *
 * Invocation tag plugin.
 *
 */
class Plugins_InvocationTags_OxInvocationTags_redirect extends Plugins_InvocationTags
{

    /**
     * Return name of plugin
     *
     * @return string
     */
    function getName()
    {
        return $this->translate("Redirect Tag");
    }

    /**
     * Return the English name of the plugin. Used when
     * generating translation keys based on the plugin
     * name.
     *
     * @return string An English string describing the class.
     */
    function getNameEN()
    {
        return 'Redirect Tag';
    }

    /**
     * Check if plugin is allowed
     *
     * @return boolean  True - allowed, false - not allowed
     */
    function isAllowed($extra)
    {
        //$isAllowed = parent::isAllowed($extra);
        return true;
    }

    function getOrder()
    {
       // parent::getOrder();
        return 15;
    }

    /**
     * Return list of options
     *
     * @return array    Group of options
     */
    function getOptionsList()
    {
        if (is_array($this->defaultOptions)) {
            if (in_array('cacheBuster', $this->defaultOptions)) {
                unset($this->defaultOptions['cacheBuster']);
            }
        }
        $options = array (
            'spacer'      => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
            'what'          => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
            //'clientid'      => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
            'redirect'     => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
            'campaignid'    => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
            'block'         => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
            'target'        => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
            'source'        => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
            'withtext'      => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
            'blockcampaign' => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
            'charset'       => MAX_PLUGINS_INVOCATION_TAGS_STANDARD
        );

        return $options;
    }

    /**
     * Return invocation code for this plugin (codetype)
     *
     * @return string
     */
    function generateInvocationCode()
    {
        $aComments = array(
            'SSL Delivery Comment' => '',
            'Comment'              => $this->translate(""),
            );
        parent::prepareCommonInvocationData($aComments);

        $conf = $GLOBALS['_MAX']['CONF'];
        $mi = &$this->maxInvocation;
       
     
            ///////////redirect////////////////////
		if (isset($mi->redirect) && $mi->redirect != '') {
					
			$redirect = ($mi->redirect)*1000;
        
        }	
        
        ///////////redirect////////////////////

		$redirect=!empty($redirect)?$redirect:5000;

		$zoneid= "zoneid=".$mi->zoneid;
        $buffer = $mi->buffer;
        $buffer = "<script type='text/javascript'>\n";
        $buffer .= "function Redirect()\n";
        $buffer .= "{\n";
		$conf['file']['js']="oxm_redirect.php?".$zoneid;
		$buffer .= "window.top.location.href='http:".MAX_commonConstructPartialDeliveryUrl($conf['file']['js'])."';\n";
		$buffer .= "}\n";
        $buffer .= "setTimeout('Redirect()',".$redirect.");\n";
        $buffer .= "</script>";		     
        return $buffer;
    }

    function setInvocation(&$invocation) {
        $this->maxInvocation = &$invocation;
        $this->maxInvocation->canDetectCharset = true;
    }

}

?>
