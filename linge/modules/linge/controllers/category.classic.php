<?php
/**
* @package   linge
* @subpackage linge
* @author    your name
* @copyright 2011 your name
* @link      http://www.yourwebsite.undefined
* @license    All rights reserved
*/

class categoryCtrl extends jController {
    /**
    *
    */
    function index() {
    	
        $rep = $this->getResponse('html');
        
        $rep->bodyTpl = 'linge~layout' ;
        
        $tpl = new jTpl();  
       
		$monfichier = jApp::configPath('defaultconfig.ini.php');
		$ini = new jIniFileModifier ($monfichier);
		$domaine = $ini->getValue('domaine');
	    $wsurl 	 = $ini->getValue('wsurl');
		
		jClasses::inc('linge~restclient');
		$params = array('type' => 'parent');
		$client = RestClient::get($wsurl . '/index.php/vroum/category/', $params);
		$categoriesParent = json_decode($client->getResponse());
		
		$params = array('type' => 'child');
		$client = RestClient::get($wsurl . 'index.php/vroum/category/', $params);
		$categoriesChild = json_decode($client->getResponse());
		
		$params = array('type' => 'sub');
		$client = RestClient::get($wsurl . 'index.php/vroum/category/', $params);
		$categoriesSub = json_decode($client->getResponse());
		
		$tpl->assign('categoriesParent', $categoriesParent);		
		$tpl->assign('categoriesChild', $categoriesChild);
		$tpl->assign('categoriesSub', $categoriesSub);		

		
		$tpl->assign( 'domaine' ,  $domaine );
						
			$params = array('term' => $this->param('q') , 'offset' => $this->param('offset') );
			$id = array('id' => $this->param('id') );

			$client = RestClient::get($wsurl . 'index.php/vroum/produits/produitsCategory', $params);

			$tail = "";
			if($rest = substr($client->getResponse(), -1) != "]" ) {
				$tail = "]";
			}			
			
			$produits = json_decode($client->getResponse().$tail);

	        foreach($produits as $image){    	
	        	
	        	if($image->imagecache != '' && $image->imagecache != null){
	        		$image->longimage   = $image->imagecache;
	        		$image->mediumimage = $image->imagecache;
	        		$image->petiteimage = $image->imagecache;
	        	}        	
   		        	
	        	$cache = false;
	        	if($image->source != 'SDC'){
	        		$cache = true;
	        	}

        		if( ($image->longimage !== null &&  $image->longimage != '') && $image->imagecache  == null){ 	  					
	                $image->longimage = $this->resizeImage($image->longimage, $cache);  	                	                              
	            } elseif ( ($image->mediumimage !== null &&  $image->mediumimage != '') && $image->imagecache  == null) {     	
	                $image->mediumimage = $this->resizeImage($image->mediumimage, $cache);	                	                
	            } elseif (  ($image->petiteimage !== null &&  $image->petiteimage != '')  && $image->imagecache  == null) {            		            	
	                $image->petiteimage = $this->resizeImage($image->petiteimage, $cache);
	            }
	        }  	

	        $tpl->assign( 'produits' , $produits  );
	        $tpl->assign( 'q' , $this->param('q'));
	        $tpl->assign( 'id' , $this->param('id'));
	        $tpl->assign( 'offset' , $this->param('offset'));
	        
			$rep->body->assign('MAIN', $tpl->fetch("linge~listing")); 						
		
		
        return $rep;
    }
    
    function resizeImage($url, $useCache){
		
		if($url == ""){
			return null;
		}
		$extensions = array(
		 1 => 'GIF', 2 => 'JPG',3 => 'PNG', 4 => 'SWF', 5 => 'PSD',
            6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF', 9 => 'JPC', 10 => 'JP2', 11 => 'JPX',
           12 => 'JB2', 13 => 'SWC', 14 => 'IFF');
		
		$infos_image = getImageSize($url);		

		
		$ch = curl_init ($url) ;
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1) ;
        $content = curl_exec ($ch) ;
        curl_close ($ch) ;
		
        $config = jApp::configPath('defaultconfig.ini.php');
        $ini = new jIniFileModifier ($config);
        $cache = $ini->getValue('imagecache','0');
        
        $rand  = rand(1,10000000);
        $ok = jFile::write( $cache . '/var/upload/img'. $rand . '.' . strtolower($extensions[$infos_image[2]]) , $content);
         
        $params = array('maxwidth'=>190, 'maxheight'=>242,'background'=>'#ffffff','zoom'=>100);
        $att = jImageModifier::get('../var/upload/img'. $rand . '.jpg', $params);

        if($useCache == true){
	        // instanciation de la factory
	        $maFactory = jDao::get("linge~produits");
	        
			$conditions = jDao::createConditions();
			$conditions->startGroup('OR');
				$conditions->addCondition('petiteimage','=',$url);
				$conditions->addCondition('mediumimage','=',$url);
				$conditions->addCondition('longimage','=',$url);	        
			$conditions->endGroup();
	         
	        $liste = $maFactory->findBy($conditions)->fetch();    
	        if($liste != null && $liste != false){   
		        $liste->imagecache = $att['src'];
		        $maFactory->update($liste);
	        }
        }
        return  $att['src'];
    }
}
