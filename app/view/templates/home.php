<?php

use Wcms\Config;

 $this->layout('layout', ['title' => 'home', 'stylesheets' => [$css . 'back.css', $css . 'home.css', $css . 'tagcolors.css'], 'favicon' => '']) ?>




<?php $this->start('page') ?>



<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'home', 'pagelist' => $pagelist]) ?>

<?php if ($user->iseditor()) { ?>



<?php
    $optlist = $optlist ?? null;
    $this->insert('homemenu', [
        'user' => $user,
        'opt' => $opt,
        'optlist' => $optlist,
        'optmap' => $optmap,
        'optrandom' => $optrandom,
        'pagelist' => $pagelist,
        'faviconlist' => $faviconlist,
        'thumbnaillist' => $thumbnaillist,
        'editorlist' => $editorlist,
        'colors' => $colors,
        'queryaddress' => $queryaddress,
        'matchedbookmarks' => $matchedbookmarks,
        'editablebookmarks' => $editablebookmarks
    ]);
?>



<main class="home">

    <?php $this->insert('homebookmark', ['publicbookmarks' => $publicbookmarks, 'personalbookmarks' => $personalbookmarks, 'queryaddress' => $queryaddress]) ?>


    <?php $this->insert('homeopt', ['opt' => $opt, 'user' => $user, 'display' => $display, 'workspace' => $workspace]) ?>

    <section class="pages">

        <div class="block">

            <h2>
                Pages (<?= count($pagelistopt) ?>)
                <?php if($opt->isfiltered()) { ?>
                    <i class="fa fa-filter" title="There are active filters"></i>
                <?php } ?>
                <?php if(!empty($deepsearch)) { ?>
                    <i class="fa fa-search" title="There is active search"></i>
                <?php } ?>
                <span class="right">
                    <a href="?display=list" <?= $display === 'list' ? 'class="selected"' : '' ?> ><i class="fa fa-th-list"></i></a>
                    <a href="?display=graph"  <?= $display === 'graph' ? 'class="selected"' : '' ?>  ><i class="fa fa-sitemap"></i></a>
                    <a href="?display=map" <?= $display === 'map' ? 'class="selected"' : '' ?> ><i class="fa fa-globe"></i></a>
                </span>
            </h2>

            <?php if($display === 'graph') { ?>

            <!-- ___________________ G R A P H _________________________ -->

            <div id="deepsearchbar">
                <form action="" method="get">
                    <input type="hidden" name="display" value="graph">
                    <input type="checkbox" name="showorphans" value="1" id="showorphans" <?= $showorphans ? 'checked' : '' ?>>
                    <label for="showorphans">show orphans pages</label>
                    <input type="checkbox" name="showredirection" value="1" id="showredirection" <?= $showredirection ? 'checked' : '' ?>>
                    <label for="showredirection">show redirections</label>
                    <select name="layout" id="layout">
                        <?= options(Wcms\Modelhome::GRAPH_LAYOUTS, $layout) ?>
                    </select>
                    <label for="layout">graph layout</label>
                    <input type="submit" value="update">
                </form>
            </div>

            <div id="graph"></div>

            <script>
                var data = <?= $json ?>;
                console.log(data);
            </script>

            <script src="<?= Wcms\Model::jspath() ?>graph.bundle.js" defer></script>

            <?php } elseif ($display === 'map') { ?>
                
            
            <!-- ___________________ M A P _________________________ -->

            <div id="map">

                <?= $mapcounter ?> pages have geo datas set.

                <div id="geomap"></div>

                <script>
                    var pages = <?= $json ?>;
                </script>

                <script src="<?= Wcms\Model::jspath() ?>leaflet.bundle.js" async></script>
                <script src="<?= Wcms\Model::jspath() ?>map.bundle.js" async></script>

            </div>
            
            <?php } else { ?>

            <!-- ___________________ D E E P _________________________ -->

            <div id="deepsearchbar">
                <form action="<?= $this->url('home') ?>" method="get">
                    <input type="text" name="search" value="<?= $deepsearch ?>" id="deepsearch" placeholder="deep search">
                    <input type="submit" value="search">
                    <details <?= empty($deepsearch) ? "" : "open" ?>>
                        <summary><i class="fa fa-cog"></i></summary>
                        <input type="checkbox" name="id" id="deepid" value="1" <?= $searchopt['id'] ? 'checked' : '' ?>>
                        <label for="deepid">id</label>
                        <input type="checkbox" name="title" id="deeptitle" value="1" <?= $searchopt['title'] ? 'checked' : '' ?>>
                        <label for="deeptitle">title</label>
                        <input type="checkbox" name="description" id="deepdescription" value="1"  <?= $searchopt['description'] ? 'checked' : '' ?>>
                        <label for="deepdescription">description</label>
                        <input type="checkbox" name="content" id="deepcontent" value="1"  <?= $searchopt['content'] ? 'checked' : '' ?>>
                        <label for="deepcontent" title="Markdown content : MAIN, HEADER, NAV, ASIDE, FOOTER">content</label>
                        <input type="checkbox" name="other" id="deepother" value="1"  <?= $searchopt['other'] ? 'checked' : '' ?>>
                        <label for="deepother" title="Structure content : BODY, CSS, Javascript">other</label>
                        <input type="checkbox" name="case" id="deepcase" value="1"  <?= $searchopt['casesensitive'] ? 'checked' : '' ?>>
                        <label for="deepcase" title="Case sensitive or not">case sensitive</label>
                    </details>
                </form>
            </div>


            <!-- ___________________ T A B L E _______________________ -->


            <div class="scroll">

                <table id="home2table">
                    <thead>
                        <tr>
                            <?php if($user->issupereditor()) { ?><th id="checkall">x</th> <?php } ?>
                            <?php if($columns['favicon']) { ?>
                                <th class="favicon">
                                    <a href="<?= $opt->sortbyorder('favicon') ?>">ico</a>
                                    <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'favicon']) ?>
                                </th>
                            <?php } ?>
                            <th class="id">
                                <a href="<?= $opt->sortbyorder('id') ?>">id</a>
                                <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'id']) ?>
                            </th>
                            <th class="edit"></th>
                            <th class="read"></th>
                            <th class="delete" title="delete page"></th>
                            <th class="download" title="download page as json"></th>
                            <?php if ($columns['tag']) { ?>
                            <th class="tag">
                                <a href="<?= $opt->sortbyorder('tag') ?>">tag</a>
                                <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'tag']) ?>
                            </th>
                            <?php } if ($columns['title']) { ?>
                            <th class="title">
                                <a href="<?= $opt->sortbyorder('title') ?>">title</a>
                                <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'title']) ?>
                            </th>
                            <?php }
                                if ($columns['description']) { ?>
                            <th class="description">description</th>
                            <?php }
                                if ($columns['linkto']) { ?>
                            <th class="linkto">
                                <a href="<?= $opt->sortbyorder('linkto') ?>">linkto</a>
                                <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'linkto']) ?>
                                
                            </th>
                            <?php } if ($columns['geolocalisation']) { ?>
                                <th class="geo">
                                    <i class="fa fa-globe"></i>
                                </th>                                
                            <?php }
                                if ($columns['datemodif']) { ?>
                            <th class="datemodif">
                                <a href="<?= $opt->sortbyorder('datemodif') ?>">last modification</a>
                                <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'datemodif']) ?>
                            </th>
                            <?php }
                                if ($columns['datecreation']) { ?>
                            <th class="datecreation">
                                <a href="<?= $opt->sortbyorder('datecreation') ?>">date of creation</a>
                                <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'datecreation']) ?>
                            </th>
                            <?php }
                                if ($columns['date']) { ?>
                            <th class="date">
                                <a href="<?= $opt->sortbyorder('date') ?>">date</a>
                                <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'date']) ?>
                            </th>
                            <?php }
                                if ($columns['secure']) { ?>
                            <th class="secure">
                                <a href="<?= $opt->sortbyorder('secure') ?>">privacy</a>
                                <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'secure']) ?>
                            </th>
                            <?php }
                                if ($columns['authors']) { ?>
                                    <th class="authors">
                                        <a href="<?= $opt->sortbyorder('authors') ?>">authors</a>
                                <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'authors']) ?>
                                    </th>
                                    <?php }
                                if ($columns['visitcount']) { ?>
                            <th class="visitcount">
                                <a href="<?= $opt->sortbyorder('visitcount') ?>">visit</a>
                                <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'visitcount']) ?>
                            </th>
                            <?php }
                                if ($columns['editcount']) { ?>
                            <th class="editcount">
                                <a href="<?= $opt->sortbyorder('editcount') ?>">edit</a>
                                <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'editcount']) ?>
                            </th>
                            <?php }
                                if ($columns['displaycount']) { ?>
                            <th class="displaycount">
                                <a href="<?= $opt->sortbyorder('displaycount') ?>">display</a>
                                <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'displaycount']) ?>
                            </th>
                            <?php } if ($columns['version']) { ?>
                                <th class="displaycount">
                                    <a href="<?= $opt->sortbyorder('version') ?>">version</a>
                                    <?= $this->insert('macro_tablesort', ['opt' => $opt, 'th' => 'version']) ?>
                                </th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagelistopt as $item) { ?>
                        <tr>
                            <?php if($user->issupereditor()) { ?><td><input type="checkbox" name="pagesid[]" value="<?= $item->id() ?>" id="id_<?= $item->id() ?>" form="multi"></td><?php } ?>
                            <?php if($columns['favicon']) { ?>
                                <td class="favicon">
                                    <?php if(!empty($item->favicon())) { ?>
                                    <picture>
                                        <source srcset="<?= Wcms\Model::faviconpath() . $item->favicon() ?>" />
                                        <img alt="" title="<?= $item->favicon() ?>" >
                                    </picture>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                            <td class="id">
                                <label title="<?= $item->title() ?>" for="id_<?= $item->id() ?>">
                                    <?= $item->id() ?>
                                </label>
                                <?php if (!empty($item->redirection())) { ?>
                                    <span class="redirection">
                                        <a
                                            href="<?= \Wcms\Model::idcheck($item->redirection()) ? $this->upage('pageread', $item->redirection()) : getfirsturl($item->redirection()) ?>"
                                            title="This page redirect to: <?= $item->redirection() ?>"
                                        >
                                            <i class="fa fa-external-link-square"></i>
                                            <span class="refresh">
                                                <?= $item->refresh() !== 0 ? $item->refresh() . 's' : '' ?>
                                            </span>
                                        </a>
                                    </span>
                                <?php } ?>
                            </td>
                            <td class="edit">
                                <?php if($this->caneditpage($item)) { ?>
                                    <a href="<?= $this->upage('pageedit', $item->id()) ?>">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <?php } ?>
                            </td>
                            <td class="read">
                                <a href="<?= $this->upage('pageread', $item->id()) ?>">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                            <td class="delete">
                                <?php if($this->candeletepage($item)) { ?>
                                    <a href="<?= $this->upage('pagedelete', $item->id()) ?>">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                <?php } ?>
                            </td>
                            <td class="download">
                                <?php if ($this->caneditpage($item)) { ?>
                                <a href="<?= $this->upage('pagedownload', $item->id()) ?>" download>
                                    <i class="fa fa-download"></i>
                                </a>
                                <?php } ?>
                            </td>
                            <?php if ($columns['tag']) { ?>
                            <td class="tag"><?= $opt->taglinks($item->tag('array')) ?></td>
                            <?php }
                                    if ($columns['title']) { ?>
                            <td class="title" title="<?= $item->title() ?>"><label for="id_<?= $item->id() ?>"><?= $item->title() ?></label></td>
                            <?php }
                                    if ($columns['description']) { ?>
                            <td class="description" title="<?= $item->description() ?>"><?= $item->description('short') ?></td>
                            <?php }
                                    if ($columns['linkto']) { ?>
                            <td class="linkto"><?= $opt->linktolink($item->linkto('array')) ?></td>
                            <?php }
                                    if ($columns['geolocalisation']) { ?>
                            <td class="geo"><?= $item->isgeo() ? '<i class="fa fa-globe"></i>' : '' ?></td>
                            <?php }
                                    if ($columns['datemodif']) { ?>
                            <td class="datemodif" title="<?= $item->datemodif('dmy') ?> <?= $item->datemodif('ptime') ?>">
                                <time datetime="<?= $item->datemodif('string') ?>" title="<?= $item->datemodif('dmy') . ' ' . $item->datemodif('ptime') ?>">
                                    <?= $item->datemodif('hrdi') ?>
                                </time>
                            </td>
                            <?php }
                                    if ($columns['datecreation']) { ?>
                            <td class="datecreation" title="<?= $item->datecreation('dmy') ?> <?= $item->datecreation('ptime') ?>">
                                <time datetime="<?= $item->datecreation('string') ?>" title="<?= $item->datecreation('dmy') . ' ' . $item->datecreation('ptime') ?>">
                                    <?= $item->datecreation('hrdi') ?>
                                </time>
                            </td>
                            <?php }
                                    if ($columns['date']) { ?>
                            <td class="date" title="<?= $item->date('dmy') ?> <?= $item->date('ptime') ?>">
                                <time datetime="<?= $item->date('string') ?>" title="<?= $item->date('dmy') . ' ' . $item->date('ptime') ?>">
                                    <?= $item->date('dmy') ?>
                                </time>
                        </td>
                            <?php }
                                    if ($columns['secure']) { ?>
                            <td class="secure">
                                <?= $opt->securelink($item->secure('int') , $item->secure('string')) ?>
                                <?= !empty($item->password()) ? '<i class="fa fa-lock" title="This page is password protected"></i>' : '' ?>
                            </td>
                            <?php }
                                    if ($columns['authors']) { ?>
                            <td class="authors"><?= $opt->authorlinks($item->authors('array')) ?></td>
                            <?php }
                                    if ($columns['visitcount']) { ?>
                            <td class="visitcount"><?= $item->visitcount() ?></td>
                            <?php }
                                    if ($columns['editcount']) { ?>
                            <td class="editcount"><?= $item->editcount() ?></td>
                            <?php }
                                    if ($columns['displaycount']) { ?>
                            <td class="displaycount"><?= $item->displaycount() ?></td>
                            <?php } if ($columns['version']) { ?>
                                <td class="version"><?= $item->version() ?></td>                                
                            <?php } ?>
                        </tr>

                        <?php  } ?>
                    </tbody>
                </table>
            </div>

            <?php } ?>

        </div>

    </section>

</main>

<?php $this->insert('footer', ['footer' => $footer]) ?>


<?php if(!Wcms\Config::disablejavascript()) { ?>

<script src="<?= Wcms\Model::jspath() ?>home.bundle.js"></script>

<?php } ?>


<?php } ?>


<?php $this->stop() ?>
