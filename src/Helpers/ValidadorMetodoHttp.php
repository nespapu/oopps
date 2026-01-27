<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Helpers\Router;

final class ValidadorMetodoHttp
{
    public static function soloGet(callable $manejador): callable
    {
        return function () use ($manejador): void {
            if (!Router::esGet()) {
                http_response_code(405);
                return;
            }
            $manejador();
        };
    }

    public static function soloPost(callable $manejador): callable
    {
        return function () use ($manejador): void {
            if (!Router::esPost()) {
                http_response_code(405);
                return;
            }
            $manejador();
        };
    }

    public static function segunMetodo(?callable $get, ?callable $post): callable
    {
        return function () use ($get, $post): void {
            if (Router::esGet()) {
                if ($get) { $get(); return; }
            }

            if (Router::esPost()) {
                if ($post) { $post(); return; }
            }

            http_response_code(405);
        };
    }

    public static function solo(callable $manejador, bool $permitido): callable
    {
        return function () use ($manejador, $permitido): void {
            if (!$permitido) {
                http_response_code(405);
                return;
            }
            $manejador();
        };
    }
}
