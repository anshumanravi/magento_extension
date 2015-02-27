<?php
/**
 * Copyright 2015 Zendesk
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * In version 2.0 we are replacing the deprecated Feedback Tab with the new
 * Embeddables Web Widget.
 * More info: https://www.zendesk.com/embeddables
 *
 * In this data upgrade we are going to drop the Feedback Tab related settings
 * from the database, and inserting the required fields for the Web Widget.
 */

$config = new Mage_Core_Model_Config();

// We won't need the Feedback Tab code snippet anymore
$config->deleteConfig('zendesk/features/feedback_tab_code');

// We won't check in our code whether to show or not the Feedback Tab
$config->deleteConfig('zendesk/features/feedback_tab_code_active');

// Retrieve the domain from the config settings
$domain = Mage::getStoreConfig('zendesk/general/domain');

if($domain) {
    // We are activating the Web Widget by default
    $config->saveConfig('zendesk/features/web_widget_code_active', 1);

    // The Web Widget code snippet, using the account zendesk domain from settings
    $webWidgetSnippet=<<<EOJS
<!-- Start of zendesk Zendesk Widget script -->
<script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement("iframe");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src="javascript:false",r.title="",r.role="presentation",(r.frameElement||r).style.cssText="display: none",d=document.getElementsByTagName("script"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(c){n=document.domain,r.src='javascript:var d=document.open();d.domain="'+n+'";void(0);',o=s}o.open()._l=function(){var o=this.createElement("script");n&&(this.domain=n),o.id="js-iframe-async",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload="document._l();">'),o.close()}("//assets.zendesk.com/embeddable_framework/main.js","{$domain}");/*]]>*/</script>
<!-- End of zendesk Zendesk Widget script -->
EOJS;

    $config->saveConfig('zendesk/features/web_widget_code_snippet', $webWidgetSnippet);
} else {
    // There is no domain on the settings, we can't activate the Web Widget
    // The user should probably re-run the Setup from the Zendesk extension settings page
    $config->saveConfig('zendesk/features/web_widget_code_active', 0);
    $config->saveConfig('zendesk/features/web_widget_code_snippet', '');
}
