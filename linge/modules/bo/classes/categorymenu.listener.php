<?php
/**
* @package   linge
* @subpackage bo
* @author    your name
* @copyright 2011 your name
* @link      http://www.yourwebsite.undefined
* @license    All rights reserved
*/

class categorymenuListener extends jEventListener {

    /**
    *
    */
    function onmasteradminGetMenuContent ($event) {
        
            $event->add(new masterAdminMenuItem('bolist', 'category', jUrl::get('bo~category:index'), 1, 'crud'));
    }
}
