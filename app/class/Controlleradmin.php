<?php

namespace Wcms;

use RuntimeException;
use Wcms\Exception\Filesystemexception;

class Controlleradmin extends Controller
{
    /** @var Modelmedia $mediamanager */
    protected $mediamanager;
    /** @var Modeladmin */
    protected $adminmanager;

    public function __construct($router)
    {
        parent::__construct($router);

        $this->adminmanager = new Modeladmin();

        if ($this->user->isvisitor()) {
            http_response_code(401);
            $this->showtemplate('connect', ['route' => 'admin']);
            exit;
        }
        if (!$this->user->isadmin()) {
            http_response_code(403);
            $this->showtemplate('forbidden');
            exit;
        }
    }

    public function desktop()
    {
        $datas['pagelist'] = $this->pagemanager->list();
        $this->mediamanager = new Modelmedia();
        $datas['faviconlist'] = $this->mediamanager->listfavicon();
        $datas['thumbnaillist'] = $this->mediamanager->listthumbnail();
        $datas['themes'] = $this->mediamanager->listthemes();

        $globalcssfile = Model::GLOBAL_CSS_FILE;

        if (is_file($globalcssfile)) {
            $datas['globalcss'] = file_get_contents($globalcssfile);
        } else {
            $datas['globalcss'] = "";
        }

        $datas['pagesdblist'] = $this->adminmanager->pagesdblist();
        $datas['pagesdbtree'] = $this->mediamanager->listdir(Model::PAGES_DIR);

        $this->showtemplate('admin', $datas);
    }

    public function update()
    {
        try {
            Fs::accessfile(Model::GLOBAL_CSS_FILE, true);
            Fs::writefile(Model::GLOBAL_CSS_FILE, $_POST['globalcss'], 0664);
            Config::hydrate($_POST);
            Config::savejson();
            $this->sendflashmessage("Configuration succesfully updated", self::FLASH_SUCCESS);
        } catch (Filesystemexception $e) {
            $this->sendflashmessage("Can't write config file or global css file", self::FLASH_ERROR);
        }
        $this->routedirect('admin');
    }

    public function database()
    {
        if (!empty($_POST['action'])) {
            switch ($_POST['action']) {
                case 'duplicate':
                    if (!empty($_POST['dbsrc']) && !empty($_POST['dbtarget'])) {
                        $this->adminmanager->copydb($_POST['dbsrc'], $_POST['dbtarget']);
                    }
                    break;
                case 'select':
                    if (!empty($_POST['pagetable'])) {
                        Config::hydrate($_POST);
                        try {
                            $this->pagemanager->flushrendercache();
                            Config::savejson();
                        } catch (RuntimeException $e) {
                            $this->sendflashmessage(
                                'Cannot update Config file : ' . $e->getMessage(),
                                self::FLASH_ERROR
                            );
                        }
                    }
                    break;
            }
        }
        $this->routedirect('admin');
    }
}
