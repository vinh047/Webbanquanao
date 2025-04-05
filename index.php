
<?php

    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>sagkuto</title>
    <link rel="icon" type="./Images/png" href="./assets/img/logo_favicon/favicon.png">
    <link rel="stylesheet" href="./assets/css/header.css">
    <link rel="stylesheet" href="./assets/css/slide.css">
    <link rel="stylesheet" href="./assets/css/footer.css">
    <link rel="stylesheet" href="./assets/fonts/font.css">
    <link rel="stylesheet" href="./assets/fonts/error.css">
</head>
<body>';
?>

<?php

    include("./layout/header.php");

?>

<?php


    if(isset($_GET['page']))
    {
        $page = $_GET['page'];
        if(is_numeric($page))
        {
            include('./layout/product.php');
        }else 
        {
            switch($page)
            {
                case 'ao':
                case 'quan':
                case 'aopolo':
                case 'aosomi':
                case 'aokhoac':
                    include('./layout/phanloai.php');
                    break;
                case 'sanpham':
                    if (isset($_GET['phanloai'])) {
                        include('./layout/phanloai.php');
                    } else {
                        include('./layout/product.php');
                    }
                    break;
                case 'error':
                    {
                        include('./layout/error404.php');
                    }
                    break;
                default:
                    include('./layout/notfound.php');
                    break;
            }
            
        }
    }else
    {
        include('./layout/home.php');
    }




?>








<?php

        include("./layout/footer.php");

?>