<?php

namespace Wcms;

class Controllerhome extends Controllerpage
{
    /** @var Modelhome */
    protected $modelhome;
    protected $opt;
    /** @var Optlist */
    protected $optlist;

    public function __construct($render)
    {
        parent::__construct($render);
        $this->modelhome = new Modelhome;
    }




    public function desktop()
    {
        if ($this->user->isvisitor() && Config::homepage() === 'redirect' && Config::homeredirect() !== null) {
            $this->routedirect('pageread/', ['page' => Config::homeredirect()]);
        } else {




            $table = $this->modelhome->getlister();
            $this->opt = $this->modelhome->optinit($table);

            $table2 = $this->modelhome->table2($table, $this->opt);

            $columns = $this->modelhome->setcolumns($this->user->columns());

            $vars = ['user' => $this->user, 'table2' => $table2, 'opt' => $this->opt, 'columns' => $columns];
            $vars['footer'] = ['version' => getversion(), 'total' => count($table), 'database' => Config::pagetable()];

            if (isset($_POST['query']) && $this->user->iseditor()) {
                $datas = array_merge($_POST, $_SESSION['opt']);
                $this->optlist = $this->modelhome->Optlistinit($table);
                $this->optlist->hydrate($datas);
                $vars['optlist'] = $this->optlist;
            }

            $this->showtemplate('home', $vars);
        }
    }

    public function columns()
    {
        if (isset($_POST['columns']) && $this->user->iseditor()) {
            $user = $this->usermanager->get($this->user->id());
            $user->hydrate($_POST);
            $this->usermanager->add($user);
            $this->usermanager->writesession($user);
        }
        $this->routedirect('home');
    }

    public function search()
    {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'read':
                        $this->routedirect('pageread/', ['page' => $_POST['id']]);
                        break;

                    case 'edit':
                        $this->routedirect('pageedit', ['page' => $_POST['id']]);
                        break;
                }
            }
        } else {
            $this->routedirect('home');
        }
    }

    /**
     * Render every pages in the database 
     */
    public function renderall()
    {
        if ($this->user->iseditor()) {
            $pagelist = $this->modelhome->getlister();
            foreach ($pagelist as $page) {
                $this->renderpage($page);
                $this->pagemanager->update($page);
            }
        }
        $this->routedirect('home');
    }

    public function bookmark()
    {
        if ($this->user->iseditor() && isset($_POST['action']) && isset($_POST['id']) && !empty($_POST['id'])) {
            if ($_POST['action'] == 'add' && isset($_POST['query'])) {
                if (isset($_POST['user']) && $_POST['user'] == $this->user->id()) {
                    $usermanager = new Modeluser();
                    $user = $usermanager->get($_POST['user']);
                    $user->addbookmark($_POST['id'], $_POST['query']);
                    $usermanager->add($user);
                } else {
                    Config::addbookmark($_POST['id'], $_POST['query']);
                    Config::savejson();
                }
            } elseif ($_POST['action'] == 'del') {
                if(isset($_POST['user']) && $_POST['user'] == $this->user->id()) {
                    $usermanager = new Modeluser();
                    $user = $usermanager->get($_POST['user']);
                    foreach ($_POST['id'] as $id) {
                        $user->deletebookmark($id);
                    }
                    $usermanager->add($user);
                } else {
                    foreach ($_POST['id'] as $id) {
                        Config::deletebookmark($id);
                    }
                    Config::savejson();
                }
            }
        }
        $this->routedirect('home');
    }
}

?>