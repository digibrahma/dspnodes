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

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/OA/Creative/File.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/max/other/common.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/component/Form.php';
require_once MAX_PATH . '/lib/OA/Maintenance/Priority.php';

require_once LIB_PATH . '/Plugin/Component.php';

$htmltemplate = MAX_commonGetValueUnslashed('htmltemplate');

// Register input variables
phpAds_registerGlobalUnslashed(
     'alink'
    ,'alink_chosen'
    ,'alt'
    ,'alt_imageurl'
    ,'asource'
    ,'atar'
    ,'adserver'
    ,'bannertext'
    ,'campaignid'
    ,'checkswf'
    ,'clientid'
    ,'comments'
    ,'description'
    ,'ext_bannertype'
    ,'height'
    ,'imageurl'
    ,'keyword'
    ,'message'
    ,'replaceimage'
    ,'replacealtimage'
    ,'status'
    ,'statustext'
    ,'type'
    ,'submit'
    ,'target'
    ,'transparent'
    ,'upload'
    ,'url'
    ,'weight'
    ,'width'
);

/*-------------------------------------------------------*/
/* Client interface security                             */
/*-------------------------------------------------------*/
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER);
OA_Permission::enforceAccessToObject('clients',   $clientid);
OA_Permission::enforceAccessToObject('campaigns', $campaignid);

if (OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER)) {
    OA_Permission::enforceAllowed(OA_PERM_BANNER_EDIT);
    OA_Permission::enforceAccessToObject('banners', $bannerid);
} else {
    OA_Permission::enforceAccessToObject('banners', $bannerid, true);
}
/*-----------------------Modified By DaC016--------------------------------*/
$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],
$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];


		
		/*-----------------------Modified By DaC016--------------------------------*/
/*-------------------------------------------------------*/
/* Store preferences									 */
/*-------------------------------------------------------*/
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['clientid'] = $clientid;
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['campaignid'][$clientid] = $campaignid;
phpAds_SessionDataStore();

/*
storage type / media type
sql gif
sql png
sql jpeg
sql swf
sql mov
web gif
web png
web jpeg
web swf
web mov
url gif
url png
url jpeg
url swf
url mov
txt text
html html
*/

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

//decide whether this is add or edit, get banner data or initialise it
if ($bannerid != '') {
    // Fetch the data from the database
    $doBanners = OA_Dal::factoryDO('banners');
    if ($doBanners->get($bannerid)) {
        $aBanner = $doBanners->toArray();
    }

 /* openXmods */

$aBanner['width_new']  = $aBanner['width'];

$aBanner['height_new'] = $aBanner['height'];

/* openXmods */

    // Set basic values
    $type               = $aBanner['storagetype'];
    $ext_bannertype     = $aBanner['ext_bannertype'];
    $hardcoded_links    = array();
    $hardcoded_targets  = array();
    $hardcoded_sources  = array();

    if (empty($ext_bannertype)) {
        if ($type == 'html') {
            $ext_bannertype = 'bannerTypeHtml:oxHtml:genericHtml';
        } elseif ($type == 'txt') {
            $ext_bannertype = 'bannerTypeText:oxText:genericText';
        }
    }
    // Check for hard-coded links
    if (!empty($aBanner['parameters'])) {
        $aSwfParams = unserialize($aBanner['parameters']);
        if (!empty($aSwfParams['swf'])) {
            foreach ($aSwfParams['swf'] as $iKey => $aSwf) {
                $hardcoded_links[$iKey]   = $aSwf['link'];
                $hardcoded_targets[$iKey] = $aSwf['tar'];
                $hardcoded_sources[$iKey] = '';
            }
        }
    }
    if (!empty($aBanner['filename'])) {
        $aBanner['replaceimage'] = "f"; //select keep image by default
    }

    if (!empty($aBanner['alt_filename'])) {
        $aBanner['replacealtimage'] = "f"; //select keep backup image by default
    }

    $aBanner['hardcoded_links'] = $hardcoded_links;
    $aBanner['hardcoded_targets'] = $hardcoded_targets;
    $aBanner['hardcoded_sources'] = $hardcoded_sources;
    $aBanner['clientid']   = $clientid;

}
else {
    // Set default values for new banner
    $aBanner['bannerid']     = '';
    $aBanner['campaignid']   = $campaignid;
    $aBanner['clientid']     = $clientid;
    $aBanner['alt']          = '';
    $aBanner['status']       = '';
    $aBanner['bannertext']   = '';
    $aBanner['url']          = "http://";
    $aBanner['target']       = '';
    $aBanner['imageurl']     = "http://";
    $aBanner['width']        = '';
    $aBanner['height']       = '';
    $aBanner['htmltemplate'] = '';
    $aBanner['description']  = '';
    $aBanner['comments']     = '';
    $aBanner['contenttype']  = '';
    $aBanner['adserver']     = '';
    $aBanner['keyword']      = '';
    $aBanner["weight"]       = $pref['default_banner_weight'];

    $aBanner['hardcoded_links'] = array();
    $aBanner['hardcoded_targets'] = array();
}
if ($ext_bannertype)
{
    list($extension, $group, $plugin) = explode(':', $ext_bannertype);
    $oComponent = &OX_Component::factory($extension, $group, $plugin);
    //  we may want to use the ancestor class for some sort of generic functionality
    if (!$oComponent)
    {
        $oComponent = OX_Component::getFallbackHandler($extension);
    }
    $formDisabled = (!$oComponent || !$oComponent->enabled);
}
if ((!$ext_bannertype) && $type && (!in_array($type, array('sql','web','url','html','txt'))))
{
    list($extension, $group, $plugin) = explode('.',$type);
    $oComponent = &OX_Component::factoryByComponentIdentifier($extension,$group,$plugin);
    $formDisabled = (!$oComponent || !$oComponent->enabled);
    if ($oComponent)
    {
        $ext_bannertype = $type;
        $type = $oComponent->getStorageType();
    }
    else
    {
        $ext_bannertype = '';
        $type = '';
    }
}

/*
// If adding a new banner or used storing type is disabled
// determine which bannertype to show as default
$show_sql   = $conf['allowedBanners']['sql'];
$show_web   = $conf['allowedBanners']['web'];
$show_url   = $conf['allowedBanners']['url'];*/
//$show_html  = $conf['allowedBanners']['html'];
//$show_txt   = $conf['allowedBanners']['text'];
 $show_web   = $conf['allowedBanners']['web'];
/*
if (isset($type) && $type == "sql")      $show_sql     = true;
if (isset($type) && $type == "web")      $show_web     = true;
if (isset($type) && $type == "url")      $show_url     = true;*/
//if (isset($type) && $type == "html")     $show_html    = true;
																
//if (isset($type) && $type == "txt")      $show_txt     = true;
 if (isset($type) && $type == "web")      $show_web     = true;

$bannerTypes = array();
if ($show_web) {
    $bannerTypes['web']['web'] = $GLOBALS['strWebBanner'];
}

/*
if ($show_sql) {
    $bannerTypes['sql']['sql'] = $GLOBALS['strMySQLBanner'];
}
if ($show_url) {
    $bannerTypes['url']['url']= $GLOBALS['strURLBanner'];
}

if ($show_html) {

    $aBannerTypeHtml = OX_Component::getComponents('bannerTypeHtml');


    foreach ($aBannerTypeHtml AS $tmpComponent)
    {
      //  $componentIdentifier = $tmpComponent->getComponentIdentifier();
        //$bannerTypes['html'][$componentIdentifier] = $tmpComponent->getOptionDescription();
$bannerTypes['html']['bannerTypeHtml:oxHtml:genericHtml'] = "Generic HTML Banner";
    }
}


if ($show_txt) {
    $aBannerTypeText = OX_Component::getComponents('bannerTypeText');
  foreach ($aBannerTypeText AS $tmpComponent)
    {
        $componentIdentifier = $tmpComponent->getComponentIdentifier();
        $bannerTypes['text'][$componentIdentifier] = $tmpComponent->getOptionDescription();
    }
}
*/

if (!$type)
{
   // if ($show_txt)     $type = "txt";
   // if ($show_html)    $type = "html";
    //if ($show_url)     $type = "url";
    //if ($show_sql)     $type = "sql";
    if ($show_web)     $type = "web";
}

$show_web   = $conf['allowedBanners']['web'];
//$show_txt   = $conf['allowedBanners']['text'];
//$show_html   = $conf['allowedBanners']['html'];
$bannerTypes = array();
if ($show_web) {
    $bannerTypes['web']['web'] = $GLOBALS['strWebBanner'];
}
if ($show_txt) {
    $aBannerTypeText = OX_Component::getComponents('bannerTypeText');
    foreach ($aBannerTypeText AS $tmpComponent)
    {
        $componentIdentifier = $tmpComponent->getComponentIdentifier();
        $bannerTypes['text'][$componentIdentifier] = $tmpComponent->getOptionDescription();
    }
}

if ($show_html) {
    $aBannerTypeHtml = OX_Component::getComponents('bannerTypeHtml');
    foreach ($aBannerTypeHtml AS $tmpComponent)
    {
        //$componentIdentifier = $tmpComponent->getComponentIdentifier();
        //$bannerTypes['html'][$componentIdentifier] = $tmpComponent->getOptionDescription();
$bannerTypes['html']['bannerTypeHtml:oxHtml:genericHtml'] = "Generic HTML Banner";
}

}

if (!$type)
{
    if ($show_web)     $type = "web";
  // if ($show_txt)     $type = "txt";   
//if ($show_html)     $type = "html";   
}

//build banner form
$form = buildBannerForm($type, $aBanner, $oComponent, $formDisabled);

$valid = $form->validate();
if ($valid && $oComponent && $oComponent->enabled)
{
    $valid = $oComponent->validateForm($form);
}
if ($valid)
{
    //process submitted values
    processForm($bannerid, $form, $oComponent, $formDisabled);
}
else { //either validation failed or form was not submitted, display the form
    displayPage($bannerid, $campaignid, $clientid, $bannerTypes, $aBanner, $type, $form, $ext_bannertype, $formDisabled);
}



function displayPage($bannerid, $campaignid, $clientid, $bannerTypes, $aBanner, $type, $form, $ext_bannertype, $formDisabled=false)
{
    // Initialise some parameters
    $pageName = basename($_SERVER['SCRIPT_NAME']);
    $aEntities = array('clientid' => $clientid, 'campaignid' => $campaignid, 'bannerid' => $bannerid);

    $entityId = OA_Permission::getEntityId();
    if (OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER)) {
        $entityType = 'advertiser_id';
    } else {
        $entityType = 'agency_id';
    }

    // Display navigation
    $aOtherCampaigns = Admin_DA::getPlacements(array($entityType => $entityId));
    $aOtherBanners = Admin_DA::getAds(array('placement_id' => $campaignid), false);
    MAX_displayNavigationBanner($pageName, $aOtherCampaigns, $aOtherBanners, $aEntities);

    //actual page content - type chooser and form
    /*-------------------------------------------------------*/
    /* Main code                                             */
    /*-------------------------------------------------------*/
    $oTpl = new OA_Admin_Template('mobilebanner-edit.html');

    $oTpl->assign('clientId',  $clientid);
    $oTpl->assign('campaignId',  $campaignid);
    $oTpl->assign('bannerId',  $bannerid);
    $oTpl->assign('bannerTypes', $bannerTypes);
    $oTpl->assign('bannerType', ($ext_bannertype ? $ext_bannertype : $type));
    $oTpl->assign('bannerHeight', $aBanner["height"]);
    $oTpl->assign('bannerWidth', $aBanner["width"]);
    $oTpl->assign('disabled', $formDisabled);
    $oTpl->assign('form', $form->serialize());


    $oTpl->display();

    /*********************************************************/
    /* HTML framework                                        */
    /*********************************************************/
    phpAds_PageFooter();
}


function buildBannerForm($type, $aBanner, &$oComponent=null, $formDisabled=false)
{

    //-- Build forms
    $form = new OA_Admin_UI_Component_Form("mobilebannerForm", "POST", $_SERVER['SCRIPT_NAME'], null, array("enctype"=>"multipart/form-data"));
    $form->forceClientValidation(true);
    $form->addElement('hidden', 'clientid', $aBanner['clientid']);
    $form->addElement('hidden', 'campaignid', $aBanner['campaignid']);
    $form->addElement('hidden', 'bannerid', $aBanner['bannerid']);
   $form->addElement('hidden', 'type', $type);


$form->addElement('hidden', 'type', $type);
    $form->addElement('hidden', 'status', $aBanner['status']);

    if ($type == 'sql' || $type == 'web') {
        $form->addElement('custom', 'banner-iab-note', null, null);
    }
    if ($aBanner['contenttype'] == 'swf' && empty($aBanner['alt_contenttype']) && empty($aBanner['alt_imageurl'])) {
        $form->addElement('custom', 'banner-backup-note', null, null);
    }

    $form->addElement('header', 'header_basic', $GLOBALS['strBasicInformation']);
    if (OA_Permission::isAccount(OA_ACCOUNT_ADMIN) || OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) {
        $form->addElement('text', 'description', $GLOBALS['strName']);
    }
    else {
        $form->addElement('static', 'description', $GLOBALS['strName'], $aBanner['description']);
    }

    //local banners
    if ($type == 'sql' || $type == 'web') {
 /* openXmods */

if($aBanner['bannerid'] != '')
{
 	if ($type == 'sql') {
            $header = $form->createElement('header', 'header_sql', $GLOBALS['strMySQLBanner']." -  banner creative");
        }
        else {
            $header = $form->createElement('header', 'header_sql', "Select images for Mobile banners ");
        }

    
        $header->setAttribute('icon', 'icon-banner-stored.gif');
        $form->addElement($header);

        $imageName = _getContentTypeIconImageName($aBanner['contenttype']);
        $size = _getBannerSizeText($type, $aBanner['filename']);
//
        addUploadGroup($form, $aBanner,
            array(
                'uploadName' => 'upload',
                'radioName' => 'replaceimage',
                'imageName'  => $imageName,
                'fileName'  => $aBanner['filename'],
                'fileSize'  => $size,
                'newLabel'  =>  $GLOBALS['strNewBannerFile'],
                'updateLabel'  => $GLOBALS['strUploadOrKeep'],
                'handleSWF' => true
              )
        );

} 
 /* openXmods */

//=======================================================Modified By DAC021===============================================================//
   

   
if($aBanner['bannerid'] == '')
{

$width = array();
$height = array();

$user_id = OA_Permission::getUserId();
$query = mysql_query("select * from oxm_mobilezonesize where user_id=1") or die(mysql_error());
	while($row = mysql_fetch_assoc($query))
	{
			$width[$row['zonecategory']] = $row['width'];
			$height[$row['zonecategory']] = $row['height'];
			
	}

 	if ($type == 'sql') {
             $header1 = $form->createElement('header', 'header_sql', "Select images for Mobile banners {Size Should Be Equal}");
        }
        else {
             $header1 = $form->createElement('header', 'header_sql', "Select images for Mobile banners {Size Should Be Equal}");
        }
    
        $header1->setAttribute('icon', 'icon-banner-stored.gif');
        $form->addElement($header1);
 addUploadGroup($form, $aBanner,
            array(
                'uploadName' => 'upload',
                'radioName' => 'replaceimage',
                'imageName'  => $imageName,
                'fileName'  => $aBanner['filename'],
                'fileSize'  => $size,
                'newLabel'  =>  'Mobile Banner 1 [  ' . $width[1] .'X'. $height[1] . '  ]',
                'updateLabel'  => $GLOBALS['strUploadOrKeep'],
                'handleSWF' => true
              )
        );
        addUploadGroup($form, $aBanner,
            array(
                'uploadName' => 'upload2',
                'radioName' => 'replaceimage',
                'imageName'  => $imageName,
                'fileName'  => $aBanner['filename'],
                'fileSize'  => $size,
                'newLabel'  => 'Mobile Banner 2 [  ' . $width[2] .'X'. $height[2] . '  ]',
                'updateLabel'  => $GLOBALS['strUploadOrKeep'],
                'handleSWF' => true
              )
        );
        addUploadGroup($form, $aBanner,
            array(
                'uploadName' => 'upload3',
                'radioName' => 'replaceimage',
                'imageName'  => $imageName,
                'fileName'  => $aBanner['filename'],
                'fileSize'  => $size,
                'newLabel'  => 'Mobile Banner 3 [  ' . $width[3] .'X'. $height[3] . '  ]',
                'updateLabel'  => $GLOBALS['strUploadOrKeep'],
                'handleSWF' => true
              )
        );
  addUploadGroup($form, $aBanner,
            array(
                'uploadName' => 'upload4',
                'radioName' => 'replaceimage',
                'imageName'  => $imageName,
                'fileName'  => $aBanner['filename'],
                'fileSize'  => $size,
                'newLabel'  => 'Mobile Banner 4 [  ' . $width[4] .'X'. $height[4] . '  ]',
                'updateLabel'  => $GLOBALS['strUploadOrKeep'],
                'handleSWF' => true
              )
        );
}
//=======================================================Modified By DAC021===============================================================//
        $altImageName = null;
        $altSize = null;
        if ($aBanner['contenttype'] == 'swf') {
            $altImageName = _getContentTypeIconImageName($aBanner['alt_contenttype']);
            $altSize = _getBannerSizeText($type, $aBanner['alt_filename']);
        }

        $aUploadParams = array(
                'uploadName' => 'uploadalt',
                'radioName' => 'replacealtimage',
                'imageName'  => $altImageName,
                'fileSize'  => $altSize,
                'fileName'  => $aBanner['alt_filename'],
                'newLabel'  => $GLOBALS['strNewBannerFileAlt'],
                'updateLabel'  => $GLOBALS['strUploadOrKeep'],
                'handleSWF' => false
              );

        if ($aBanner['contenttype'] != 'swf') {
            $aUploadParams = array_merge($aUploadParams, array('decorateId' => 'swfAlternative'));
        }
        addUploadGroup($form, $aBanner, $aUploadParams);

        $form->addElement('header', 'header_b_links', "Banner link");
        if (count($aBanner['hardcoded_links']) == 0) {
            $form->addElement('text', 'url', $GLOBALS['strURL']);
            $targetElem = $form->createElement('text', 'target', $GLOBALS['strTarget']);
            $targetElem->setAttribute('maxlength', '16');
            $form->addElement($targetElem);
        }
        else {
            foreach ($aBanner['hardcoded_links'] as $key => $val) {
                $link['text'] = $form->createElement('text', "alink[".$key."]", null);
                $link['text']->setAttribute("style", "width:330px");
                $link['radio'] = $form->createElement('radio', "alink_chosen", null, null, $key);
                $form->addGroup($link, 'url_group', $GLOBALS['strURL'], "", false);

                if (isset($aBanner['hardcoded_targets'][$key])) {
                    $targetElem = $form->createElement('text', "atar[".$key."]", $GLOBALS['strTarget']);
                    $targetElem->setAttribute('maxlength', '16');
                    $form->addElement($targetElem);
                }
                if (count($aBanner['hardcoded_links']) > 1) {
                    $form->addElement('text', "asource[".$key."]", $GLOBALS['strOverwriteSource']);
                }
            }
            $form->addElement('hidden', 'url', $aBanner['url']);
        }
        $form->addElement('header', 'header_b_display', 'Banner display');
        $form->addElement('text', 'alt', $GLOBALS['strAlt']);
        $form->applyFilter('alt', 'phpAds_htmlQuotes');
        $form->addElement('text', 'statustext', $GLOBALS['strStatusText']);
        $form->addElement('text', 'bannertext', $GLOBALS['strTextBelow']);
        $form->applyFilter('bannertext', 'phpAds_htmlQuotes');



        if (!empty($aBanner['bannerid'])) {

 //======================================================Modified By DAC016============================================//


           $sizeG['width'] = $form->createElement('hidden', 'width', $GLOBALS['strWidth'].":");
           $sizeG['height'] = $form->createElement('hidden', 'height', $GLOBALS['strHeight'].":");


            $sizeG['width_new'] = $form->createElement('text', 'width_new', $GLOBALS['strHeight'].":", 'disabled="disabled"');
            $sizeG['width_new']->setSize(5);

            $sizeG['height_new'] = $form->createElement('text', 'height_new', $GLOBALS['strHeight'].":", 'disabled="disabled"');
            $sizeG['height_new']->setSize(5);
            $form->addGroup($sizeG, 'size', $GLOBALS['strSize'], "&nbsp;", false);
            
           
   //======================================================Modified By DAC016============================================//

            //validation rules
            $translation = new OX_Translation();
            $widthRequiredRule = array($translation->translate($GLOBALS['strXRequiredField'], array($GLOBALS['strWidth'])), 'required');
            $widthPositiveRule = array($translation->translate($GLOBALS['strXGreaterThanZeroField'], array($GLOBALS['strWidth'])), 'min', 1);
            $heightRequiredRule = array($translation->translate($GLOBALS['strXRequiredField'], array($GLOBALS['strHeight'])), 'required');
            $heightPositiveRule = array($translation->translate($GLOBALS['strXGreaterThanZeroField'], array($GLOBALS['strHeight'])), 'min', 1);
            $numericRule = array($GLOBALS['strNumericField'] , 'numeric');

            $form->addGroupRule('size', array(
                'width' => array($widthRequiredRule, $numericRule, $widthPositiveRule),
                'height' => array($heightRequiredRule, $numericRule, $heightPositiveRule)));
        }
        if (!isset($aBanner['contenttype']) || $aBanner['contenttype'] == 'swf')
        {
            $form->addElement('checkbox', 'transparent', $GLOBALS['strSwfTransparency'], $GLOBALS['strSwfTransparency']);
        }

        //TODO $form->addRule("size", 'Please enter a number', 'numeric'); //this should make all fields in group size are numeric
    }

    //external banners
    if ($type == "url") {
        $header = $form->createElement('header', 'header_txt', $GLOBALS['strURLBanner']);
        $header->setAttribute('icon', 'icon-banner-url.gif');
        $form->addElement($header);

        $form->addElement('text', 'imageurl', $GLOBALS['strNewBannerURL']);

        if ($aBanner['contenttype'] == 'swf') {
            $altImageName = _getContentTypeIconImageName($aBanner['alt_contenttype']);
            $altSize = _getBannerSizeText($type, $aBanner['alt_filename']);

            $form->addElement('text', 'alt_imageurl', $GLOBALS['strNewBannerFileAlt']);
        }

        $form->addElement('header', 'header_b_links', "Banner link");
        $form->addElement('text', 'url', $GLOBALS['strURL']);
        $targetElem = $form->createElement('text', 'target', $GLOBALS['strTarget']);
        $targetElem->setAttribute('maxlength', '16');
        $form->addElement($targetElem);

        $form->addElement('header', 'header_b_display', 'Banner display');
        $form->addElement('text', 'alt', $GLOBALS['strAlt']);
        $form->applyFilter('alt', 'phpAds_htmlQuotes');

        $form->addElement('text', 'statustext', $GLOBALS['strStatusText']);
        $form->addElement('text', 'bannertext', $GLOBALS['strTextBelow']);
        $form->applyFilter('bannertext', 'phpAds_htmlQuotes');

        $sizeG['width'] = $form->createElement('text', 'width', $GLOBALS['strWidth'].":");
        $sizeG['width']->setAttribute('onChange', 'oa_sizeChangeUpdateMessage("warning_change_banner_size");');
        $sizeG['width']->setSize(5);

        $sizeG['height'] = $form->createElement('text', 'height', $GLOBALS['strHeight'].":");
        $sizeG['height']->setAttribute('onChange', 'oa_sizeChangeUpdateMessage("warning_change_banner_size");');
        $sizeG['height']->setSize(5);
        $form->addGroup($sizeG, 'size', $GLOBALS['strSize'], "&nbsp;", false);

        if (!isset($aBanner['contenttype']) || $aBanner['contenttype'] == 'swf')
        {
            $form->addElement('checkbox', 'transparent', $GLOBALS['strSwfTransparency'], $GLOBALS['strSwfTransparency']);
        }

        //validation rules
        $translation = new OX_Translation();
        $widthRequiredRule = array($translation->translate($GLOBALS['strXRequiredField'], array($GLOBALS['strWidth'])), 'required');
        $widthPositiveRule = array($translation->translate($GLOBALS['strXGreaterThanZeroField'], array($GLOBALS['strWidth'])), 'min', 1);
        $heightRequiredRule = array($translation->translate($GLOBALS['strXRequiredField'], array($GLOBALS['strHeight'])), 'required');
        $heightPositiveRule = array($translation->translate($GLOBALS['strXGreaterThanZeroField'], array($GLOBALS['strHeight'])), 'min', 1);
        $numericRule = array($GLOBALS['strNumericField'] , 'numeric');

        $form->addGroupRule('size', array(
            'width' => array($widthRequiredRule, $numericRule, $widthPositiveRule),
            'height' => array($heightRequiredRule, $numericRule, $heightPositiveRule)));
    }

    //html & text banners
    if ($oComponent)
    {
        $oComponent->buildForm($form, $aBanner);
    }

    $translation = new OX_Translation();

    //common for all banners
    if (OA_Permission::isAccount(OA_ACCOUNT_ADMIN) || OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) {
        $form->addElement('header', 'header_additional', "Additional data");
        $form->addElement('text', 'keyword', $GLOBALS['strKeyword']);
        $weightElem = $form->createElement('text', 'weight', $GLOBALS['strWeight']);
        $weightElem->setSize(6);
        $form->addElement($weightElem);
        $form->addElement('textarea', 'comments', $GLOBALS['strComments']);
        $weightPositiveRule = $translation->translate($GLOBALS['strXPositiveWholeNumberField'], array($GLOBALS['strWeight']));
        $form->addRule('weight', $weightPositiveRule, 'numeric');


    }


    //we want submit to be the last element in its own separate section
    $form->addElement('controls', 'form-controls');
    $form->addElement('submit', 'submit', 'Save changes');

    //validation rules
    if (OA_Permission::isAccount(OA_ACCOUNT_ADMIN) || OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) {
        $urlRequiredMsg = $translation->translate($GLOBALS['strXRequiredField'], array($GLOBALS['strName']));
        $form->addRule('description', $urlRequiredMsg, 'required');
    }

    //set banner values
    $form->setDefaults($aBanner);

    foreach ($aBanner['hardcoded_links'] as $key => $val) {
        $swfLinks["alink[".$key."]"] = phpAds_htmlQuotes($val);

        if ($val == $aBanner['url']) {
            $swfLinks['alink_chosen'] = $key;
        }
        if (isset($aBanner['hardcoded_targets'][$key])) {
            $swfLinks["atar[".$key."]"] = phpAds_htmlQuotes($aBanner['hardcoded_targets'][$key]);
        }
        if (count($aBanner['hardcoded_links']) > 1) {
            $swfLinks["asource[".$key."]"] = phpAds_htmlQuotes($aBanner['hardcoded_sources'][$key]);
        }
    }
    $form->setDefaults($swfLinks);
    if ($formDisabled)
    {
        $form->freeze();
    }

    return $form;
}



function addUploadGroup($form, $aBanner, $vars)
{
        $uploadG = array();
        if (isset($vars['fileName']) && $vars['fileName'] != '') {
            $uploadG['radio1'] = $form->createElement('radio', $vars['radioName'], null, (empty($vars['imageName']) ? '' : "<img src='".OX::assetPath()."/images/".$vars['imageName']."' align='absmiddle'> ").$vars['fileName']."<i dir=".$GLOBALS['phpAds_TextDirection'].">(".$vars['fileSize'].")</i>", 'f');
            $uploadG['radio2'] = $form->createElement('radio', $vars['radioName'], null, null, 't');
            $uploadG['upload'] = $form->createElement('file', $vars['uploadName'], null, array("onchange" => "selectFile(this, ".($vars['handleSWF'] ? 'true' : 'false').")", "style" => "width: 250px;"));
            if ($vars['handleSWF']) {
                $uploadG['checkSWF'] = $form->createElement("checkbox", "checkswf", null, $GLOBALS['strCheckSWF']);
                $form->addDecorator('checkswf', 'tag',
                    array('attributes' =>
                        array('id' => 'swflayer', 'style' => 'display:none')));
            }

            $form->addGroup($uploadG, $vars['uploadName'].'_group', $vars['updateLabel'], array("<br>", "", "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"), false);
        }
        else { //add new creative
            $uploadG['hidden'] = $form->createElement("hidden", $vars['radioName'], "t");
            $uploadG['upload'] = $form->createElement('file', $vars['uploadName'], null, array("onchange" => "selectFile(this, ".($vars['handleSWF'] ? 'true' : 'false').")", "size" => 26, "style" => "width: 250px"));
            if ($vars['handleSWF']) {
                $uploadG['checkSWF'] = $form->createElement("checkbox", "checkswf", null, $GLOBALS['strCheckSWF']);
                $form->addDecorator('checkswf', 'tag',
                    array('attributes' =>
                        array('id' => 'swflayer', 'style' => 'display:none')));
            }

            $form->addGroup($uploadG, $vars['uploadName'].'_group', $vars['newLabel'], "<br>", false);

            if (!empty($vars['decorateId'])) {
                $form->addDecorator($vars['uploadName'].'_group', 'process', array('tag' => 'tr',
                    'addAttributes' => array('id' => $vars['decorateId'].'{numCall}',
                    'style' => 'display:none')));
            }

        }
        $form->setDefaults(array("checkswf" => "t")); //TODO does not work??
}


function processForm($bannerid, $form, &$oComponent, $formDisabled=false)
{
    $aFields = $form->exportValues();

    $doBanners = OA_Dal::factoryDO('banners');
    // Get the existing banner details (if it is not a new banner)
    if (!empty($bannerid)) {
        if ($doBanners->get($bannerid)) {
            $aBanner = $doBanners->toArray();
        }
    }

    $aVariables = array();
    $aVariables['campaignid']      = $aFields['campaignid'];
    $aVariables['target']          = isset($aFields['target']) ? $aFields['target'] : '';
//======================================================Modified By DAC016============================================//    
if($aFields['type'] == 'web') { 
    $con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],
$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

$query = mysql_query("select * from oxm_mobilezonesize where user_id=1") or die(mysql_error());
	while($row = mysql_fetch_assoc($query))
	{
			$width[$row['zonecategory']] = $row['width'];
			$height[$row['zonecategory']] = $row['height'];
			
	}
          
  /*  $aVariables['height']          = isset($height['1']) ? $height['1'] : 0;
    $aVariables['width']           = isset($width['1'])  ? $width['1'] : 0; */
    
       if($_POST['height'])
    {     
		$aVariables['height']         = $_POST['height'] ;
	}
	else
	{
		$aVariables['height']         = $height['1'];
	}
	if($_POST['width'])
    {     
		$aVariables['width']         = $_POST['width'] ;
	}
	else
	{
		$aVariables['width']         = $width['1'];
	}    
    
    
    
  }  
//======================================================Modified By DAC016============================================//
    
    $aVariables['weight']          = !empty($aFields['weight']) ? $aFields['weight'] : 0;
    $aVariables['adserver']        = !empty($aFields['adserver']) ? $aFields['adserver'] : '';
    $aVariables['alt']             = !empty($aFields['alt']) ? $aFields['alt'] : '';
    $aVariables['bannertext']      = !empty($aFields['bannertext']) ? $aFields['bannertext'] : '';
    $aVariables['htmltemplate']    = !empty($aFields['htmltemplate']) ? $aFields['htmltemplate'] : '';
    $aVariables['description']     = !empty($aFields['description']) ? $aFields['description'] : '';
    $aVariables['imageurl']        = (!empty($aFields['imageurl']) && $aFields['imageurl'] != 'http://') ? $aFields['imageurl'] : '';
    $aVariables['url']             = (!empty($aFields['url']) && $aFields['url'] != 'http://') ? $aFields['url'] : '';
    $aVariables['status']          = ($aFields['status'] != '') ? $aFields['status'] : '';
    $aVariables['statustext']      = !empty($aFields['statustext']) ? $aFields['statustext'] : '';
    $aVariables['storagetype']     = $aFields['type'];
    $aVariables['ext_bannertype']  = $aFields['ext_bannertype'];
    $aVariables['comments']        = $aFields['comments'];

    $aVariables['filename']        = !empty($aBanner['filename']) ? $aBanner['filename'] : '';
    $aVariables['contenttype']     = !empty($aBanner['contenttype']) ? $aBanner['contenttype'] : '';

  

    if ($aFields['type'] == 'url') {
        $aVariables['contenttype'] = OA_Creative_File::staticGetContentTypeByExtension($aVariables['imageurl']);
        if (empty($aVariables['contenttype'])) {
            // Assume dynamic urls (i.e. http://www.example.com/foo?bar) are "gif"
            $aVariables['contenttype'] = 'gif';
        }
    } elseif ($aFields['type'] == 'txt') {
        // Text banners should always have a "txt" content type
        $aVariables['contenttype'] = 'txt';
    }

    $aVariables['alt_filename']    = !empty($aBanner['alt_filename']) ? $aBanner['alt_filename'] : '';
    $aVariables['alt_contenttype'] = !empty($aBanner['alt_contenttype']) ? $aBanner['alt_contenttype'] : '';
    $aVariables['alt_imageurl']    = !empty($aFields['alt_imageurl']) ? $aFields['alt_imageurl'] : '';

    if (isset($aFields['keyword']) && $aFields['keyword'] != '') {
        $keywordArray = split('[ ,]+', $aFields['keyword']);
        $aVariables['keyword'] = implode(' ', $keywordArray);
    } else {
        $aVariables['keyword'] = '';
    }

    $editSwf = false;

    // Deal with any files that are uploaded.
    if (!empty($_FILES['upload']) && $aFields['replaceimage'] == 't') { //TODO refactor upload to be a valid quickform elem
        $oFile = OA_Creative_File::factoryUploadedFile('upload');
        checkForErrorFileUploaded($oFile);
        $oFile->store($aFields['type']);
        $aFile = $oFile->getFileDetails();

        if (!empty($aFile)) {
            $aVariables['filename']      = $aFile['filename'];
            $aVariables['contenttype']   = $aFile['contenttype'];
  //======================================================Modified By DAC016============================================//    
  if($aFields['type'] == 'web') {  
    $con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],
$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

$query = mysql_query("select * from oxm_mobilezonesize where user_id=1") or die(mysql_error());
	while($row = mysql_fetch_assoc($query))
	{
			$width[$row['zonecategory']] = $row['width'];
			$height[$row['zonecategory']] = $row['height'];
			
	}
          
	    $aVariables1['height']          = isset($height['1']) ? $height['1'] : 0;
	    $aVariables1['width']           = isset($width['1'])  ? $width['1'] : 0;
    
//======================================================Modified By DAC016============================================//
            
            $aVariables['pluginversion'] = $aFile['pluginversion'];
            $editSwf                     = $aFile['editswf'];

            $aVariables1['filename']      = $aFile['filename'];
            $aVariables1['contenttype']   = $aFile['contenttype'];
           
            
            $aVariables1['width']         = $aFile['width'];
            $aVariables1['height']        = $aFile['height'];
            
            
	    $aVariables1['pluginversion'] = $aFile['pluginversion'];

        }
//=======================================================Modified By DAC021===============================================================//

 /*   if (!empty($_FILES['upload1']) && $aFields['replaceimage'] == 't') { //TODO refactor upload to be a valid quickform elem
        $oFile1 = OA_Creative_File::factoryUploadedFile('upload1');
        checkForErrorFileUploaded($oFile1);
        $oFile1->store($aFields['type']);
        $aFile1 = $oFile1->getFileDetails();


        if (!empty($aFile1)) {
            $aVariables1['filename']      = $aFile1['filename'];
            $aVariables1['contenttype']   = $aFile1['contenttype'];
            $aVariables1['width']         = $aFile1['width'];
            $aVariables1['height']        = $aFile1['height'];
	   $aVariables1['pluginversion'] = $aFile1['pluginversion'];
        }
}*/

    if (!empty($_FILES['upload2']) && $aFields['replaceimage'] == 't') { //TODO refactor upload to be a valid quickform elem
        $oFile2 = OA_Creative_File::factoryUploadedFile('upload2');
        checkForErrorFileUploaded($oFile2);
        $oFile2->store($aFields['type']);
        $aFile2 = $oFile2->getFileDetails();

        if (!empty($aFile2)) {
            $aVariables2['filename']      = $aFile2['filename'];
            $aVariables2['contenttype']   = $aFile2['contenttype'];
            $aVariables2['width']         = $aFile2['width'];
            $aVariables2['height']        = $aFile2['height'];
	    $aVariables2['pluginversion'] = $aFile2['pluginversion'];
        }
}
    if (!empty($_FILES['upload3']) && $aFields['replaceimage'] == 't') { //TODO refactor upload to be a valid quickform elem
        $oFile3 = OA_Creative_File::factoryUploadedFile('upload3');
        checkForErrorFileUploaded($oFile3);
        $oFile3->store($aFields['type']);
        $aFile3 = $oFile3->getFileDetails();

        if (!empty($aFile3)) {
            $aVariables3['filename']      = $aFile3['filename'];
            $aVariables3['contenttype']   = $aFile3['contenttype'];
            $aVariables3['width']         = $aFile3['width'];
            $aVariables3['height']        = $aFile3['height'];
	    $aVariables3['pluginversion'] = $aFile3['pluginversion'];

        }
}
    if (!empty($_FILES['upload4']) && $aFields['replaceimage'] == 't') { //TODO refactor upload to be a valid quickform elem
        $oFile4 = OA_Creative_File::factoryUploadedFile('upload4');
        checkForErrorFileUploaded($oFile4);
        $oFile4->store($aFields['type']);
        $aFile4 = $oFile4->getFileDetails();

        if (!empty($aFile4)) {
            $aVariables4['filename']      = $aFile4['filename'];
            $aVariables4['contenttype']   = $aFile4['contenttype'];
            $aVariables4['width']         = $aFile4['width'];
            $aVariables4['height']        = $aFile4['height'];
	    $aVariables4['pluginversion'] = $aFile4['pluginversion'];

        }
}
//=======================================================Modified By DAC021===============================================================//
        // Delete the old file for this banner
        if (!empty($aBanner['filename']) && ($aBanner['filename'] != $aFile['filename']) && ($aBanner['storagetype'] == 'web' || $aBanner['storagetype'] == 'sql')) {
            DataObjects_Banners::deleteBannerFile($aBanner['storagetype'], $aBanner['filename']);
        }
        
      }
    }
    if (!empty($_FILES['uploadalt']) && $_FILES['uploadalt']['size'] > 0
        &&  $aFields['replacealtimage'] == 't') {

        //TODO: Check image only? - Wasn't enforced before
        $oFile = OA_Creative_File::factoryUploadedFile('uploadalt');
        checkForErrorFileUploaded($oFile);
        $oFile->store($aFields['type']);
        $aFile = $oFile->getFileDetails();

        if (!empty($aFile)) {
            $aVariables['alt_filename']    = $aFile['filename'];
            $aVariables['alt_contenttype'] = $aFile['contenttype'];
        }
    }

    // Handle SWF transparency
    if ($aVariables['contenttype'] == 'swf') {
        $aVariables['transparent'] = isset($aFields['transparent']) && $aFields['transparent'] ? 1 : 0;
    }

    // Update existing hard-coded links if new file has not been uploaded
    if ($aVariables['contenttype'] == 'swf' && empty($_FILES['upload']['tmp_name'])
        && isset($aFields['alink']) && is_array($aFields['alink']) && count($aFields['alink'])) {
        // Prepare the parameters
        $parameters_complete = array();

        // Prepare targets
        if (!isset($aFields['atar']) || !is_array($aFields['atar'])) {
            $aFields['atar'] = array();
        }

        foreach ($aFields['alink'] as $key => $val) {
            if (substr($val, 0, 7) == 'http://' && strlen($val) > 7) {
                if (!isset($aFields['atar'][$key])) {
                    $aFields['atar'][$key] = '';
                }

                if (isset($aFields['alink_chosen']) && $aFields['alink_chosen'] == $key) {
                    $aVariables['url'] = $val;
                    $aVariables['target'] = $aFields['atar'][$key];
                }

/*
                if (isset($aFields['asource'][$key]) && $aFields['asource'][$key] != '') {
                    $val .= '|source:'.$aFields['asource'][$key];
                }
*/
                $parameters_complete[$key] = array(
                    'link' => $val,
                    'tar'  => $aFields['atar'][$key]
                );
            }
        }

        $parameters = array('swf' => $parameters_complete);
    } else {
        $parameters = null;
    }

    $aVariables['parameters'] = serialize($parameters);

    //TODO: deleting images is not viable because they could still be in use in the delivery cache
    //    // Delete any old banners...
    //    if (!empty($aBanner['filename']) && $aBanner['filename'] != $aVariables['filename']) {
    //        phpAds_ImageDelete($aBanner['storagetype'], $aBanner['filename']);
    //    }
    //    if (!empty($aBanner['alt_filename']) && $aBanner['alt_filename'] != $aVariables['alt_filename']) {
    //        phpAds_ImageDelete($aBanner['storagetype'], $aBanner['alt_filename']);
    //    }

    // Clients are only allowed to modify certain fields, ensure that other fields are unchanged
    if (OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER)) {
        $aVariables['weight']       = $aBanner['weight'];
        $aVariables['description']  = $aBanner['name'];
        $aVariables['comments']     = $aBanner['comments'];
    }

    $insert = (empty($bannerid)) ? true : false;

    if ($oComponent)
    {
        $result = $oComponent->preprocessForm($insert, $bannerid, $aFields, $aVariables);
        if ($result === false)
        {
            // handle error
            return false;
        }
    }

/* openxmods - DAC009 -DAC016*/



	 if($aVariables['storagetype'] == 'web')
	 $aVariables['pluginversion'] = '-2';
	 if($aVariables['storagetype'] == 'txt')
	 $aVariables['pluginversion'] = '-3';
	 if($aVariables['storagetype'] == 'html')
	 $aVariables['pluginversion'] = '-4';
	 
/* openxmods - DAC009 -DAC016*/

    // File the data
    $doBanners->setFrom($aVariables);
    if ($insert) {



	$result= mysql_query("select * from oxm_mobilezonesize");
		
			while($row = mysql_fetch_assoc($result))
			{
		
			$width[$row['zonecategory']] = $row['width'];
			$height[$row['zonecategory']] = $row['height'];
			
			}

	if($aFile['width'] && $aFile['height'])
	{
			if($width[1]!=$aFile['width'] || $height[1]!=$aFile['height'] )
			
			{
				$translation = new OX_Translation ();
  $translated_message = $translation->translate('<b style="color:red;">Upload Banner1 in Given size</b>',
			    array (
				MAX::constructURL ( MAX_URL_ADMIN, 'banner-edit.php?clientid='.$aFields['clientid'].'&campaignid='.$aFields['campaignid'].'&bannerid='.$aFields['bannerid'] ),
				htmlspecialchars ( $aFields ['description'])
			    ));
			    OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
			    $nextPage = "mobilebanner-edit.php?clientid=".$aFields['clientid']."&campaignid=".$aFields['campaignid']."&bannerid=$bannerid";
	
    // Go to the next page
    Header("Location: $nextPage");
    exit;
			}
	}
	
	if($aFile2['width'] && $aFile2['height'])
	{
			if($width[2]!=$aFile2['width'] || $height[2]!=$aFile2['height'] )
			
			{
				$translation = new OX_Translation ();
  $translated_message = $translation->translate('<b style="color:red;"> Upload Banner2 in Given size</b>',
			    array (
				MAX::constructURL ( MAX_URL_ADMIN, 'banner-edit.php?clientid='.$aFields['clientid'].'&campaignid='.$aFields['campaignid'].'&bannerid='.$aFields['bannerid'] ),
				htmlspecialchars ( $aFields ['description'])
			    ));
			    OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
			    $nextPage = "mobilebanner-edit.php?clientid=".$aFields['clientid']."&campaignid=".$aFields['campaignid']."&bannerid=$bannerid";

    // Go to the next page
    Header("Location: $nextPage");
    exit;
			}
	
	}
		
	if($aFile3['width'] && $aFile3['height'])
	{
			if($width[3]!=$aFile3['width'] || $height[3]!=$aFile3['height'] )
			{
				$translation = new OX_Translation ();
  $translated_message = $translation->translate('<b style="color:red;">Upload Banner3 in Given size</b>',
			    array (
				MAX::constructURL ( MAX_URL_ADMIN, 'banner-edit.php?clientid='.$aFields['clientid'].'&campaignid='.$aFields['campaignid'].'&bannerid='.$aFields['bannerid'] ),
				htmlspecialchars ( $aFields ['description'])
			    ));
			    OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
			    $nextPage = "mobilebanner-edit.php?clientid=".$aFields['clientid']."&campaignid=".$aFields['campaignid']."&bannerid=$bannerid";
		
    // Go to the next page
    Header("Location: $nextPage");
    exit;
			}
	
	
	
	}
	
	if($aFile4['width'] && $aFile4['height'])
	{
	
			if($width[4]!=$aFile4['width'] || $height[4]!=$aFile4['height'] )
			{
				$translation = new OX_Translation ();
  $translated_message = $translation->translate('<b style="color:red;"> Upload Banner4 in Given size</b>',
			    array (
				MAX::constructURL ( MAX_URL_ADMIN, 'banner-edit.php?clientid='.$aFields['clientid'].'&campaignid='.$aFields['campaignid'].'&bannerid='.$aFields['bannerid'] ),
				htmlspecialchars ( $aFields ['description'])
			    ));
			    OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
			    $nextPage = "mobilebanner-edit.php?clientid=".$aFields['clientid']."&campaignid=".$aFields['campaignid']."&bannerid=$bannerid";

    // Go to the next page
    Header("Location: $nextPage");
    exit;
			}
	
	}	 


		 //********s**********************************validation**********************************************



        $bannerid = $doBanners->insert();
   	 

        $con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],
$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

        if($aFields['type'] == 'web') {
	mysql_query("update rv_banners set masterbanner = -2, pluginversion=0 where bannerid =".$bannerid) or die(mysql_query());        



//======================================================Modified By DAC016============================================//       
       $campaignid = $aFields['campaignid'];

  	$zon=mysql_query("select zone_id from ".$table_prefix."placement_zone_assoc where  placement_id = ".$campaignid)or die(mysql_error());

    if(mysql_num_rows($zon)>0)
     {
  	   while($sele=mysql_fetch_array($zon)){

		             	     $j=$sele['zone_id'];

	               	             $k=$bannerid;

	               	               	 for($i=0;$i<=3;$i++){	
               	               
	               	               	 $j++;
					 $k++;	
	               	                         	               	 
	  mysql_query("INSERT INTO ".$table_prefix."ad_zone_assoc (zone_id,ad_id,priority,link_type,priority_factor) VALUES ('".$j."','".$k."','0','1','1')") or die('error in');			
	               	               	 }
				   
	
	  }

     }
    }
    
            if($aFields['type'] == 'txt') { 
 mysql_query("update ".$table_prefix."banners set masterbanner = -3 , pluginversion=0 where bannerid =".$bannerid) or die(mysql_query());                   
	}
            if($aFields['type'] == 'html') { 



 mysql_query("update ".$table_prefix."banners set masterbanner = -4 ,is_dj_html='".$_REQUEST['3rdparty']."',pluginversion=0, width = ".$_POST['width'].", height = ".$_POST['height']." where bannerid =".$bannerid) or die(mysql_query());                   
	}
//======================================================Modified By DAC016============================================//       

//======================================================Modified By DAC021============================================//

if($aFields['type'] == 'web') { 
$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],
$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

$query = mysql_query("select * from oxm_mobilezonesize where user_id=1") or die(mysql_error());
	while($row = mysql_fetch_assoc($query))
	{
			$width[$row['zonecategory']] = $row['width'];
			$height[$row['zonecategory']] = $row['height'];
			
	}

	//$last_bannerid = mysql_insert_id();

mysql_query("insert into ".$table_prefix."banners(campaignid,target,height,width,weight,adserver,alt,bannertext,htmltemplate,description,imageurl,url,status,statustext,storagetype,ext_bannertype,comments,filename,contenttype,alt_filename,alt_contenttype,alt_imageurl,keyword,pluginversion,parameters,masterbanner)
values('".$aVariables['campaignid']."', '".$aVariables['target']."', '".$height[1]."', '".$width[1]."', '".$aVariables['weight']."', '".$aVariables['adserver']."', '".$aVariables['alt']."', '".$aVariables['bannertext']."', '".$aVariables['htmltemplate']."', '".$aVariables['description']." [ Mobile Banner 1]', '".$aVariables['imageurl']."', '".$aVariables['url']."', '".$aVariables['status']."', '".$aVariables['statustext']."', '".$aVariables['storagetype']."', '".$aVariables['ext_bannertype']."', '".$aVariables['comments']."', '".$aFile['filename']."', '".$aFile['contenttype']."', '".$aVariables['alt_filename']."', '".$aVariables['alt_contenttype']."', '".$aVariables['alt_imageurl']."', '".$aVariables['keyword']."' , '".$aFile['pluginversion']."', '".$aVariables['parameters']."',".$bannerid.") ") or die(mysql_error());

mysql_query("insert into ".$table_prefix."banners(campaignid,target,height,width,weight,adserver,alt,bannertext,htmltemplate,description,imageurl,url,status,statustext,storagetype,ext_bannertype,comments,filename,contenttype,alt_filename,alt_contenttype,alt_imageurl,keyword,pluginversion,parameters,masterbanner)
values('".$aVariables['campaignid']."', '".$aVariables['target']."', '".$height[2]."', '".$width[2]."', '".$aVariables['weight']."', '".$aVariables['adserver']."', '".$aVariables['alt']."', '".$aVariables['bannertext']."', '".$aVariables['htmltemplate']."', '".$aVariables['description']." [ Mobile Banner 2]', '".$aVariables['imageurl']."', '".$aVariables['url']."', '".$aVariables['status']."', '".$aVariables['statustext']."', '".$aVariables['storagetype']."', '".$aVariables['ext_bannertype']."', '".$aVariables['comments']."', '".$aFile2['filename']."', '".$aFile2['contenttype']."', '".$aVariables['alt_filename']."', '".$aVariables['alt_contenttype']."', '".$aVariables['alt_imageurl']."', '".$aVariables['keyword']."' , '".$aFile2['pluginversion']."', '".$aVariables['parameters']."', ".$bannerid.") ") or die(mysql_error());

mysql_query("insert into ".$table_prefix."banners(campaignid,target,height,width,weight,adserver,alt,bannertext,htmltemplate,description,imageurl,url,status,statustext,storagetype,ext_bannertype,comments,filename,contenttype,alt_filename,alt_contenttype,alt_imageurl,keyword,pluginversion,parameters,masterbanner)
values('".$aVariables['campaignid']."', '".$aVariables['target']."', '".$height[3]."', '".$width[3]."', '".$aVariables['weight']."', '".$aVariables['adserver']."', '".$aVariables['alt']."', '".$aVariables['bannertext']."', '".$aVariables['htmltemplate']."', '".$aVariables['description']." [ Mobile Banner 3]', '".$aVariables['imageurl']."', '".$aVariables['url']."', '".$aVariables['status']."', '".$aVariables['statustext']."', '".$aVariables['storagetype']."', '".$aVariables['ext_bannertype']."', '".$aVariables['comments']."', '".$aFile3['filename']."', '".$aFile3['contenttype']."', '".$aVariables['alt_filename']."', '".$aVariables['alt_contenttype']."', '".$aVariables['alt_imageurl']."', '".$aVariables['keyword']."' , '".$aFile3['pluginversion']."', '".$aVariables['parameters']."', ".$bannerid.") ") or die(mysql_error());

mysql_query("insert into ".$table_prefix."banners(campaignid,target,height,width,weight,adserver,alt,bannertext,htmltemplate,description,imageurl,url,status,statustext,storagetype,ext_bannertype,comments,filename,contenttype,alt_filename,alt_contenttype,alt_imageurl,keyword,pluginversion,parameters,masterbanner)
values('".$aVariables['campaignid']."', '".$aVariables['target']."','".$height[4]."', '".$width[4]."', '".$aVariables['weight']."', '".$aVariables['adserver']."', '".$aVariables['alt']."', '".$aVariables['bannertext']."', '".$aVariables['htmltemplate']."', '".$aVariables['description']." [ Mobile Banner 4]', '".$aVariables['imageurl']."', '".$aVariables['url']."', '".$aVariables['status']."', '".$aVariables['statustext']."', '".$aVariables['storagetype']."', '".$aVariables['ext_bannertype']."', '".$aVariables['comments']."', '".$aFile4['filename']."', '".$aFile4['contenttype']."', '".$aVariables['alt_filename']."', '".$aVariables['alt_contenttype']."', '".$aVariables['alt_imageurl']."', '".$aVariables['keyword']."' , '".$aFile4['pluginversion']."', '".$aVariables['parameters']."', ".$bannerid.") ") or die(mysql_error());

}
//=======================================================Modified By DAC021===============================================================//


        // Run the Maintenance Priority Engine process
        OA_Maintenance_Priority::scheduleRun();
    } else {
        

           //$doBanners->width = $_POST['width'];
           //$doBanners->height = $_POST['height'];


if($aFile['width'] && $aFile['height'])
{

if ($aFile['width'] != $aBanner['width'] || $aFile['height'] != $aBanner['height']) {

		
	$translation = new OX_Translation ();
	
  	$translated_message = $translation->translate('<b style="color:red;">Kindly upload Banner of size '.$aBanner['width'].'X'.$aBanner['height'].' </b>',
			    array (
				MAX::constructURL ( MAX_URL_ADMIN, 'mobilebanner-edit.php?clientid='.$aFields['clientid'].'&campaignid='.$aFields['campaignid'].'&bannerid='.$aFields['bannerid'] ),
				htmlspecialchars ( $aFields ['description'])
			    ));
			    OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
			    $nextPage = "mobilebanner-edit.php?clientid=".$aFields['clientid']."&campaignid=".$aFields['campaignid']."&bannerid=$bannerid";
	
    // Go to the next page
    Header("Location: $nextPage");

exit;
           
        }

}
if($_REQUEST['3rdparty']==2)
{
$strupdate=",url='',target=''";
}
else
{
$strupdate='';
}
mysql_query("update ".$table_prefix."banners set is_dj_html='".$_REQUEST['3rdparty']."'$strupdate where bannerid =".$bannerid);


        $doBanners->update();
        // check if size has changed
        if ($aVariables['width'] != $aBanner['width'] || $aVariables['height'] != $aBanner['height']) {
            MAX_adjustAdZones($bannerid);
            MAX_addDefaultPlacementZones($bannerid, $aVariables['campaignid']);
        }
    }
    if ($oComponent)
    {
        $result = $oComponent->processForm($insert, $bannerid, $aFields, $aVariables);
        if ($result === false)
        {
            // handle error
            // remove rec from banners table?
            return false;
        }
    }

    $translation = new OX_Translation ();
    if ($insert) {
        // Queue confirmation message
//=======================================================Modified By DAC021===============================================================//
if($aFields['type'] == 'web') { 
					$desc = "Parent Banner ".$aFields['description']." has been added with Four Mobile Banners";
		
        $translated_message = $translation->translate ( $desc, array(
            MAX::constructURL(MAX_URL_ADMIN, 'banner-edit.php?clientid=' .  $aFields['clientid'] . '&campaignid=' . $aFields['campaignid'] . '&bannerid=' . $bannerid),
           // htmlspecialchars($aFields['description'])
           

        ));
        OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
        
        }
if($aFields['type'] == 'txt') { 
					$desc = "Parent Banner ".$aFields['description']." has been added ";
		
        $translated_message = $translation->translate ( $desc, array(
            MAX::constructURL(MAX_URL_ADMIN, 'banner-edit.php?clientid=' .  $aFields['clientid'] . '&campaignid=' . $aFields['campaignid'] . '&bannerid=' . $bannerid),
           // htmlspecialchars($aFields['description'])
           

        ));
        OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
        
        }
        if($aFields['type'] == 'html') { 
					$desc = "Parent Banner ".$aFields['description']." has been added ";
		
        $translated_message = $translation->translate ( $desc, array(
            MAX::constructURL(MAX_URL_ADMIN, 'banner-edit.php?clientid=' .  $aFields['clientid'] . '&campaignid=' . $aFields['campaignid'] . '&bannerid=' . $bannerid),
           // htmlspecialchars($aFields['description'])
           

        ));
        OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
        
        }
//=======================================================Modified By DAC021===============================================================//
       
        // Determine what the next page is
        if ($editSwf) {
            $nextPage = "banner-swf.php?clientid=".$aFields['clientid']."&campaignid=".$aFields['campaignid']."&bannerid=$bannerid&insert=true";
        }
        else {
            $nextPage = "campaign-banners.php?clientid=".$aFields['clientid']."&campaignid=".$aFields['campaignid'];
        }
    }
    else {
        // Determine what the next page is
        if ($editSwf) {
            $nextPage = "banner-swf.php?clientid=".$aFields['clientid']."&campaignid=".$aFields['campaignid']."&bannerid=$bannerid";
        }
        else {
        
            $translated_message = $translation->translate($GLOBALS['strBannerHasBeenUpdated'],
            array (
                MAX::constructURL ( MAX_URL_ADMIN, 'banner-edit.php?clientid='.$aFields['clientid'].'&campaignid='.$aFields['campaignid'].'&bannerid='.$aFields['bannerid'] ),
                htmlspecialchars ( $aFields ['description'])
            ));

            
            OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
            $nextPage = "mobilebanner-edit.php?clientid=".$aFields['clientid']."&campaignid=".$aFields['campaignid']."&bannerid=$bannerid";
        }
    }

    // Go to the next page
    Header("Location: $nextPage");
    exit;
}

function checkForErrorFileUploaded($oFile)
{
	if (PEAR::isError($oFile)) {
	    phpAds_PageHeader(1);
	    phpAds_Die($GLOBALS['strErrorOccurred'], htmlspecialchars($oFile->getMessage()). "<br>Please make sure you selected a valid file.");
	}
}

function _getContentTypeIconImageName($contentType)
{
    $imageName = '';
    if (empty($contentType)) {
        return $imageName;
    }

    switch ($contentType) {
        case 'swf':
        case 'dcr':  $imageName = 'icon-filetype-swf.gif'; break;
        case 'jpeg': $imageName = 'icon-filetype-jpg.gif'; break;
        case 'gif':  $imageName = 'icon-filetype-gif.gif'; break;
        case 'png':  $imageName = 'icon-filetype-png.gif'; break;
        case 'rpm':  $imageName = 'icon-filetype-rpm.gif'; break;
        case 'mov':  $imageName = 'icon-filetype-mov.gif'; break;
        default:     $imageName = 'icon-banner-stored.gif'; break;
    }

    return $imageName;
}


function _getBannerSizeText($type, $filename)
{
    $size = phpAds_ImageSize($type, $filename);
    if (round($size / 1024) == 0) {
         $size = $size." bytes";
    }
    else {
         $size = round($size / 1024)." Kb";
    }

    return $size;
}
?>
