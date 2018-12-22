<?php

class Controller
{

    protected $user;
    protected $router;
    /**
     * @var Modeluser
     */
    protected $usermanager;
    protected $plates;

	public function __construct($router) {
        $this->setuser();
        $this->router = $router;        
        $this->initplates();
	}

    public function setuser()
    {
        $this->usermanager = new Modeluser;        
        $this->user = $this->usermanager->readsession();
    }

    public function initplates()
    {
        $router = $this->router;
        $this->plates = new League\Plates\Engine(Model::TEMPLATES_DIR);
        $this->plates->registerFunction('url', function (string $string, array $vars = []) use ($router) {
            return $router->generate($string, $vars);
        });
        $this->plates->registerFunction('uart', function (string $string, string $id) use ($router) {
            return $router->generate($string, ['art' => $id]);
        });
    }

    public function showtemplate($template, $params)
    {
        $params = array_merge($this->commonsparams(), $params);
        echo $this->plates->render($template, $params);
    }

    public function commonsparams()
    {
        $commonsparams = [];
        $commonsparams['router'] = $this->router;
        $commonsparams['user'] = $this->user;
        $commonsparams['css'] = Model::csspath();
        return $commonsparams;
    }





    public function redirect($url)
    {
        header('Location: ' . $url);
    }

    public function routedirect(string $route, array $vars = [])
    {
        $this->redirect($this->router->generate($route, $vars));
    }

}





?>