<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Leisure Magento Theme</title>
<!--CSS-->
<link rel="stylesheet" href="{$domaine}css/styles.css">
<!--Google Webfont -->
<link href='http://fonts.googleapis.com/css?family=Istok+Web' rel='stylesheet' type='text/css'>
<!--Javascript-->
<script type="text/javascript" src="{$domaine}js/jquery-1.7.2.min.js" ></script>
<script type="text/javascript" src="{$domaine}js/jquery.flexslider.js" ></script>
<script type="text/javascript" src="{$domaine}js/jquery.easing.js"></script>
<script type="text/javascript" src="{$domaine}js/jquery.jcarousel.js"></script>
<script type="text/javascript" src="{$domaine}js/jquery.jtweetsanywhere-1.3.1.min.js" ></script>
<script type="text/javascript" src="{$domaine}js/simpletabs_1.3.js"></script>
<script type="text/javascript" src="{$domaine}js/form_elements.js" ></script>
<script type="text/javascript" src="{$domaine}js/custom.js"></script>
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
                <!--Language Switcher Starts-->
                <!--Top Links Ends-->
            </div>
            <!--Logo Starts-->
            <h1 class="logo"> <a href="{$domaine}"><img src="{$domaine}images/logo2.jpg" /></a> </h1>
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
            <!--SIDE NAV STARTS-->
            <div id="side_nav">
                <div class="sideNavCategories">                    

                </div>
            </div>
            <!--SIDE NAV ENDS-->
            <!--MAIN CONTENT STARTS-->
            <div id="main_content">
                <div class="category_banner"></div>
                <ul class="breadcrumb">

                </ul>
                <!--Toolbar-->
                <!--Toolbar-->
                <!--Product List Starts-->
                <div class="products_list_list">
                    <ul>
                   {foreach $produits as $h}
                         {if  $h->longimage != null}
                         <li>
                             <div>
                             <a class="product_image" href="{$h->url}"  ><img src="{$h->longimage}"  /></a>
                                </div>
                                <div class="product_info">
                                    <h3>{$h->nom}</h3>
                                    <small>{$h->long_description}</small> </div>
                                <div class="price_info">
                                    <button class="price_add" title="" type="button"><span class="pr_price">{$h->prix}€</span></button>
									<div class="boutique" ><a href="{$h->url}" >Voir sur le  site {$h->store}</a></div>
                                </div>
                            </li>
                        {elseif  $h->mediumimage != null}
                             <li>
                                 <div>
                                 <a class="product_image" href="{$h->url}"  ><img src="{$h->mediumimage}"   /></a>
             					</div>
                                <div class="product_info">
                                    <h3>{$h->nom}</h3>
                                    <small>{$h->long_description}</small> </div>
                                <div class="price_info">
                                    <button class="price_add" title="" type="button"><span class="pr_price">{$h->prix}€</span></button>
									<div class="boutique" ><a href="{$h->url}" >Voir sur le  site {$h->store}</a></div>
                                </div>
                            </li>
                        {elseif $h->petiteimage != null}
                             <li> 
                                 <div>
                                 <a class="product_image"  href="{$h->url}"  ><img src="{$h->petiteimage}"   /></a>
                        		</div>
                                <div class="product_info">
                                    <h3>{$h->nom}</h3>
                                    <small>{$h->long_description}</small> </div>
                                <div class="price_info">
                                    <button class="price_add" title="" type="button"><span class="pr_price">{$h->prix}€</span></button>
									<div class="boutique" ><a href="{$h->url}" >Voir sur le  site {$h->store}</a></div>
                                </div>
                            </li>                      
                        {/if}
                     {/foreach} 
                    </ul>
                </div>
                <!--Product List Ends-->
                <!--Toolbar-->
                <!--Toolbar-->
            </div>
            <!--MAIN CONTENT ENDS-->
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
