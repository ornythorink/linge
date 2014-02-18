<?php

class netaffcsvCtrl extends jControllerCmdLine {
    
    
    public $paths =  "/home/ornythorink/linge/linge/var/uploads/netaff/" ;
    
    public function run(){
         $rep = $this->getResponse(); 
         
         jLog::log('start to process' ,'default');
  
  
  		 $this->nettoieProduits();      
         
          // on vide l'arborescence   
         $this->nettoieStoreTree();
         
         // on la reconstruit
         $this->createStoreTree();
         
         // on importe les csv
         $this->getCSV();
         
         $this->importCSV();
    
         // on teste et on applique le reset si necessaire
         $this->resetConfig();         
         
         return $rep;
    }
    

    public function nettoieProduits(){
        $rep = $this->getResponse(); 
        
         $cnx = jDb::getConnection();
        
        $dbh = $cnx->prepare( "DELETE FROM produits  WHERE source = 'NET' ")  ;
        $dbh->execute();
        $dbh = null ;
        $cnx = null ;
        return $rep;         
    }
    
	
     public function resetConfig(){
        $rep = $this->getResponse(); 
        
            if($this->isOver()){
                 $cnx = jDb::getConnection();
                $rs = $cnx->query( "
                    UPDATE csv_config
                    SET flagbash = 'N' 
                    WHERE  url <> '' 
                    AND source = 'netaff' 
                    AND actif = 1             
                    ")  ;
            } 
            
        jLog::log('DEBUG step 11 - reset config','default');            
            
        return $rep;
        
    }
    
    public function isOver(){
         $cnx = jDb::getConnection();
        $rs = $cnx->query( "
            SELECT * FROM csv_config
            
            WHERE  url <> '' 
            AND source = 'netaff' 
            AND actif = 1 
            AND flagbash = 'N'  
            ")  ;       
        
        $res = $rs->fetchAll();
        
        if(count($res) === 0){
            return true ;            
        }else{
            return false;
        }
        
    }


    public function importCSV(){          
        $rep = $this->getResponse(); 
        
         $cnx = jDb::getConnection();
        $rs = $cnx->query( "SELECT * FROM csv_config 
            WHERE  url <> '' 
            AND source = 'netaff' 
            AND actif = 1 
            AND flagbash = 'N' 
            LIMIT 0,1 ")  ;

        foreach($rs as $r){
        $path = $this->paths . $r->rewrite .'/' . $r->rewrite . '.csv' ;          
      
        
        
        jLog::log($path ,'default');

       	
        	$fo = new SplFileObject($path);  
  
			# définition du caractère séparateur de colonnes 
			$delimiter = ';';
			$enclosure = "\"";			 
			$fo->setCsvControl($delimiter, $enclosure,"\\");  

			$options = SplFileObject::READ_CSV  +  SplFileObject::SKIP_EMPTY;
        	
        	$fo->setFlags($options);
			
			$i = 0;
			foreach( $fo as $line ){
				
		if($i > 0){
		$line[1] = str_replace('"','',$line[1]);
		$line[2] = str_replace('"','',$line[2]);
		$line[3] = str_replace('"','',$line[3]);
		$line[5] = str_replace('"','',$line[5]);
		$line[6] = str_replace('"','',$line[6]);
		$line[8] = str_replace('"','',$line[8]);		
		$line[10] = str_replace('"','',$line[10]);
		$line[11] = str_replace('"','',$line[11]);
		$line[12] = str_replace('"','',$line[12]);
		$line[13] = str_replace('"','',$line[13]);
		$line[14] = str_replace('"','',$line[14]);
		$line[15] = str_replace('"','',$line[15]);		
		$line[16] = str_replace('"','',$line[16]);		
		$line[19] = str_replace('"','',$line[19]);
		$line[20] = str_replace('"','',$line[20]);
		$line[26] = str_replace('"','',$line[26]);
		$line[27] = str_replace('"','',$line[27]);
		$line[28] = str_replace('"','',$line[28]);	

		if($line[28]!== null){
			$cacheimage = $this->resizeImage($line[28]);
		} elseif ($line[27] !== null) {
			$cacheimage = $this->resizeImage($line[27]);
		} elseif ($line[26] !== null) {
			$cacheimage = $this->resizeImage($line[26]);
		}
		
		
			$query = <<<EOD
		INSERT INTO produits (
			nom,
			reference_fabriquant,
			prix,
			promo,
			transport,
			monnaie,
			id_produit_affilie,
			categorie_marchand,
			url,
			short_description,
			marque,
			ean13,
			long_description ,
			delais,
			qte,
			petiteimage,
			mediumimage,
			longimage,
			cacheimage
		)		
		VALUES (
		"{$line[1]}",
		"{$line[2]}",
		"{$line[3]}",
		"{$line[5]}",
		"{$line[6]}",
		"{$line[8]}",		
		"{$line[10]}",
		"{$line[11]}",
		"{$line[12]}",
		"{$line[13]}",
		"{$line[14]}",
		"{$line[15]}",		
		"{$line[16]}",		
		"{$line[19]}",
		"{$line[20]}",
		"{$line[26]}",
		"{$line[27]}",
		"{$line[28]}",
		"{$cacheimage}"	
		);
EOD;
 
        $cnx->exec($query);
		}
		$i++;	        	
        }         

        $flag_query = "UPDATE csv_config SET flagbash = 'Y' WHERE rewrite = '" . $r->rewrite ."'"; 

        $cnx->query($flag_query);
        }
        
        $cnx = null ;
        
        
        jLog::log('DEBUG step 6 - IMPORT csv ' . $r->rewrite ,'default');
        
        return $rep;
    }   
    
    public function resizeImage($url){
    
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
    

    public function createStoreTree(){

        $rep = $this->getResponse(); 
        
        $cnx = jDb::getConnection();
        $rs = $cnx->query( "SELECT rewrite FROM csv_config 
				            WHERE  url <> '' 
				            AND source = 'netaff' 
				            AND actif = 1 
				            AND flagbash = 'N' 
				            LIMIT 0,1   ")  ;
        
          foreach( $rs as $record){
             jFile::createDir( $this->paths . $record->rewrite);
          }
          
          jLog::log('DEBUG step 4 - create tree ' . $record->rewrite, 'default');

         return $rep;        
    }
    
    
    public function nettoieStoreTree(){

        $rep = $this->getResponse(); 
        
        $cnx = jDb::getConnection();
        $rs = $cnx->query( "SELECT rewrite FROM csv_config WHERE source = 'netaff' ")  ;
        
          foreach( $rs as $record){
             jFile::removeDir(  $this->paths . $record->rewrite );
          }
          
        $dbh = null ;
        
        jLog::log('DEBUG step 3 - nettoyage tree','default');

        return $rep;        
    }
        
    public function getCSV(){

        $rep = $this->getResponse(); 
        
        $cnx = jDb::getConnection();
        $rs = $cnx->query( "SELECT * FROM csv_config WHERE  url <> '' 
           AND source = 'netaff' 
           AND actif = 1 
           AND flagbash = 'N' 
            LIMIT 0,1 ")  ;

        foreach($rs as $r){
        $path = $this->paths . $r->rewrite .'/' . $r->rewrite . '.csv' ;

            $ch = curl_init();
            $fp = fopen($path, "w");
            curl_setopt($ch, CURLOPT_URL, $r->url  );
            curl_setopt($ch, CURLOPT_HTTPGET, true );
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_FAILONERROR, true ); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);


            $page = curl_exec($ch);

            curl_close($ch); 
         }
         unset($rs);  
         chmod($path , 0777);
         jLog::log('DEBUG step 5 - get CSV ' . $r->rewrite ,'default');
         return $rep;        
    }
    
}