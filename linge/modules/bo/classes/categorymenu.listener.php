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
        
            $event->add(new masterAdminMenuItem('bolist', 'category white list Zanox', jUrl::get('bo~category:zanoxWhiteListe'), 1, 'crud'));
            $event->add(new masterAdminMenuItem('bolist', 'category white list Netaff', jUrl::get('bo~category:netaffWhiteListe'), 1, 'crud'));
            $event->add(new masterAdminMenuItem('bolist', 'category white list Effil', jUrl::get('bo~category:effilWhiteListe'), 1, 'crud'));            
    }
}
