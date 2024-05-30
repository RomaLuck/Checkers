<?php

function view($path, $attributes = []): void
{
    extract($attributes);

    require base_path('views/' . $path);
}

function redirect($path): void
{
    header("Location: {$path}");
    exit();
}

function base_path($path): string
{
    return BASE_PATH . $path;
}