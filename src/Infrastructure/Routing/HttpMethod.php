<?php
declare(strict_types=1);

namespace App\Infrastructure\Routing;

enum HttpMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
}
