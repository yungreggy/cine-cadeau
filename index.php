<?php 
session_start();



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('PATH_DIR', 'http://localhost/alex/cine-cadeau/');


require_once('controller/Controller.php');
require_once('library/RequirePage.php');
require_once __DIR__ . '/vendor/autoload.php';
require_once('library/Twig.php');
require_once('library/CheckSession.php');

//$url = isset($_GET["url"]) ? explode ('/', ltrim($_GET["url"], '/')) : '/';

$url = isset($_SERVER['PATH_INFO']) ? explode('/', ltrim($_SERVER['PATH_INFO'], '/')) : '/';


//print_r($url);

// echo $url[0]; // controller
// echo $url[1]; // method
// echo $url[2]; // value


if ($url == '/') {
    require_once('controller/ControllerHome.php');
    $controller = new ControllerHome;
    echo $controller->index();
} else {
    //controller
    $requestURL = $url[0];
    $requestURL = ucfirst($requestURL);
    $controllerPath = __DIR__ . "/controller/Controller" . $requestURL . ".php";

    if (file_exists($controllerPath)) {
        require_once($controllerPath);
        $controllerName = 'Controller' . $requestURL;
        $controller = new $controllerName;
        if (isset($url[1])) {
            $method = $url[1];
            if (isset($url[2])) {
                echo $controller->$method($url[2]);
            } else {
                echo $controller->$method();
            }
        } else {
            echo $controller->index();
        }


    } else {
        require_once('controller/ControllerHome.php');
        $controller = new ControllerHome;
        echo $controller->error('404');
    }
}


// https://packagist.org/packages/rakit/validation
?>

