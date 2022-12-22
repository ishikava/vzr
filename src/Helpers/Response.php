<?php


namespace Vzr\Helpers;


class Response
{
    public static function show404()
    {
        http_response_code(404);

        $loader = new \Twig\Loader\FilesystemLoader($_SERVER["DOCUMENT_ROOT"] . '/../templates');
        $twig = new \Twig\Environment($loader);
        echo $twig->render('404.twig');
    }
}
