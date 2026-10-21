<?php
// src/Router.php - Lightweight High-Performance HTTP Router

class Router {
    private array $routes = [];

    public function get(string $path, callable|array $handler): self {
        $this->routes['GET'][$path] = $handler;
        return $this;
    }

    public function post(string $path, callable|array $handler): self {
        $this->routes['POST'][$path] = $handler;
        return $this;
    }

    public function dispatch(string $uri, string $method): void {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $method = strtoupper($method);
        if ($method === 'HEAD') {
            $method = 'GET';
        }

        // Normalize trailing slashes (except root)
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        // Direct match
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            $this->invoke($handler, []);
            return;
        }

        // Pattern matching for dynamic routes (e.g. /tenants/{id})
        if (!empty($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $handler) {
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route);
                $pattern = '#^' . $pattern . '$#';
                if (preg_match($pattern, $path, $matches)) {
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    $this->invoke($handler, $params);
                    return;
                }
            }
        }

        // 404 Handler
        http_response_code(404);
        echo "<!DOCTYPE html><html><head><title>404 Not Found</title>";
        echo "<style>body{font-family:Roboto,sans-serif;background:#f8f9fa;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}";
        echo ".card{background:#fff;padding:40px;border-radius:12px;box-shadow:0 1px 3px rgba(60,64,67,0.3);text-align:center;max-width:400px;}";
        echo "h1{color:#1a73e8;margin:0 0 10px;}p{color:#5f6368;margin-bottom:20px;}</style></head><body>";
        echo "<div class='card'><h1>404</h1><p>The page you requested could not be found.</p><a href='/' style='color:#1a73e8;text-decoration:none;font-weight:500;'>Return to Home</a></div></body></html>";
    }

    private function invoke(callable|array $handler, array $params): void {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
        } elseif (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
            $instance = is_string($class) ? new $class() : $class;
            call_user_func_array([$instance, $method], $params);
        }
    }
}
