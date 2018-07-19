<?php

namespace MyFw;


use Closure;
use Exception;
use MyFw\exceptions\RouteException;

class Router
{
    private $uri;
    private $method;
    private $routes = ['GET' => [], 'POST' => [], 'PUT' => [], 'DELETE' => []];

    /**
     * Router constructor.
     * Recebe o metodo e a URI e atribui a seus respectivos atributos
     */
    public function __construct()
    {
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Wrapper para o método add() no método GET
     *
     * @param string $uri
     * @param string|Closure $callable
     */
    public function get(string $uri, $callable): void
    {
        $this->add('GET', $uri, $callable);
    }

    /**
     * Wrapper para o método add() no método POST
     *
     * @param string $uri
     * @param string|Closure $callable
     */
    public function post(string $uri, $callable): void
    {
        $this->add('POST', $uri, $callable);
    }

    /**
     * Wrapper para o método add() no método PUT
     *
     * @param string $uri
     * @param string|Closure $callable
     */
    public function put(string $uri, $callable): void
    {
        $this->add('PUT', $uri, $callable);
    }

    /**
     * Wrapper para o método add() no método DELETE
     *
     * @param string $uri
     * @param string|Closure $callable
     */
    public function delete(string $uri, $callable): void
    {
        $this->add('DELETE', $uri, $callable);
    }

    /**
     * Adiciona grupos de rotas
     *
     * @param string $group
     * @param $routes
     */
    public function group(string $group, $routes): void
    {
        if (is_array($routes)) {
            foreach ($routes as $key => $value) {
                $method = strtolower($value[0]);
                $uri = $group . $key;
                $callable = $value[1];
                $this->$method($uri, $callable);
            }
            return;
        }

        if($routes instanceof Closure){
            $groupRoutes = $routes();
            foreach ($groupRoutes as $key => $value) {
                $method = strtolower($value[0]);
                $uri = $group . $key;
                $callable = $value[1];
                $this->$method($uri, $callable);
            }
        }
    }

    /**
     * Retorna se uma rota já foi registrada em determinado método
     *
     * @param string $method
     * @param string $uri
     * @return bool
     */
    private function routeExists(string $method, string $uri): bool
    {
        return array_key_exists($uri, $this->routes[$method]);
    }

    /**
     * Registra uma rota
     *
     * @param string $method
     * @param string $uri
     * @param string|Closure $callable
     */
    private function add(string $method, string $uri, $callable): void
    {
        if ($this->routeExists($method, $uri)) {
            throw new RouteException("Rota $uri duplicada no método $method");
        } else {
            $this->routes[$method][$uri] = $callable;
        }
    }

    /**
     * Extrai o controller e o action de um callable
     *
     * @param string $callable
     * @return \stdClass
     */
    private function extractControllerAction(string $callable): \stdClass
    {
        $explode = explode('@', $callable);
        $object = new \stdClass();
        $object->controller = $explode[0];
        $object->action = $explode[1];
        return $object;
    }

    /**
     * Executa a busca da rota atual no array de rotas registradas
     *
     * @throws \ErrorException
     */
    public function run(): void
    {
        foreach ($this->routes[$this->method] as $uri => $callable) {
            $urlArray = explode('/', $this->uri);
            $routeArray = explode('/', $uri);
            $parameters = [];
            for ($i = 0; $i < count($routeArray); $i++) {
                if ((strpos($routeArray[$i], '{') !== false) && (count($urlArray) == count($routeArray))) {
                    $routeArray[$i] = $urlArray[$i];
                    $parameters[] = $urlArray[$i];
                }
                $uri = implode($routeArray, '/');
            }

            if ($uri == $this->uri) {
                if ($callable instanceof Closure) {
                    call_user_func_array($callable, $parameters);
                    return;
                } else {
                    $controller = $this->extractControllerAction($callable)->controller;
                    $action = $this->extractControllerAction($callable)->action;
                    call_user_func_array([ControllerFactory::build($controller), $action], $parameters);
                    return;
                }
                break;
            }
        }
        view('404', ['uri' => $this->uri]);
    }
}