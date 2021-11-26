<?php
class App
{
    private $__controller, $__action, $__params, $__routes, $__db;
    public static $app;
    public function __construct()
    {
        self::$app = $this;
        global $routes, $config;
        // var_dump($config);
        $this->__routes = new Route();
        if (!empty($routes['default_controller'])) {
            $this->__controller = $routes['default_controller'];
        }
        $this->__action = 'index';
        $this->__params = [];

        if (class_exists('DB')) {
            $dbObject = new DB();
            $this->__db = $dbObject->db;
        }

        $this->handleUrl();
    }

    public function getUrl()
    {
        if (!empty($_SERVER['PATH_INFO'])) {
            $url = $_SERVER['PATH_INFO'];
        } else {
            $url = '/';
        }
        return $url;
    }

    public function handleUrl()
    {
        $url = $this->getUrl();
        $url = $this->__routes->handleRoute($url);
        $urlArr = array_values(array_filter(explode('/', $url)));
        // foreach check file in controller
        $urlCheck = '';
        if (!empty($urlArr)) {
            foreach ($urlArr as $key => $item) {
                $urlCheck .= $item . '/';
                $fileCheck = rtrim($urlCheck, '/');
                $fileArr = explode('/', $fileCheck);
                $fileArr[count($fileArr) - 1] = ucfirst($fileArr[count($fileArr) - 1]);
                $fileCheck = implode('/', $fileArr);

                if (!empty($urlArr[$key - 1])) {
                    unset($urlArr[$key - 1]);
                }

                if (file_exists('app/controllers/' . $fileCheck . '.php')) {
                    $urlCheck = $fileCheck;
                    break;
                }
            }
            $urlArr = array_values($urlArr);
        }
        // handle controller
        if (!empty($urlArr[0])) {
            $this->__controller = ucfirst($urlArr[0]);
        } else {
            $this->__controller = ucfirst($this->__controller);
        }

        // check urlCheck empty
        if (empty($urlCheck)) {
            $urlCheck = $this->__controller;
        }

        if (file_exists('app/controllers/' . $urlCheck . '.php')) {
            require_once 'app/controllers/' . $urlCheck . '.php';
            // check class $this->__controller exists
            if (class_exists($this->__controller)) {
                $this->__controller = new $this->__controller;
                unset($urlArr[0]);
                $urlArr = array_values($urlArr);
                if (!empty($this->__db)) {
                    $this->__controller->db = $this->__db;
                }
            }
        } else {
            $this->loadError();
        }

        //handle action
        if (!empty($urlArr[0])) {
            $this->__action = ucfirst($urlArr[0]);
            unset($urlArr[0]);
        }

        //handle params
        $this->__params = array_values($urlArr);
        // check method exists
        if (method_exists($this->__controller, $this->__action)) {
            call_user_func_array([$this->__controller, $this->__action], $this->__params);
        } else {
            $this->loadError();
        }
    }
    public function getCurrentController()
    {
        return $this->__controller;
    }
    public function loadError($name = '404', $data = [])
    {
        extract($data);
        require_once 'errors/' . $name . '.php';
    }
}
