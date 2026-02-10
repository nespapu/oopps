<?php
namespace App\Application\Routing;

interface RoutePatternProvider
{
    /** @return list<string> */
    public function patterns(): array;
}
