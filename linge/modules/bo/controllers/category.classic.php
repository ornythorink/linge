<?php 


class categoryCtrl extends jController {
    /**
    *
    */
    function index() {
    	$rep = $this->getResponse('html');
    	
    	return $rep;
    }
    
    public function updateCategories(){
    	$rep = $this->getResponse('htmlfragment');
    	$id = $this->param('id');


    	$factory = jDao::get('bo~produits');
    	$record = $factory->get( $id);

    	$cnx  = jDb::getConnection();
    	 
    	$query = "
			UPDATE produits SET status = 'Ok' WHERE categorie_marchand = '". $record->categorie_marchand . "'" ;
    	$res  = $cnx->exec($query);    	
    	
    	$factoryCategory = jDao::get('bo~whiteliste');
    	$category = jDao::createRecord("bo~whiteliste");
    	$category->category = $record->categorie_marchand;
    	$category->source = $record->source;
    	$category->boutique = $record->boutique;
    	
    	$factoryCategory->insert($category);
    	
    	return $rep;
    }    
    
    function zanoxWhiteListe() {
    	$rep = $this->getResponse('html');
    
    	$cnx  = jDb::getConnection();
    
    	 
    	$query = "
    	SELECT id_produit, boutique , source  , categorie_marchand
    	FROM produits WHERE source = 'ZNX'
    	AND status = 'Validation'
    	group by categorie_marchand
    	";
    	$res  = $cnx->query($query);
    	$liste = $res->fetchAll();
    
    	$tpl = new jTpl();
    	 
    	$tpl->assign('liste', $liste);
    
    	$rep->body->assign('MAIN', $tpl->fetch("bo~crud_list"));
    
    	return $rep;
    } 

    function netaffWhiteListe() {
    	$rep = $this->getResponse('html');
    	
    	$cnx  = jDb::getConnection();
    	
    	
    	$query = "
    	SELECT id_produit, boutique , source ,categorie_marchand
    	FROM produits WHERE source = 'NET'
    	AND status = 'Validation'
    	group by categorie_marchand
    	";
    	$res  = $cnx->query($query);
    	$liste = $res->fetchAll();
    	
    	$tpl = new jTpl();
    	
    	$tpl->assign('liste', $liste);
    	
    	$rep->body->assign('MAIN', $tpl->fetch("bo~crud_list"));
    	
    	return $rep;    	
    }
    
    function effilWhiteListe() {
    	$rep = $this->getResponse('html');
    	 
    	$cnx  = jDb::getConnection();
    	 
    	 
    	$query = "
    	SELECT id_produit, boutique , source  , categorie_marchand
    	FROM produits WHERE source = 'EFF'
    	AND status = 'Validation'
    	group by categorie_marchand
    	";
    	$res  = $cnx->query($query);
    	$liste = $res->fetchAll();
    	 
    	$tpl = new jTpl();
    	 
    	$tpl->assign('liste', $liste);
    	 
    	$rep->body->assign('MAIN', $tpl->fetch("bo~crud_list"));
    	 
    	return $rep;
    }    
    
}