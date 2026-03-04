<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $view, array $data = []): void
    {
        // Make data available as variables inside the view
        extract($data, EXTR_SKIP);

        $viewsBasePath = __DIR__ . '/../../views';

        $viewFile = "{$viewsBasePath}/{$view}.php";

        if (!is_file($viewFile)) {
            throw new \RuntimeException("View not found: {$viewFile}");
        }

        // Capture view output
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Load main layout
        $layoutFile = "{$viewsBasePath}/layouts/layout.php";

        if (!is_file($layoutFile)) {
            throw new \RuntimeException("Layout not found: {$layoutFile}");
        }

        require $layoutFile;
    }
}