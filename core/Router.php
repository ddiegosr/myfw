<?php

namespace Core;


use Closure;
use Exception;

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
    public function get(string $uri, $callable)
    {
        $this->add('GET', $uri, $callable);
    }

    /**
     * Wrapper para o método add() no método POST
     *
     * @param string $uri
     * @param string|Closure $callable
     */
    public function post(string $uri, $callable)
    {
        $this->add('POST', $uri, $callable);
    }

    /**
     * Wrapper para o método add() no método PUT
     *
     * @param string $uri
     * @param string|Closure $callable
     */
    public function put(string $uri, $callable)
    {
        $this->add('PUT', $uri, $callable);
    }

    /**
     * Wrapper para o método add() no método DELETE
     *
     * @param string $uri
     * @param string|Closure $callable
     */
    public function delete(string $uri, $callable)
    {
        $this->add('DELETE', $uri, $callable);
    }

    /**
     * Retorna se uma rota já foi registrada em determinado método
     *
     * @param string $method
     * @param string $uri
     * @return bool
     */
    private function routeExists(string $method, string $uri)
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
    private function add(string $method, string $uri, $callable)
    {
        if($this->routeExists($method, $uri)){
            echo "Rota $uri duplicada no método $uri";
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
    private function extractControllerAction(string $callable)
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
     * @return mixed
     */
    public function run()
    {
        foreach ($this->routes[$this->method] as $uri => $callable) {
            $urlArray = explode('/', $this->uri);
            $routeArray = explode('/', $uri);
            $parameters = [];
            for ($i = 0; $i < count($routeArray); $i++){
                if ((strpos($routeArray[$i], '{') !== false) && (count($urlArray) == count($routeArray))) {
                    $routeArray[$i] = $urlArray[$i];
                    $parameters[] = $urlArray[$i];
                }
                $uri = implode($routeArray, '/');
            }

            if ($uri == $this->uri) {
                if ($callable instanceof Closure) {
                    return call_user_func_array($callable, $parameters);
                } else {
                    $controller = $this->extractControllerAction($callable)->controller;
                    $action = $this->extractControllerAction($callable)->action;
                    try {
                        return call_user_func_array([ControllerFactory::build($controller), $action], $parameters);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                }
                break;
            }
        }
        echo "404";
    }
}