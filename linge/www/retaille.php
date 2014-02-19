<?php
phpinfo();
    $imgURL = $_GET['image'];
    ##-- Get Image file from Port 80 --##
    $fp = fopen($imgURL, "r");
    $imageFile = fread ($fp, 3000000);
    fclose($fp);

    ##-- Create a temporary file on disk --##
    $tmpfname = tempnam ("/temp", "IMG");

    ##-- Put image data into the temp file --##
    $fp = fopen($tmpfname, "w");
    fwrite($fp, $imageFile);
    fclose($fp);

    ##-- Load Image from Disk with GD library --##
    $img = imagecreatefromjpeg ($tmpfname);

    //Dimensions de l'image
    $imgWidth = imagesx($img);
    $imgHeight = imagesy($img);

    $maxWidth = 220;
    $maxHeight = 242;
    
    //Facteur largeur/hauteur des dimensions max
    $whFact = $maxWidth/$maxHeight;

    //Facteur largeur/hauteur de l'original
    $imgWhFact = $imgWidth/$imgHeight;

    //fixe les dimensions du thumb
    if($whFact < $imgWhFact)
    {
        //Si largeur déterminante
        $thumbWidth  = $maxWidth;
        $thumbHeight = $thumbWidth/$imgWhFact;
    }
    else
    {
        //Si hauteur déterminante
        $thumbHeight = $maxHeight;
        $thumbWidth = $thumbHeight*$imgWhFact;
    }
    
    //Crée le thumb (image réduite)
    $imgThumb = imagecreatetruecolor($thumbWidth, $thumbHeight);

    //Insère l'image de base redimensionnée
    imagecopyresized($imgThumb, $img, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $imgWidth, $imgHeight);
    
    ##-- Delete Temporary File --##
    unlink($tmpfname);

    ##-- Check for errors --##
    if (!$img) {
        print "Could not create JPEG image $imgURL";
    }
    
    echo Header( "Content-Type: image/jpeg");

    echo imagejpeg($img, '', 100);




?>
