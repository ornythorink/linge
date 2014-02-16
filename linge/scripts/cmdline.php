<?php
/**
* @package   linge
* @subpackage 
* @author    your name
* @copyright 2011 your name
* @link      http://www.yourwebsite.undefined
* @license    All rights reserved
*/

require_once (__DIR__.'/../application.init.php');

checkAppOpened();

require_once (JELIX_LIB_CORE_PATH.'jCmdlineCoordinator.class.php');

require_once (JELIX_LIB_CORE_PATH.'request/jCmdLineRequest.class.php');

jApp::setCoord(new jCmdlineCoordinator('cmdline/cmdline.ini.php'));
jApp::coord()->process(new jCmdLineRequest());

