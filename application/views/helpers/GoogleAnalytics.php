<?php

class Zend_View_Helper_GoogleAnalytics extends Zend_View_Helper_Abstract
{
    public function googleAnalytics()
    {
        $html = '';
        
        $fc = Zend_Controller_Front::getInstance();
        $config = $fc->getParam('bootstrap')->getOptions();
        if (isset($config['analytics']['apikey']) && $config['analytics']['apikey']) {
            $key = $config['analytics']['apikey'];
            $html = <<<EOT

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("$key");
pageTracker._trackPageview();
} catch(err) {}</script>


EOT;
        }
        return $html;
    }
}