<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Leisure Magento Theme</title>
<!--CSS-->
<link rel="stylesheet" href="{$domaine}/css/styles.css">
<!--Google Webfont -->
<link href='http://fonts.googleapis.com/css?family=Istok+Web' rel='stylesheet' type='text/css'>
<!--Javascript-->
<script type="text/javascript" src="{$domaine}/js/jquery-1.7.2.min.js" ></script>
<script type="text/javascript" src="{$domaine}/js/jquery.flexslider.js" ></script>
<script type="text/javascript" src="{$domaine}/js/jquery.easing.js"></script>
<script type="text/javascript" src="{$domaine}/js/jquery.jcarousel.js"></script>
<script type="text/javascript" src="{$domaine}/js/jquery.jtweetsanywhere-1.3.1.min.js" ></script>
<script type="text/javascript" src="{$domaine}/js/simpletabs_1.3.js"></script>
<script type="text/javascript" src="{$domaine}/js/form_elements.js" ></script>
<script type="text/javascript" src="{$domaine}/js/custom.js"></script>
<script type="text/javascript" src="http://img.metaffiliation.com/na/na/res/trk/script.js" ></script>    
<!--[if lt IE 9]>
    <script src="js/html5.js"></script>
<![endif]-->
<!-- mobile setting -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<body>
	
<div class="wrapper">
    <div class="header_container">
        <!--Header Starts-->
        <header>
            <div class="top_bar clear">
                   <!--Top Links Ends-->
            </div>
            <!--Logo Starts-->
            <h1 class="logo"> <a href="{$domaine}" /><img src="{$domaine}/images/logo2.jpg" /></a> </h1>
            <!--Logo Ends-->
            <!--Responsive NAV-->            
            <!--Responsive NAV-->
            <!--Search Starts-->
  	        <form class="header_search" action="{jurl 'linge~search:index'}" >
                <div class="form-search">
                    <input id="search" type="text" name="q" value="" class=""  placeholder="Search">
                    <input type="hidden" name="lang" value="{$j_locale}" >
                    <button type="submit" title="Search"></button>
                </div>
            </form>
            <!--Search Ends-->
        </header>
        <!--Header Ends-->
    </div>
    <div class="navigation_container">
        <!--Navigation Starts-->
        <nav>
            <ul class="primary_nav">
                 {foreach $categoriesParent as $parent}
 						<li class="active"><a href="">{$parent->name_categorie}</a>
                           <ul class="sub_menu">
                           	   {foreach $categoriesChild as $child}
	                           	   {if $child->id_parent == $parent->id_categorie}
		                           <li> <a href="{jurl 'linge~category:index' , array('q' => $child->name_categorie, 'id' => $child->id_categorie,'lang'=>$j_locale)  }">{$child->name_categorie}</a>
			                           <ul>
			                           {foreach $categoriesSub as $sub}
	                           	   	   		{if $child->id_categorie == $sub->id_parent}			                           	 	
			                                	<li><a href="{jurl 'linge~category:index' , array('q' => $sub->name_categorie, 'id' => $sub->id_categorie,'lang'=>$j_locale) }">{$sub->name_categorie}</a></li>
		                                	 {/if}
									   {/foreach}                                
			                           </ul>
		                           </li>
		                           {/if}
	                           {/foreach} 
                           </ul>
                        </li> 							
                 {/foreach}	 
            </ul>

        </nav>
        <!--Navigation Ends-->
    </div>
    <div class="section_container">
        <!--Mid Section Starts-->
        <section>
            <!--Banner Starts-->
            <div id="banner_section" >
                <div class="flexslider">
                    <ul class="slides">
                        <li><img src="images/photo1.jpg" />
                            <div class="flex-caption">
                     <h3>Chercher sans se fatiguer...</h3>
              </div>
                        </li>
                        <li> <img src="images/photo2.jpg" />
                        <div class="flex-caption">
                      <h3>Parmi un large choix...</h3>
               </div> 
                        </li>
                        <li><img src="images/photo3.jpg" />
                        <div class="flex-caption">
                      <h3>...Sexy mais classe</h3>
               </div>                         	
                    </ul>
                </div>
                <div class="promo_banner">
                    <div class="home_banner">
<!-- DEBUT du code HTML zanox-affiliate -->
<!-- (Le code HTML zanox-affiliate ne peut pas être modifié pour préserver une fonctionnalité parfaite !)-->
<a href="http://ad.zanox.com/ppc/?27235568C1716946532T"><img src="http://ad.zanox.com/ppv/?27235568C1716946532" align="bottom" width="180" height="150" border="0" hspace="1" alt="Banniere_180x150_2"></a>
<!-- FIN du code HTML zanox-affiliate -->                    	
                    </div>
                    <div class="home_banner"><!-- DEBUT du code HTML zanox-affiliate -->
<!-- (Le code HTML zanox-affiliate ne peut pas être modifié pour préserver une fonctionnalité parfaite !)-->
<a href="http://ad.zanox.com/ppc/?27235571C1868822034T"><img src="http://ad.zanox.com/ppv/?27235571C1868822034" align="bottom" width="180" height="150" border="0" hspace="1" alt="180x150.gif"></a>
<!-- FIN du code HTML zanox-affiliate --></div>
                    <div class="home_banner">
<!-- DEBUT du code HTML zanox-affiliate -->
<!-- (Le code HTML zanox-affiliate ne peut pas être modifié pour préserver une fonctionnalité parfaite !)-->
<a href="http://ad.zanox.com/ppc/?27235590C1963128781T"><img src="http://ad.zanox.com/ppv/?27235590C1963128781" align="bottom" width="180" height="150" border="0" hspace="1" alt="BE-FR_DEC 180x150"></a>
<!-- FIN du code HTML zanox-affiliate -->                 	
                    </div>
                </div>
            </div>
            <!--Banner Ends-->
            <!--Product List Starts-->

 
            
            <div class="products_list products_slider">
                <h2 class="sub_title">DERNIERS ARRIVAGES</h2>
                <ul id="first-carousel" class="first-and-second-carousel jcarousel-skin-tango">
                    {foreach $hoffres as $h}
                         {if  $h->longimage != null}
                         <li>
                             <div>
                             <a class="product_image" href="{$h->url}"  ><img src="{retaille $h->longimage}"  /></a>
                                </div>
                                <div class="product_info">
                                    <h3><a href="{$h->url}">{$h->nom}</a></h3>
                                    <small></small> </div>
                                <div class="price_info">
                                    <button class="price_add" title="" type="button"><span class="pr_price">{$h->prix}€</span><span class="pr_add">Voir le site</span></button>
                                </div>
                            </li>
                        {elseif  $h->mediumimage != null}
                             <li>
                                 <div>
                                 <a class="product_image"><img src="{retaille $h->mediumimage}"   /></a>
             					</div>
                                <div class="product_info">
                                    <h3><a href="{$h->url}">{$h->nom}</a></h3>
                                    <small></small> </div>
                                <div class="price_info">
                                    <button class="price_add" title="" type="button"><span class="pr_price">{$h->prix}€</span><span class="pr_add">Voir le site</span></button>
                                </div>
                            </li>
                        {elseif $h->petiteimage != null}
                             <li> 
                                 <div>
                                 <a class="product_image"><img src="{retaille $h->petiteimage}"   /></a>
                        		</div>
                                <div class="product_info">
                                    <h3><a href="{$h->url}">{$h->nom}</a></h3>
                                    <small></small> </div>
                                <div class="price_info">
                                    <button class="price_add" title="" type="button"><span class="pr_price">{$h->prix}€</span><span class="pr_add">Voir le site</span></button>
                                </div>
                            </li>                      
                        {/if}
                     {/foreach}                   
                </ul>
            </div>
            <!--Product List Ends-->
            <!--Product List Ends-->
            <!--Newsletter_subscribe Starts-->
            <!--Newsletter_subscribe Ends-->
        </section>
        <!--Mid Section Ends-->
    </div>
    <div class="footer_container">
        <!--Footer Starts-->
        <footer>
            <address>
            Copyright © 2014 Grégory Ornythorink LANCESTREMERE. All Rights Reserved.
            </address>
        </footer>
        <!--Footer Ends-->
    </div>
</div>
</body>
</html>

