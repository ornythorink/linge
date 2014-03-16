<?php

class effilcsvCtrl extends jControllerCmdLine {
    
    
    public $paths =  "/home/ornythorink/linge/linge/var/uploads/effil/" ;
    
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
    

    public function nettoieProduits($boutique){
        $rep = $this->getResponse(); 
        
         $cnx = jDb::getConnection();
        
        $dbh = $cnx->prepare( "DELETE FROM produits  WHERE source = 'EFF'  AND  boutique =  '" .  $boutique ."'")  ;
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
                    AND source = 'effil' 
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
            AND source = 'effil' 
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
            AND source = 'effil' 
            AND actif = 1 
            AND flagbash = 'N' 
            LIMIT 0,1 ")  ;

        foreach($rs as $r){
        $path = $this->paths . $r->rewrite .'/' . $r->rewrite . '.csv' ;         
		$matching = (int) $r->matchingColumnCategory;
        jLog::log($path ,'default');
        $this->nettoieProduits($r->rewrite);
        
        	$fo = new SplFileObject($path);  
  
			# définition du caractère séparateur de colonnes 
			$delimiter = '|';
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
			prix,
			url,
			marque,
			long_description ,
			petiteimage,
			mediumimage,
			longimage,
			qte,
			categorie_marchand,
			boutique,
	    	source
		)		
		VALUES (
		"{$line[1]}",
		"{$line[6]}",
		"{$line[2]}",
		"{$line[5]}",
		"{$line[4]}",
		"{$line[3]}",		
		"{$line[3]}",
		"{$line[3]}",
		"{$line[7]}",
		"{$line[$matching]}",	
		"{$r->rewrite}",
		'EFF'		
		) 

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
    
  
    public function createStoreTree(){

        $rep = $this->getResponse(); 
        
        $cnx = jDb::getConnection();
        $rs = $cnx->query( "SELECT rewrite FROM csv_config 
				            WHERE  url <> '' 
				            AND source = 'effil' 
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
        $rs = $cnx->query( "SELECT rewrite FROM csv_config WHERE source = 'effil' ")  ;
        
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
           AND source = 'effil' 
           AND actif = 1 
           AND flagbash = 'N' 
            LIMIT 0,1 ");

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