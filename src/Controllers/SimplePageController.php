<?php

namespace Vzr\Controllers;

use Twig\Loader\FilesystemLoader;

class SimplePageController
{
    /**
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\LoaderError
     */
    public static function show404()
    {
        $loader = new FilesystemLoader($_SERVER["DOCUMENT_ROOT"] . '/../templates');
        $twig = new \Twig\Environment($loader, array('debug' => true));
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        echo $twig->render('404.twig');
    }
}
