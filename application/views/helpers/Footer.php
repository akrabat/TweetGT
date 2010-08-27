<?php

class Zend_View_Helper_Footer extends Zend_View_Helper_Abstract
{
    public function footer()
    {
        $year = date('Y');
        if ($year != 2010) {
            $year = "2010, $year";
        }
        return <<<EOT
    <p>Copyright &copy; $year by <a href="http://akrabat.com">Rob Allen</a>.<br/>
    This application is built with <a href="http://framework.zend.com">Zend Framework</a>.
    Source code is available on <a href="http://www.github.com/akrabat/TweetGT">github</a> 
    and licensed under the <a href="http://akrabat.com/license/new-bsd/">New BSD</a>.


EOT;
    }
}