<?php $this->layout('layout', ['title' => 'home', 'stylesheets' => [$css . 'home.css', $css . 'tagcolors.css'], 'favicon' => '']) ?>




<?php $this->start('page') ?>


<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'home', 'pagelist' => $pagelist]) ?>

    <?php if ($user->iseditor()) { ?>



    <?php
        $optlist = $optlist ?? null;
        $this->insert('homemenu', ['user' => $user, 'opt' => $opt, 'optlist' => $optlist, 'pagelist' => $pagelist, 'faviconlist' => $faviconlist, 'thumbnaillist' => $thumbnaillist, 'editorlist' => $editorlist, 'colors' => $colors]);
    ?>

    <div id="deepsearchbar" class="hidephone">
        <form action="<?= $this->url('home') ?>" method="get">
            <input type="text" name="search" value="<?= $deepsearch ?>" id="search" placeholder="deep search">
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
            <input type="submit" value="search">
        </form>
    </div>


    <main class="home">


        <?php $this->insert('homeopt', ['opt' => $opt, 'user' => $user]) ?>

        <section class="pages">

            <div class="block">

                <h2 class="hidephone">Pages (<?= count($table2) ?>)</h2>

                <div class="scroll">

                    <table id="home2table">
                        <thead>
                            <tr>
                                <?php if($user->issupereditor()) { ?><th id="checkall" class="hidephone">x</th> <?php } ?>
                                <?php if($columns['favicon']) { ?>
                                    <th class="favicon"><a href="<?= $opt->sortbyorder('favicon') ?>">ico</a></th>
                                <?php } ?>
                                <th class="id"><a href="<?= $opt->sortbyorder('id') ?>">id</a></th>
                                <th>edit</th>
                                <th>see</th>
                                <th class="delete" title="delete page">del</th>
                                <?php if ($user->issupereditor()) { ?>
                                <th class="download hidephone" title="download page as json">dl</th>
                                <?php }
                                    if ($columns['tag']) { ?>
                                <th class="tag"><a href="<?= $opt->sortbyorder('tag') ?>">tag</a></th>
                                <?php }
                                    if ($columns['title']) { ?>
                                <th class="title"><a href="<?= $opt->sortbyorder('title') ?>">title</a></th>
                                <?php }
                                    if ($columns['description']) { ?>
                                <th class="summary">summary</th>
                                <?php }
                                    if ($columns['linkto']) { ?>
                                <th class="linkto"><a href="<?= $opt->sortbyorder('linkto') ?>">to</a></th>
                                <?php }
                                    if ($columns['linkfrom']) { ?>
                                <th class="linkfrom"><a href="<?= $opt->sortbyorder('linkfrom') ?>">from</a></th>
                                <?php }
                                    if ($columns['datemodif']) { ?>
                                <th class="datemodif"><a href="<?= $opt->sortbyorder('datemodif') ?>">last modification</a></th>
                                <?php }
                                    if ($columns['datecreation']) { ?>
                                <th class="datecreation"><a href="<?= $opt->sortbyorder('datecreation') ?>">date of creation</a></th>
                                <?php }
                                    if ($columns['date']) { ?>
                                <th class="date"><a href="<?= $opt->sortbyorder('date') ?>">date</a></th>
                                <?php }
                                    if ($columns['secure']) { ?>
                                <th class="secure"><a href="<?= $opt->sortbyorder('secure') ?>">privacy</a></th>
                                <?php }
                                    if ($columns['authors']) { ?>
                                        <th class="authors"><a href="<?= $opt->sortbyorder('authors') ?>">authors</a></th>
                                        <?php }
                                    if ($columns['visitcount']) { ?>
                                <th class="visitcount"><a href="<?= $opt->sortbyorder('visitcount') ?>">visit</a></th>
                                <?php }
                                    if ($columns['editcount']) { ?>
                                <th class="editcount"><a href="<?= $opt->sortbyorder('editcount') ?>">edit</a></th>
                                <?php }
                                    if ($columns['affcount']) { ?>
                                <th class="affcount"><a href="<?= $opt->sortbyorder('affcount') ?>">aff</a></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($table2 as $item) { ?>
                            <tr>
                                <?php if($user->issupereditor()) { ?><td class="hidephone"><input type="checkbox" name="pagesid[]" value="<?= $item->id() ?>" id="id_<?= $item->id() ?>" form="multi"></td><?php } ?>
                                <?php if($columns['favicon']) { ?>
                                    <td class="favicon"><img class="favicon" src="<?= Wcms\Model::faviconpath() . $item->favicon() ?>" alt="<?= $item->favicon() ?>" title="<?= $item->favicon() ?>"></td>
                                <?php } ?>
                                <td class="id"><label title="<?= $item->title() ?>" for="id_<?= $item->id() ?>"><?= $item->id() ?></label></td>
                                <td><?php if($user->issupereditor() || in_array($user->id(), $item->authors())) { ?><a href="<?= $this->upage('pageedit', $item->id()) ?>"><img src="<?= Wcms\Model::iconpath() ?>edit.png" class="icon"></a><?php } ?></td>
                                <td><a href="<?= $this->upage('pageread/', $item->id()) ?>" target="_blank"><img src="<?= Wcms\Model::iconpath() ?>read.png" class="icon"></a></td>
                            <td class="delete"><?php if($user->issupereditor() || $item->authors() === [$user->id()]) { ?><a href="<?= $this->upage('pagedelete', $item->id()) ?>">✖</a><?php } ?></td>
                                <?php if ($user->issupereditor()) { ?>
                                <td class="hidephone"><a href="<?= $this->upage('pagedownload', $item->id()) ?>" download><img src="<?= Wcms\Model::iconpath() ?>download.png" class="icon"></a></td>
                                <?php }
                                        if ($columns['tag']) { ?>
                                <td class="tag"><?= $opt->taglinks($item->tag('array')) ?></td>
                                <?php }
                                        if ($columns['title']) { ?>
                                <td class="title" title="<?= $item->title() ?>"><label for="id_<?= $item->id() ?>"><?= $item->title() ?></label></td>
                                <?php }
                                        if ($columns['description']) { ?>
                                <td class="summary" title="<?= $item->description() ?>"><?= $item->description('short') ?></td>
                                <?php }
                                        if ($columns['linkto']) { ?>
                                <td class="linkto"><a title="<?= $item->linkto('string') ?>"><?= $item->linkto('sort') ?></a></td>
                                <?php }
                                        if ($columns['linkfrom']) { ?>
                                <td class="linkfrom"><?= $opt->linktolink($item->linkfrom('array')) ?></td>
                                <?php }
                                        if ($columns['datemodif']) { ?>
                                <td class="datemodif"><time datetime="<?= $item->datemodif('string') ?>" title="<?= $item->datemodif('dmy') . ' ' . $item->datemodif('ptime') ?>"><?= $item->datemodif('hrdi') ?></time></td>
                                <?php }
                                        if ($columns['datecreation']) { ?>
                                <td class="datecreation"><time datetime="<?= $item->datecreation('string') ?>" title="<?= $item->datecreation('dmy') . ' ' . $item->datecreation('ptime') ?>"><?= $item->datecreation('hrdi') ?></time></td>
                                <?php }
                                        if ($columns['date']) { ?>
                                <td class="date"><time datetime="<?= $item->date('string') ?>" title="<?= $item->date('dmy') . ' ' . $item->date('ptime') ?>"><?= $item->date('dmy') ?></time></td>
                                <?php }
                                        if ($columns['secure']) { ?>
                                <td class="secure"><?= $opt->securelink($item->secure('int') , $item->secure('string')) ?></td>
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
                                        if ($columns['affcount']) { ?>
                                <td class="affcount"><?= $item->affcount() ?></td>
                                <?php } ?>
                            </tr>

                            <?php  } ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </section>


        <?php if($user->display()['bookmark'] && (!empty(Wcms\Config::bookmark()) || !empty($user->bookmark()))) { ?>

        <section class="hidephone" id="bookmark">
            <div class="block">
                <h2>Bookmarks</h2>
                <div class="scroll">
                    <strong>Public</strong>
                    <ul>
                        <?php foreach (Wcms\Config::bookmark() as $id => $query) { ?>
                            <li>
                                <a href="<?= $query ?>"><?= $id ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                    <strong><?= $user->id() ?></strong>
                    <ul>
                        <?php foreach ($user->bookmark() as $id => $query) { ?>
                            <li>
                                <a href="<?= $query ?>"><?= $id ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </section>
            </div>
        </nav>

        <?php } ?>

    </main>

    <?php $this->insert('footer', ['footer' => $footer]) ?>

    <script src="<?= Wcms\Model::jspath() ?>home.bundle.js"></script>

    <?php } ?>

</body>



<?php $this->stop() ?>