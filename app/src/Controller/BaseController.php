<?php

namespace Src\Controller;

use Symfony\Component\HttpFoundation\Response;

class BaseController
{
    protected function render(string $path, array $attributes = [], $status = 200, array $headers = []): Response
    {
        extract($attributes, EXTR_SKIP);

        ob_start();

        include __DIR__ . '/../../views' . $path;

        $content = ob_get_clean();

        return new Response($content);
    }
}