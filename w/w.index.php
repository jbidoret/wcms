<?php

// _____________________________________________________ R E Q U I R E ________________________________________________________________



session_start();



require(__DIR__.'/fn/w.fn.php');

function class_autoloader($class)
{
    require(__DIR__. DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR  . strtolower($class) . '.php');
}





require(__DIR__.'/../vendor/autoload.php');

spl_autoload_register('class_autoloader');




//$text = 'blabla il fait un temps vraiment [dégeu](=degeulasse) parce quil pleut [baucoup](?id=orthographe), en amazonie la [vie](?id=prout) et la [mort](?id=prout) sont des notions très proches.';

//$artmanager = new Modelart;

// var_dump($artmanager->getlisterwhere(['id', 'description'], ['55', '44', 'lol']));

try {
  $router = new Router;

} catch(Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}






exit;

// ________________________________________________________ I N S T A L _________________________________________________

$app = new App();
$aff = new Aff();


$config = $app->readconfig();
if (!$config) {
    $message = 'config_file_error';
    echo $message;
    if (isset($_POST['config']) && $_POST['config'] == 'create') {
        $config = $app->createconfig($_POST);
        $app->savejson($config->tojson());
        header('Location: ./');

    } else {
        $aff->configform();
    }
    exit;
}


// _________________________________________________________ S E S ___________________________________________________________


if (!isset($_SESSION['level'])) {
    $session = 0;
} else {
    $session = $_SESSION['level'];
}

$app->setsession($session);




// _______________________________________________________ A C T I O N __________________________________________________________________


$router = new Router;





if (isset($_POST['action'])) {
    switch ($_POST['action']) {

        case 'login':
            $_SESSION['level'] = $app->login($_POST['pass'], $config);
            if (isset($_GET['id'])) {
                header('Location: ?id=' . $_GET['id']);
            } else {
                header('Location: ./');
            }
            break;

        case 'logout':
            $_SESSION['level'] = $app->logout();
            if (isset($_GET['id'])) {
                header('Location: ?id=' . $_GET['id']);
            } else {
                header('Location: ./');
            }
            break;

        case 'addmedia':
            $message = $app->addmedia($_FILES, 2 ** 24, $_POST['id']);
            header('Location: ./?aff=media&message=' . $message);
            break;



        case 'editcss':
            file_put_contents($app::GLOBAL_CSS_DIR, $_POST['editcss']);
            header('Location: ./?aff=admin');
            break;

        case 'editconfig':
            $config->hydrate($_POST);
            $app->savejson($config->tojson());
            header('Location: ./?aff=admin');
            break;


    }
}



// _____________________________________________________ D A T A B A S E __________________________________________________________________

if (isset($_POST['action'])) {
    $app->bddinit($config);

    switch ($_POST['action']) {

        case 'new':
            if (isset($_GET['id'])) {
                $art = new Art2($_GET);
                $art->reset();
                $app->add($art);
                header('Location: ?id=' . $_GET['id'] . '&edit=1');
            }
            break;

        case 'update':
            if ($app->exist($_GET['id'])) {
                $art = new Art2($_POST);
                $art->updatelinkfrom();
                $art->autotaglistcalc($app->taglist($app->getlister(['id', 'title', 'tag']), $art->autotaglist()));
                $app->update($art);
                if($config->fontsize() != $_POST['fontsize']) {
                    $config->setfontsize($_POST['fontsize']);
                    $app->savejson($config->tojson());
                }
                header('Location: ?id=' . $art->id() . '&edit=1&message='.$art->id().'_saved');
            }
            break;

        case 'display':
            if ($app->exist($_GET['id'])) {
                $art = new Art2($_POST);
                $art->updatelinkfrom();
                $art->autotaglistcalc($app->taglist($app->getlister(['id', 'title', 'tag']), $art->autotaglist()));
                $app->update($art);
                if($config->fontsize() != $_POST['fontsize']) {
                    $config->setfontsize($_POST['fontsize']);
                    $app->savejson($config->tojson());
                }
                header('Location: ?id=' . $art->id().'&message='.$art->id().'_saved');
            }
            break;

        case 'home':
            if ($app->exist($_GET['id'])) {
                $art = new Art2($_POST);
                $art->updatelinkfrom();
                $art->autotaglistcalc($app->taglist($app->getlister(['id', 'title', 'tag']), $art->autotaglist()));
                $app->update($art);
                if($config->fontsize() != $_POST['fontsize']) {
                    $config->setfontsize($_POST['fontsize']);
                    $app->savejson($config->tojson());
                }
                header('Location: ./?message='.$art->id().'_saved');
            }
            break;

        case 'delete':
            if ($app->exist($_GET['id'])) {
                $art = new Art2($_POST);
                $app->delete($art);
                header('Location: ?id=' . $art->id());
            }
            break;

        case 'massedit':
            if (isset($_POST['id'])) {
                foreach ($_POST['id'] as $id) {
                    if ($app->exist($id)) {
                        $art = $app->get($id);

                        switch ($_POST['massaction']) {
                            case 'do':
                                switch ($_POST['massedit']) {
                                    case 'delete':
                                        $app->delete($art);
                                        break;

                                    case 'erasetag':
                                        $art->settag('');
                                        $app->update($art);
                                        break;

                                    case 'erasetemplate':
                                        $art->settemplate('');
                                        $app->update($art);
                                        break;

                                    case 'not published':
                                        $art->setsecure(2);
                                        $app->update($art);
                                        break;

                                    case 'private':
                                        $art->setsecure(1);
                                        $app->update($art);
                                        break;

                                    case 'public':
                                        $art->setsecure(0);
                                        $app->update($art);
                                        break;
                                }
                                break;

                            case 'set template':
                                if (isset($_POST['masstemplate'])) {
                                    $art->settemplate($_POST['masstemplate']);
                                    $app->update($art);
                                }
                                break;

                            case 'add tag':
                                if (isset($_POST['targettag'])) {
                                    $art = $app->get($id);
                                    $tagstring = strip_tags(trim(strtolower($_POST['targettag'])));
                                    $taglist = str_replace(' ', '', $tagstring);
                                    $taglist = explode(",", $taglist);
                                    foreach ($taglist as $tag) {
                                        if (!in_array($tag, $art->tag('array'))) {
                                            $newtaglist = $art->tag('array');
                                            array_push($newtaglist, $tag);
                                            $art->settag($newtaglist);
                                        }
                                    }
                                    $app->update($art);
                                }
                                break;

                        }

                       


                    }
                    header('Location: ./');
                }
                break;
            }
    }
}





if (isset($_POST['actiondb'])) {
    $app->setbdd($config);

    switch ($_POST['actiondb']) {

        case 'addtable':
            if (isset($_POST['tablename'])) {
                $message = $app->addtable($config->dbname(), $_POST['tablename']);
                header('Location: ./?aff=admin&message=' . $message);
            }
            break;

        case 'duplicatetable':
            $message = $app->tableduplicate($config->dbname(), $_POST['arttable'], $_POST['tablename']);
            header('Location: ./?aff=admin&message=' . $message);            
            break;

    }
}




// _______________________________________________________ H E A D _____________________________________________________________

if (isset($_GET['id'])) {
    $app->bddinit($config);
    if ($app->exist($_GET['id'])) {
        $art = $app->get($_GET['id']);
        if (!isset($_GET['edit'])) {
            $_GET['edit'] = 0;
        }
        $aff->arthead($art, $app::GLOBAL_CSS_DIR, $_GET['edit']);
    } else {
        // $aff->head($_GET['id'], '', 'white');
        $aff->noarthead($_GET['id'], $app::GLOBAL_CSS_DIR);

    }
} elseif (isset($_GET['aff'])) {
    $aff->head($_GET['aff'], $_GET['aff'], $config->color4());
} else {
    $aff->head('home', 'home', $config->color4());
}







// _____________________________________________________ A L E R T _______________________________________________________________ 

if (isset($_GET['message'])) {
    echo '<span class="alert">' . $_GET['message'] . '</span>';
}








// ______________________________________________________ B O D Y _______________________________________________________________ 


$aff->nav($app);

if (array_key_exists('id', $_GET)) {
    $app->bddinit($config);
    include(__DIR__.'/controller/w.article.php');
} elseif (array_key_exists('tag', $_GET)) {
    $app->bddinit($config);
    echo '<h4>' . $_GET['tag'] . '</h4>';
    $aff->tag($app->getlister(['id', 'title', 'description', 'tag']), $_GET['tag'], $app);
} elseif (array_key_exists('linkfrom', $_GET)) {
    $app->bddinit($config);
    echo '<h4><a href="?id=' . $_GET['linkfrom'] . '">' . $_GET['linkfrom'] . '</a></h4>';
    $aff->linkfrom($app->getlister(['id', 'title', 'description', 'linkfrom']), $_GET['linkfrom'], $app);
} elseif (array_key_exists('aff', $_GET)) {
    include(__DIR__.'/controller/w.menu.php');
} else {
    include(__DIR__.'/controller/w.home.php');
}



?>