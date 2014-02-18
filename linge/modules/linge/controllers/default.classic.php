<?php
/**
* @package   linge
* @subpackage linge
* @author    your name
* @copyright 2011 your name
* @link      http://www.yourwebsite.undefined
* @license    All rights reserved
*/

class defaultCtrl extends jController {
    /**
    *
    */
    function index() {
        $rep = $this->getResponse('html');
        
        $rep->bodyTpl = 'linge~layout' ;
        
        $tpl = new jTpl();  
       
        $cnx = jDb::getConnection();
		
		jClasses::inc('linge~restclient');
		$params = array('type' => 'parent');
		$client = RestClient::get('http://ornythorink.alwaysdata.net/index.php/vroum/category/', $params);
		$categoriesParent = json_decode($client->getResponse());
		
		$params = array('type' => 'child');
		$client = RestClient::get('http://ornythorink.alwaysdata.net/index.php/vroum/category/', $params);
		$categoriesChild = json_decode($client->getResponse());
		
		$tpl->assign('categoriesParent', $categoriesParent);		
		$tpl->assign('categoriesChild', $categoriesChild);	

		if( $this->param('q') === NULL ){
	    
	        $sql = " SELECT * FROM produits  WHERE status = 'Ok'  ORDER BY RAND()   LIMIT 0,6" ;
	        $hoffres = $cnx->query($sql)->fetchAll();
	
	        foreach($hoffres as $image){
	            if($image->longimage !== null){
	                $image->longimage = $this->resizeImage($image->longimage);                
	            } elseif ($image->mediumimage !== null) {
	                $image->mediumimage = $this->resizeImage($image->mediumimage);
	            } elseif ($image->petiteimage !== null) {
	                $image->petiteimage = $this->resizeImage($image->petiteimage);
	            }
	            // Création d'une nouvelle ressource cURL
	            /*$ch = curl_init();
	            
	            // On récupère la réponse HTTP dans une chaîne
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	            
	            // La cible
	            curl_setopt($ch, CURLOPT_URL, $image->url);
	            
	            // On récupère les en-têtes dans la réponse (là où y'a le "Location")
	            curl_setopt($ch, CURLOPT_HEADER, 1);
	            
	            // Récupération de l'URL et affichage sur le naviguateur
	            $res = curl_exec($ch);
	            
	            // Dans cette réponse, il y a les en-têtes HTTP ;
	            // ON extrait le champ "Location".
	            preg_match('/Location: (.+?)\r?\n/i', $res, $matches);
	            
	            echo $matches[1]; // Affiche la "Location"
	            
	            
	            // Fermeture de la session cURL
	            curl_close($ch);*/				
				
				
	        }  
	        $tpl->assign( 'hoffres', $hoffres  );
			$rep->body->assign('MAIN', $tpl->fetch("linge~main")); 	
		} else {
			$params = array('term' => $this->param('q') );
			$client = RestClient::get('http://ornythorink.alwaysdata.net/index.php/vroum/produits/', $params);
			$produits = json_decode($client->getResponse());
	        foreach($produits as $image){
	            if($image->longimage !== null){
	                $image->longimage = $this->resizeImage($image->longimage);                
	            } elseif ($image->mediumimage !== null) {
	                $image->mediumimage = $this->resizeImage($image->mediumimage);
	            } elseif ($image->petiteimage !== null) {
	                $image->petiteimage = $this->resizeImage($image->petiteimage);
	            }
	        }  			
	        $tpl->assign( 'produits' , $produits  );			
			$rep->body->assign('MAIN', $tpl->fetch("linge~listing")); 						
		}		

		
        return $rep;
    }
    
    function resizeImage($url){
		
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
        
        $rand  = rand(1,100000);
        $ok = jFile::write( $cache . '/var/upload/img'. $rand . '.' . strtolower($extensions[$infos_image[2]]) , $content);
         
        $params = array('maxwidth'=>190, 'maxheight'=>242,'background'=>'#ffffff','zoom'=>100);
        $att = jImageModifier::get('../var/upload/img'. $rand . '.jpg', $params);

        return  $att['src'];
    }
}
