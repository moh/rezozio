<?php

    set_include_path('..'.PATH_SEPARATOR);
    require_once("lib/watchdog_service.php");
    $current = $_SESSION["ident"]; // l'utilisateur

    /*
    * Returns image ressource initialized with data from $fileName (auto-detect format)
    */
    function createImageFromFile($fileName){
        return imagecreatefromstring(file_get_contents($fileName));
    }
    
    // check if the file is an image or not
    if(strpos($_FILES["image"]["type"], "image") !== false){
        try{
            $data = new DataLayer();

            $image = createImageFromFile($_FILES["image"]["tmp_name"]);
            $largeur = imagesx($image);
            $hauteur = imagesy($image);

            $bigSquare = min($largeur, $hauteur); // the max squar in the image
            $bigSquareImage = imagecreatetruecolor($bigSquare, $bigSquare); 

            // extract the biggest square from the image
            imagecopyresampled($bigSquareImage, $image, 
            0,0, ($largeur - $bigSquare)/2, ($hauteur - $bigSquare)/2,
            $bigSquare, $bigSquare, $bigSquare, $bigSquare);
            

            $imageSmall = imagecreatetruecolor(48, 48);
            $imageLarge = imagecreatetruecolor(256, 256);

            // take the small image part
            imagecopyresampled($imageSmall, $bigSquareImage,
            0,0, 0, 0,
            48, 48, $bigSquare, $bigSquare);

            // take the large image part
            imagecopyresampled($imageLarge, $bigSquareImage,
            0,0, 0, 0,
            256, 256, $bigSquare, $bigSquare);

            $fluxImageSmall = fopen("php://temp", "r+");
            $fluxImageLarge = fopen("php://temp", "r+");

            imagepng($imageSmall , $fluxImageSmall);
            imagepng($imageLarge, $fluxImageLarge);

            rewind($fluxImageLarge);
            rewind($fluxImageSmall);

            $data->uploadAvatarSmall(["data"=>$fluxImageSmall, "mimetype"=>"image/png"], $current);
            $data->uploadAvatarLarge(["data"=>$fluxImageLarge, "mimetype"=>"image/png"], $current);
            produceResult(true);
        }
        catch(PDOException $e){
            produceError($e->getMessage());
        }
    }
    else{
        produceError("le fichier n'est pas un image !");
    }
?>