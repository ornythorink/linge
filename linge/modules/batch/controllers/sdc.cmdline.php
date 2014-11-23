<?php

class sdcCtrl extends jControllerCmdLine {
    

    public function run(){
         $rep = $this->getResponse(); 
         
         jLog::log('start to process', 'default');


         $this->importCSV();
         
         return $rep;
    }

    public function importCSV(){

        $cnx = jDb::getConnection('etl');


        $rep = $this->getResponse(); 
        

        $nettoyage = $cnx->query( <<<TAG
DELETE FROM produits WHERE source = 'SDC'
TAG
);
        $cnx->exec($nettoyage);


        $categories = $cnx->query( <<<TAG
SELECT tag FROM categories
TAG
);

        foreach($categories as  $keycat => $cat){
            $cat = str_replace('-',' ', $cat->tag);

            $xmlstring = Shopping::getProductByKeyword($cat,'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:33.0) Gecko/20100101 Firefox/33.0','88.179.203.15');

            $sxe = simplexml_load_string($xmlstring, 'SimpleXMLIterator');
            $cnx2 = jDb::getConnection('etl');
            if($sxe !== false){
                foreach(new RecursiveIteratorIterator($sxe, 1) as $name => $data) {
                    if($data->items->offer !== null){
                        foreach($data->items->offer as $d){

                            
                            $nom =(string) $d->name;
                            $url =(string) $d->offerURL;
                            $prix =(string) $d->basePrice;
                            $promo =(string) $d->originalPrice;
                            $transport =(string) $d->shippingCost;

                            if(isset($d->description)){
                                $description =(string) $d->description;
                            } else {
                                $description = '';
                            }

                            if(isset($d->storeNotes)) {
                                $delais  =(string)$d->storeNotes;
                            } else {
                                $delais = '';
                            }

                            if((string) $d->imageList->image[4]['available'] == true)
                            {
                                $image = (string) $d->imageList->image[4]->sourceURL;
                            }
                            elseif((string) $d->imageList->image[31]['available'] == true)
                            {
                                $image = (string) $d->imageList->image[3]->sourceURL;
                            }
                            elseif((string) $d->imageList->image[2]['available'] == true)
                            {
                                $image = (string) $d->imageList->image[2]->sourceURL;
                            }
                            elseif((string) $d->imageList->image[1]['available'] == true)
                            {
                                $image = (string) $d->imageList->image[1]->sourceURL;
                            }
                            elseif((string) $d->imageList->image[0]['available'] == true)
                            {
                                $image = (string) $d->imageList->image[0]->sourceURL;
                            }
                            $boutique =(string)$d->store->name;

                            $cnx = null ;



                            $query2 = <<<EOD
INSERT INTO
    produits (
            nom,
            prix,
            promo,
            transport,
            categorie_marchand,
            url,
            short_description,
            long_description ,
            delais,
            petiteimage,
            mediumimage,
            longimage,
            boutique,
            source
            )



            VALUES (
            "{$nom}",
            "{$prix}",
            "{$promo}",
            "{$transport}",
            "{$cat}",
            "{$url}",
            "{$description}",
            "{$description}",
            "{$delais}",
            "{$image}",
            "{$image}",
            "{$image}",
            "{$boutique}",
            'SDC'
            )
EOD;


                            $cnx2->exec($query2);
                        }
                    }

                }

            }

        }

        $validation = "UPDATE `produits` SET  `status` = 'Ok' WHERE source = 'SDC'";
        $cnx2->exec($validation);
        
        return $rep;

    }  

    
}

class Shopping
{
    private static $_uri     = null;


    private static $_host;


    private static $_request = '/publisher/3.0/rest/GeneralSearch';
    private static $_param   = null;

    private static $_apiKey  = "0558e60f-c9fe-4939-a960-a7172cc67783";
    private static $_trackingId  = "8084776";


    public static $liste;


    public static function getHost(){

        if(isset($_SERVER["HTTP_HOST"])){
            if(strstr($_SERVER["HTTP_HOST"] , "localhost" )  ){
                self::$_host  = 'http://sandbox.api.ebaycommercenetwork.com';
            }else{
                self::$_host  = 'http://api.ebaycommercenetwork.com';

            }
        }else{
            self::$_host  = 'http://api.ebaycommercenetwork.com';
        }
        return self::$_host ;
    }



    public static function getByKeyword( $keyword , $ua , $ip )
    {

        $params = array(
            "apiKey"=> self::$_apiKey ,
            "trackingId"=> self::$_trackingId,
            "keyword"=> $keyword,
            "categoryId"=>"96667",
            "numItems" => "1",
            "showProductOffers" => "true",
            "numOffersPerProduct"=>"20",
            "showProductSpecs"=>"true",
            "visitorUserAgent"=> $ua,
            "visitorIPAddress"=> $ip,
            "showProductsWithoutOffers"=>"false"

        ) ;

        $shopping = RestClient::get(self::getHost() . self::$_request, $params );
        return $shopping->getResponse();
    }

    public static function getMarque()
    {

        $params = array(
            "apiKey"=> self::$_apiKey ,
            "trackingId"=> self::$_trackingId,
            "categoryId"=>"96602",
            "showAllValuesForAttr"=>"9688_brand"
        ) ;

        $shopping = RestClient::get(self::getHost() . self::$_request, $params);
        return $shopping->getResponse();
    }

    public static function getOffersByMarques($brand)
    {

        $params = array(
            "apiKey"=> self::$_apiKey ,
            "trackingId"=> self::$_trackingId,
            "categoryId"=>"96602",
            "attributeValue"=> $brand,
            "showProductOffers" => "true",
            "numOffersPerProduct"=>"15",
            "numItems" => "200"
        ) ;

        return RestClient::get(self::getHost() . self::$_request, $params)->getResponse();
    }



    public static function getCategories()
    {

        $params = array(
            "apiKey"=> self::$_apiKey ,
            "trackingId"=> self::$_trackingId,
            "categoryId"=>"96602",
            "showAllValuesForAttr"=>"9688_brand"
        ) ;

        $shopping = RestClient::get(self::getHost() . self::$_request, $params);
        return $shopping->getResponse();
    }


    public static function getFeatured()
    {

        $params = array(
            "apiKey"=> self::$_apiKey ,
            "trackingId"=> self::$_trackingId,
            "categoryId"=>"96602",
            "offerSortType"=>"featured-store",
            "numFeatured"=>"4",
        ) ;
        $shopping = RestClient::get(self::getHost() . self::$_request, $params);
        return $shopping->getResponse();
    }

    public static function getProductByKeyword( $keyword , $ua , $ip )
    {

        $params = array(
            "apiKey"=> self::$_apiKey ,
            "trackingId"=> self::$_trackingId,
            "keyword"=> $keyword,
            "categoryId"=>"96667",
            "numItems" => "200",
            "showProductOffers" => "false",
            "numOffersPerProduct"=>"20",
            "showProductSpecs"=>"true",
            "visitorUserAgent"=> $ua,
            "visitorIPAddress"=> $ip,
            "showProductsWithoutOffers"=>"false"

        ) ;

        $shopping = RestClient::get(self::getHost() . self::$_request, $params );
        return $shopping->getResponse();
    }

    public function getProductHome(  $keyword , $ua , $ip )
    {
        $params = array(
            "apiKey"=> self::$_apiKey ,
            "trackingId"=> self::$_trackingId,
            "keyword"=> $keyword,
            "categoryId"=>"96667",
            "numItems" => "6",
            "showProductOffers" => "false",
            "numOffersPerProduct"=>"20",
            "showProductSpecs"=>"true",
            "visitorUserAgent"=> $ua,
            "visitorIPAddress"=> $ip,
            "showProductsWithoutOffers"=>"false"

        ) ;

        $shopping = RestClient::get(self::getHost() . self::$_request, $params );
        return $shopping->getResponse();

    }


    public static function getProductById( $id )
    {

        $params = array(
            "apiKey"=> self::$_apiKey ,
            "trackingId"=> self::$_trackingId,
            "categoryId"=>"96602",
            "productId"=>$id ,
            "numItems" => "60",
            "showProductOffers" => "true",
            "numOffersPerProduct"=>"20",
            "showProductSpecs"=>"true",
            "showProductsWithoutOffers"=>"false"

        ) ;

        $shopping = RestClient::get(self::getHost() . self::$_request, $params );
        return $shopping->getResponse();
    }


    public static function getCategorieByAttribute( $keyword , $ua , $ip , $attribute  )
    {
        foreach( $attribute as $k=>$v ){
            $value[$k] = $v;
        }


        $params = array(
            "apiKey"=> self::$_apiKey ,
            "trackingId"=> self::$_trackingId,
            "keyword"=> $keyword ,
            "categoryId"=>"96602",
            "showProductOffers" => "true",
            "numOffersPerProduct"=>"20",
            "visitorUserAgent"=> $ua,
            "visitorIPAddress"=> $ip,
            "numItems" => "60"
        ) ;


        $comp = "";

        foreach( $value as $clef => $att){
            if($clef != "keyword" ){
                $comp .= "&attributeValue=".$att ;
            }
        }

        $pwd =  null;
        $pass = null;
        $shopping = RestClient::get(self::getHost() . self::$_request, $params, $pwd,$pass, $comp );

        //echo($shopping->getResponse());

        return $shopping->getResponse();
    }


    public static function getCategorieBalise( $keyword , $ua , $ip , $attribute )
    {
        $params = array(
            "apiKey"=> self::$_apiKey ,
            "trackingId"=> self::$_trackingId,

            "categoryId"=>"96602",
            "attributeValue" => $attribute,
            "showProductOffers" => "true",
            "visitorUserAgent"=> $ua,
            "visitorIPAddress"=> $ip
        ) ;

        $pwd =  null;
        $pass = null;
        $shopping = RestClient::get(self::getHost() . self::$_request, $params );

        return $shopping->getResponse();
    }





    public static function Liste(){
        return self::$liste;
    }
}


class RestClient {

    private $curl ;
    private $url ;
    private $response ="";
    private $headers = array();

    private $method="GET";
    private $params=null;
    private $attribute = null;
    private $contentType = null;
    private $file =null;

    /**
     * Private Constructor, sets default options
     */
    private function __construct() {
        $this->curl = curl_init();
        curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($this->curl,CURLOPT_AUTOREFERER,true); // This make sure will follow redirects
        curl_setopt($this->curl,CURLOPT_FOLLOWLOCATION,true); // This too
        curl_setopt($this->curl,CURLOPT_HEADER,true); // THis verbose option for extracting the headers
    }

    /**
     * Execute the call to the webservice
     * @return RestClient
     */
    public function execute() {
        if($this->method === "POST") {
            curl_setopt($this->curl,CURLOPT_POST,true);
            curl_setopt($this->curl,CURLOPT_POSTFIELDS,$this->params);
        } else if($this->method == "GET"){
            curl_setopt($this->curl,CURLOPT_HTTPGET,true);
            $this->treatURL();
        } else if($this->method === "PUT") {
            curl_setopt($this->curl,CURLOPT_PUT,true);
            $this->treatURL();
            $this->file = tmpFile();
            fwrite($this->file,$this->params);
            fseek($this->file,0);
            curl_setopt($this->curl,CURLOPT_INFILE,$this->file);
            curl_setopt($this->curl,CURLOPT_INFILESIZE,strlen($this->params));
        } else {
            curl_setopt($this->curl,CURLOPT_CUSTOMREQUEST,$this->method);
        }
        if($this->contentType != null) {
            curl_setopt($this->curl,CURLOPT_HTTPHEADER,array("Content-Type: ".$this->contentType));
        }
        curl_setopt($this->curl,CURLOPT_URL,$this->url);
        $r = curl_exec($this->curl);
        $this->treatResponse($r); // Extract the headers and response
        return $this ;
    }

    /**
     * Treats URL
     */
    private function treatURL(){
        if(is_array($this->params) && count($this->params) >= 1) { // Transform parameters in key/value pars in URL
            if(!strpos($this->url,'?'))
                $this->url .= '?' ;

            $firstparam = false;

            foreach($this->params as $k=>$v) {
                if($firstparam === false){
                    $this->url .= urlencode($k)."=".urlencode($v);
                    $firstparam = true;
                }else{
                    $this->url .= "&".urlencode($k)."=".urlencode($v);
                }
            }

            if($this->attribute !== null){
                $this->url .= $this->attribute ;
            }

        }
        //debug
        //echo $this->url ."<br/>";
        return $this->url;
    }

    /*
     * Treats the Response for extracting the Headers and Response
     */
    private function treatResponse($r) {
        if($r == null or strlen($r) < 1) {
            return;
        }
        $parts  = explode("\n\r",$r); // HTTP packets define that Headers end in a blank line (\n\r) where starts the body
        while(preg_match('@HTTP/1.[0-1] 100 Continue@',$parts[0]) or preg_match("@Moved@",$parts[0])) {
            // Continue header must be bypass
            for($i=1;$i<count($parts);$i++) {
                $parts[$i - 1] = trim($parts[$i]);
            }
            unset($parts[count($parts) - 1]);
        }
        preg_match("@Content-Type: ([a-zA-Z0-9-]+/?[a-zA-Z0-9-]*)@",$parts[0],$reg);// This extract the content type
        $this->headers['content-type'] = $reg[1];
        preg_match("@HTTP/1.[0-1] ([0-9]{3}) ([a-zA-Z ]+)@",$parts[0],$reg); // This extracts the response header Code and Message
        $this->headers['code'] = $reg[1];
        $this->headers['message'] = $reg[2];
        $this->response = "";
        for($i=1;$i<count($parts);$i++) {//This make sure that exploded response get back togheter
            if($i > 1) {
                $this->response .= "\n\r";
            }
            $this->response .= $parts[$i];
        }
    }

    /*
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /*
     * @return string
     */
    public function getResponse() {
        return $this->response ;
    }

    /*
     * HTTP response code (404,401,200,etc)
     * @return int
     */
    public function getResponseCode() {
        return (int) $this->headers['code'];
    }

    /*
     * HTTP response message (Not Found, Continue, etc )
     * @return string
     */
    public function getResponseMessage() {
        return $this->headers['message'];
    }

    /*
     * Content-Type (text/plain, application/xml, etc)
     * @return string
     */
    public function getResponseContentType() {
        return $this->headers['content-type'];
    }

    /**
     * This sets that will not follow redirects
     * @return RestClient
     */
    public function setNoFollow() {
        curl_setopt($this->curl,CURLOPT_AUTOREFERER,false);
        curl_setopt($this->curl,CURLOPT_FOLLOWLOCATION,false);
        return $this;
    }

    /**
     * This closes the connection and release resources
     * @return RestClient
     */
    public function close() {
        curl_close($this->curl);
        $this->curl = null ;
        if($this->file !=null) {
            fclose($this->file);
        }
        return $this ;
    }

    /**
     * Sets the URL to be Called
     * @return RestClient
     */
    public function setUrl($url) {

        $this->url = $url;
        return $this;
    }

    /**
     * Set the Content-Type of the request to be send
     * Format like "application/xml" or "text/plain" or other
     * @param string $contentType
     * @return RestClient
     */
    public function setContentType($contentType) {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Set the Credentials for BASIC Authentication
     * @param string $user
     * @param string $pass
     * @return RestClient
     */
    public function setCredentials($user,$pass) {
        if($user != null) {
            curl_setopt($this->curl,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
            curl_setopt($this->curl,CURLOPT_USERPWD,"{$user}:{$pass}");
        }
        return $this;
    }

    /**
     * Set the Request HTTP Method
     * For now, only accepts GET and POST
     * @param string $method
     * @return RestClient
     */
    public function setMethod($method) {
        $this->method=$method;
        return $this;
    }

    public function setAttribute($attribute) {
        $this->attribute=$attribute;
        return $this;
    }

    /**
     * Set Parameters to be send on the request
     * It can be both a key/value par array (as in array("key"=>"value"))
     * or a string containing the body of the request, like a XML, JSON or other
     * Proper content-type should be set for the body if not a array
     * @param mixed $params
     * @return RestClient
     */
    public function setParameters($params) {
        $this->params=$params;
        return $this;
    }

    /**
     * Creates the RESTClient
     * @param string $url=null [optional]
     * @return RestClient
     */
    public static function createClient($url=null) {
        $client = new RestClient ;
        if($url != null) {

            $client->setUrl($url);
        }
        return $client;
    }

    /**
     * Convenience method wrapping a commom POST call
     * @param string $url
     * @param mixed params
     * @param string $user=null [optional]
     * @param string $password=null [optional]
     * @param string $contentType="multpary/form-data" [optional] commom post (multipart/form-data) as default
     * @return RestClient
     */
    public static function post($url,$params=null,$user=null,$pwd=null,$contentType="multipart/form-data") {

        return self::call("POST",$url,$params,$user,$pwd,$contentType);
    }

    /**
     * Convenience method wrapping a commom PUT call
     * @param string $url
     * @param string $body
     * @param string $user=null [optional]
     * @param string $password=null [optional]
     * @param string $contentType=null [optional]
     * @return RestClient
     */
    public static function put($url,$body,$user=null,$pwd=null,$contentType=null) {
        return self::call("PUT",$url,$body,$user,$pwd,$contentType);
    }

    /**
     * Convenience method wrapping a commom GET call
     * @param string $url
     * @param array params
     * @param string $user=null [optional]
     * @param string $password=null [optional]
     * @return RestClient
     */
    public static function get($url,array $params=null,$user=null,$pwd=null,  $contentType = null , $attribute=null) {
        return self::call("GET",$url,$params,$user,$pwd,$contentType, $attribute);
    }

    /**
     * Convenience method wrapping a commom delete call
     * @param string $url
     * @param array params
     * @param string $user=null [optional]
     * @param string $password=null [optional]
     * @return RestClient
     */
    public static function delete($url,array $params=null,$user=null,$pwd=null) {
        return self::call("DELETE",$url,$params,$user,$pwd);
    }

    /**
     * Convenience method wrapping a commom custom call
     * @param string $method
     * @param string $url
     * @param string $body
     * @param string $user=null [optional]
     * @param string $password=null [optional]
     * @param string $contentType=null [optional]
     * @return RestClient
     */
    public static function call($method,$url,$body,$user=null,$pwd=null,$contentType=null,$attribute=null) {
        return self::createClient($url)
            ->setParameters($body)
            ->setAttribute($attribute)
            ->setMethod($method)
            ->setCredentials($user,$pwd)
            ->setContentType($contentType)
            ->execute()
            ->close();
    }
}

?>