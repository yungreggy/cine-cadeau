<?php

// Dans votre contrôleur ou script principal


// Dans votre classe Twig, utilisez la variable passée au lieu de créer une nouvelle date
class Twig
{
    static public function render($template, $data = array())
    {
        $loader = new \Twig\Loader\FilesystemLoader('view');
        $twig = new \Twig\Environment(
            $loader,
            array(
                'auto_reload' => true,
                'debug' => true, // Active le mode debug
            )
        );

        

        $twig->addExtension(new \Twig\Extension\DebugExtension());

        // Vérification de la session pour déterminer si l'utilisateur est un invité
        $isGuest = !isset($_SESSION['fingerPrint']) || $_SESSION['fingerPrint'] !== md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);

        // Ajout de variables globales à Twig
        $twig->addGlobal('path', PATH_DIR);
        $twig->addGlobal('isGuest', $isGuest);
        $twig->addGlobal('session', $_SESSION);
        $twig->addGlobal('user_id', isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);

        // Utilisation de la date passée depuis le contrôleur
        $twig->addGlobal('dateFormatted', isset($data['dateFormatted']) ? $data['dateFormatted'] : null);

        echo $twig->render($template, $data);
    }
}





?>