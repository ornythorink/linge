<?php

class zanoxcsvCtrl extends jControllerCmdLine {
    
    
    public $paths =  "/home/ornythorink/linge/linge/var/uploads/zanox/" ;
    
    public function run(){
         $rep = $this->getResponse(); 
       
         jLog::log('start to process' ,'default');   
         
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

   
    public function nettoieProduits($boutique)
    {
    	$rep = $this->getResponse();
    	
    	$cnx = jDb::getConnection();
    	
    	$dbh = $cnx->prepare( "DELETE FROM produits WHERE  source = 'ZNX' AND  '" .  $boutique ."'")  ;
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
                    AND source = 'zanox' 
                    AND actif = 1             
                    ")  ;
            } 
            
        return $rep;
        
    }
    
    public function isOver(){
          $cnx = jDb::getConnection();
        $rs = $cnx->query( "
            SELECT * FROM csv_config
            
            WHERE  url <> '' 
            AND source = 'zanox' 
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
            AND source = 'zanox' 
            AND actif = 1 
            AND flagbash = 'N' 
            LIMIT 0,1 ")  ;

        foreach($rs as $r){
        $path = $this->paths . $r->rewrite .'/' . $r->rewrite . '.csv' ;   
        
        $this->nettoieProduits($r->rewrite);      
        
        $fo = new SplFileObject($path);
        
        # définition du caractère séparateur de colonnes
        $delimiter = ',';
        $enclosure = "\"";
        $fo->setCsvControl($delimiter, $enclosure,"\\");
        
        $options = SplFileObject::READ_CSV  +  SplFileObject::SKIP_EMPTY;
         
        $fo->setFlags($options);
        	
        $i = 0;
        foreach( $fo as $line ){
        
        if($i > 0){
    
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
			boutique,
	    	source
		)
		VALUES (
		"{$line[3]}", 
		"{$line[2]}",  
		"{$line[4]}",  
		"{$line[5]}",  	
		"{$line[25]}", 
		"{$line[6]}", 
		"{$line[0]}",  
		"{$line[12]}",  
		"{$line[18]}", 
		"{$line[10]}", 
		"{$line[17]}", 
		    null     ,  
		"{$line[11]}", 
		"{$line[24]}",  
	        null ,  
		"{$line[14]}", 
		"{$line[15]}", 
		"{$line[16]}", 
		"{$r->rewrite}",
		'ZNX'
		)
        
EOD;
        var_dump($query);
        $cnx->query($query);
        }
        $i++;
        }
       
        $flag_query = "UPDATE csv_config SET flagbash = 'Y' WHERE rewrite = '" . $r->rewrite ."'"; 

        $cnx->query($flag_query);
        }        
        $cnx = null ;
        return $rep;
    }   

    public function createStoreTree(){

        $rep = $this->getResponse(); 
        
         $cnx = jDb::getConnection();
        $rs = $cnx->query( "SELECT rewrite FROM csv_config WHERE source = 'zanox'  ")  ;
        
          foreach( $rs as $record){
             jFile::createDir( $this->paths . $record->rewrite);
          }
          

         return $rep;        
    }
    
    
    public function nettoieStoreTree(){

        $rep = $this->getResponse(); 
        
         $cnx = jDb::getConnection();
        $rs = $cnx->query( "SELECT rewrite FROM csv_config WHERE source = 'zanox' ")  ;
        
          foreach( $rs as $record){
             jFile::removeDir(  $this->paths . $record->rewrite );
          }
          
        $dbh = null ;

         return $rep;        
    }
    

    public function getCSV(){

        $rep = $this->getResponse(); 
        
         $cnx = jDb::getConnection();
        $rs = $cnx->query( "SELECT * FROM csv_config WHERE  url <> '' 
           AND source = 'zanox' 
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
         return $rep;        
    }
    
}