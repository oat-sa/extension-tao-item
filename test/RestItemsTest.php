<?php
require_once dirname(__FILE__) . '/../../tao/test/RestTestCase.php';

class RestItemsTest extends RestTestCase
{
    public function serviceProvider(){
        return array(
            array('taoItems/RestItems')
        );
    }
}

?>