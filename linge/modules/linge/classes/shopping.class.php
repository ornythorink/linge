<?php
jClasses::inc('linge~restclient');

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
            "numItems" => "60",
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

