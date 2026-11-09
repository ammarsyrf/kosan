<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = [], ?string $layout = 'layouts/main'): Response
    {
        return View::render($view, $data, $layout);
    }

    protected function json(mixed $data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    protected function redirect(string $url, int $statusCode = 302): Response
    {
        return Response::redirect($url, $statusCode);
    }

    protected function validate(Request $request, array $rules): array
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->isJson()) {
                Response::json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422)->send();
                exit;
            }

            Session::flash('errors', $validator->errors());
            Session::flash('old', $request->all());

            $referer = $_SERVER['HTTP_REFERER'] ?? '/';
            Response::redirect($referer)->send();
            exit;
        }

        return array_intersect_key($request->all(), $rules);
    }
}

