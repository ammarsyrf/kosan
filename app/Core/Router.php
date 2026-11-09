<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private static array $routes = [];
    private static array $groupStack = [];
    private static array $namedRoutes = [];
    private static array $globalMiddleware = [];

    public static function use(string|Middleware $middleware): void
    {
        self::$globalMiddleware[] = $middleware;
    }

    public static function get(string $path, array|callable $handler, array $middleware = [], ?string $name = null): void
    {
        self::addRoute('GET', $path, $handler, $middleware, $name);
    }

    public static function post(string $path, array|callable $handler, array $middleware = [], ?string $name = null): void
    {
        self::addRoute('POST', $path, $handler, $middleware, $name);
    }

    public static function put(string $path, array|callable $handler, array $middleware = [], ?string $name = null): void
    {
        self::addRoute('PUT', $path, $handler, $middleware, $name);
    }

    public static function patch(string $path, array|callable $handler, array $middleware = [], ?string $name = null): void
    {
        self::addRoute('PATCH', $path, $handler, $middleware, $name);
    }

    public static function delete(string $path, array|callable $handler, array $middleware = [], ?string $name = null): void
    {
        self::addRoute('DELETE', $path, $handler, $middleware, $name);
    }

    public static function group(array $attributes, callable $callback): void
    {
        self::$groupStack[] = $attributes;
        $callback();
        array_pop(self::$groupStack);
    }

    private static function addRoute(string $method, string $path, array|callable $handler, array $middleware = [], ?string $name = null): void
    {
        $prefix = '';
        $groupMiddleware = [];

        foreach (self::$groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
            if (isset($group['middleware'])) {
                $groupMiddleware = array_merge($groupMiddleware, (array)$group['middleware']);
            }
        }

        $fullPath = '/' . trim($prefix . '/' . trim($path, '/'), '/');
        $allMiddleware = array_merge($groupMiddleware, $middleware);

        $route = [
            'method' => strtoupper($method),
            'path' => $fullPath,
            'pattern' => self::compilePattern($fullPath),
            'handler' => $handler,
            'middleware' => $allMiddleware,
            'name' => $name,
        ];

        self::$routes[] = $route;

        if ($name !== null) {
            self::$namedRoutes[$name] = $fullPath;
        }
    }

    private static function compilePattern(string $path): string
    {
        // Replace {param} or {param:regex} with named regex capture groups
        $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)(?::([^\}]+))?\}/', function ($matches) {
            $name = $matches[1];
            $regex = $matches[2] ?? '[^/]+';
            return '(?P<' . $name . '>' . $regex . ')';
        }, $path);

        return '#^' . $pattern . '$#';
    }

    public static function url(string $name, array $params = []): string
    {
        if (!isset(self::$namedRoutes[$name])) {
            return '/';
        }

        $path = self::$namedRoutes[$name];
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', (string)$value, $path);
        }

        return $path;
    }

    public static function dispatch(Request $request): Response
    {
        $uri = $request->uri();
        $method = $request->method();
        $methodMatched = false;

        foreach (self::$routes as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                if ($route['method'] !== $method && !($route['method'] === 'GET' && $method === 'HEAD')) {
                    $methodMatched = true;
                    continue;
                }

                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
                $request->setParams($params);

                // Build middleware chain
                $middlewareChain = array_merge(self::$globalMiddleware, $route['middleware']);

                $runner = self::buildMiddlewarePipeline($middlewareChain, function (Request $req) use ($route) {
                    return self::executeHandler($route['handler'], $req);
                });

                return $runner($request);
            }
        }

        if ($methodMatched) {
            return Response::html(View::renderError(405, 'Method Not Allowed')->getContent(), 405);
        }

        return Response::html(View::renderError(404, 'Halaman Tidak Ditemukan')->getContent(), 404);
    }

    private static function buildMiddlewarePipeline(array $middlewareList, callable $destination): callable
    {
        return array_reduce(
            array_reverse($middlewareList),
            function ($next, $middleware) {
                return function (Request $request) use ($middleware, $next): Response {
                    $instance = is_string($middleware) ? new $middleware() : $middleware;
                    if (!$instance instanceof Middleware) {
                        throw new \RuntimeException("Middleware must implement " . Middleware::class);
                    }
                    return $instance->handle($request, $next);
                };
            },
            $destination
        );
    }

    private static function executeHandler(array|callable $handler, Request $request): Response
    {
        if (is_callable($handler)) {
            $response = $handler($request);
        } elseif (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            $response = $controller->$method($request);
        } else {
            throw new \RuntimeException('Invalid route handler.');
        }

        if ($response instanceof Response) {
            return $response;
        }

        if (is_array($response)) {
            return Response::json($response);
        }

        return Response::html((string)$response);
    }

    public static function clear(): void
    {
        self::$routes = [];
        self::$groupStack = [];
        self::$namedRoutes = [];
        self::$globalMiddleware = [];
    }
}

