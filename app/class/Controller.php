<?php

namespace Wcms;

use AltoRouter;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use League\Plates\Engine;
use RuntimeException;
use Wcms\Exception\Database\Notfoundexception;
use Wcms\Exception\Databaseexception;

class Controller
{
    protected Servicesession $servicesession;

    protected Workspace $workspace;

    /** @var User */
    protected $user;

    /** @var Altorouter */
    protected $router;

    /** @var Modeluser */
    protected $usermanager;

    /** @var Modelpage */
    protected $pagemanager;

    protected $plates;

    /** @var DateTimeImmutable */
    protected $now;

    public function __construct(AltoRouter $router)
    {
        $this->servicesession = new Servicesession();
        $this->workspace = $this->servicesession->getworkspace();
        $this->usermanager = new Modeluser();

        $this->user = new User();
        $this->setuser();
        $this->router = $router;
        $this->pagemanager = new Modelpage(Config::pagetable());
        $this->initplates();
        $this->now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));
    }

    protected function setuser()
    {
        // check session, then cookies
        if (!is_null($this->servicesession->getuser())) {
            $sessionuser = $this->servicesession->getuser();
            try {
                $this->user = $this->usermanager->get($sessionuser);
            } catch (Notfoundexception $e) {
                Logger::warning("Deleted session using non existing user : '$sessionuser'");
                $this->servicesession->empty(); // empty the session as a non existing user was set
            }
        } elseif (!empty($_COOKIE['rememberme'])) {
            try {
                $modelconnect = new Modelconnect();
                $datas = $modelconnect->checkcookie();
                $user = $this->usermanager->get($datas['userid']);
                if ($user->checksession($datas['wsession'])) {
                    $this->user = $user;
                    $this->servicesession->setwsessionid($datas['wsession']);
                    $this->servicesession->setuser($user->id());
                } else {
                    $modelconnect->deleteauthcookie(); // As not listed in the user
                }
            } catch (Notfoundexception $e) {
                Logger::warning('Deleted auth cookie using non existing user');
                $modelconnect->deleteauthcookie(); // Delete auth cookie as a non existing user was set
            } catch (RuntimeException $e) {
                Model::sendflashmessage("Invalid Autentification cookie exist : $e", "warning");
            }
        }
    }

    public function initplates()
    {
        $router = $this->router;
        $this->plates = new Engine(Model::TEMPLATES_DIR);
        $this->plates->registerFunction('url', function (string $string, array $vars = [], string $get = '') {
            return $this->generate($string, $vars, $get);
        });
        $this->plates->registerFunction('upage', function (string $string, string $id) {
            return $this->generate($string, ['page' => $id]);
        });
        $this->plates->registerFunction('ubookmark', function (string $string, string $id) {
            return $this->generate($string, ['bookmark' => $id]);
        });
        $this->plates->registerFunction('caneditpage', function (Page $page) {
            return $this->canedit($page);
        });
        $this->plates->registerFunction('candeletepage', function (Page $page) {
            return $this->candelete($page);
        });
        $this->plates->addData(['flashmessages' => Model::getflashmessages()]);
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
        $commonsparams['pagelist'] = $this->pagemanager->list();
        $commonsparams['css'] = Model::assetscsspath();
        $commonsparams['now'] = new DateTimeImmutable();
        $commonsparams['workspace'] = $this->workspace;
        return $commonsparams;
    }



    /**
     * Generate the URL for a named route. Replace regexes with supplied parameters.
     *
     * @param string $route The name of the route.
     * @param array $params Associative array of parameters to replace placeholders with.
     * @param string $get Optionnal query GET parameters formated
     * @return string The URL of the route with named parameters in place.
     * @throws InvalidArgumentException If the route does not exist.
     */
    public function generate(string $route, array $params = [], string $get = ''): string
    {
        try {
            return $this->router->generate($route, $params) . $get;
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Redirect to URL and send 302 code
     * @param string $url to redirect to
     */
    public function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    public function routedirect(string $route, array $vars = [], $gets = [])
    {

        $get = empty($gets) ? "" : "?" . http_build_query($gets);
        $this->redirect($this->generate($route, $vars, $get));
    }

    public function error(int $code)
    {
        http_response_code($code);
        exit;
    }

    /**
     * Used to display a count / total stat
     */
    public function sendstatflashmessage(int $count, int $total, string $message)
    {
        if ($count === $total) {
            Model::sendflashmessage($count . ' / ' . $total . ' ' . $message, Model::FLASH_SUCCESS);
        } elseif ($count > 0) {
            Model::sendflashmessage($count . ' / ' . $total . ' ' . $message, Model::FLASH_WARNING);
        } else {
            Model::sendflashmessage($count . ' / ' . $total . ' ' . $message, Model::FLASH_ERROR);
        }
    }

    /**
     * Destroy session and cookie token in user database
     */
    protected function disconnect()
    {
        try {
            $this->user->destroysession($this->servicesession->getwsessionid());
            $cookiemanager = new Modelconnect();
            $cookiemanager->deleteauthcookie();
            $this->servicesession->empty();
            $this->usermanager->update($this->user);
        } catch (Databaseexception $e) {
            Logger::errorex($e);
        }
    }

    /**
     * Tell if the current user can edit the given Page
     *
     * User need to be SUPEREDITOR, otherwise, it need to be author of a page.
     *
     * @param Page $page
     */
    protected function canedit(Page $page): bool
    {
        if ($this->user->issupereditor()) {
            return true;
        } elseif ($this->user->isinvite() || $this->user->iseditor()) {
            return (in_array($this->user->id(), $page->authors()));
        } else {
            return false;
        }
    }

    /**
     * Tell if the current user can delete the given Page
     *
     * User need to be SUPEREDITOR, otherwise, it need to be the only author of a page.
     *
     * @param Page $page
     */
    protected function candelete(Page $page): bool
    {
        if ($this->user->issupereditor()) {
            return true;
        } elseif ($this->user->isinvite() || $this->user->iseditor()) {
            return ($page->authors() === [$this->user->id()]);
        } else {
            return false;
        }
    }
}
