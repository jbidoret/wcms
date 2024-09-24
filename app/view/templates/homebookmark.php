

<div class="block panel-section">
    <h1>Bookmarks</h1>
    
    <div id="bookmarks" class="scroll">
        
        <h3>Public</h3>
        <ul class="linkslist">
        <?php foreach ($publicbookmarks as $bookmark) { ?>
            <li class="flexrow">
                <a
                    href="<?= $this->url("home", $bookmark->params(), $bookmark->query()) ?>&display=<?= $display ?>"
                    data-current="<?= isset($queryaddress) && $bookmark->query() === $queryaddress ? '1' : '0' ?>"
                    class="bookmark"
                    title="<?= $bookmark->description() ?>"
                >
                    <span class="icon">
                        <?= $bookmark->icon() ?>
                    </span>
                    <span class="name">
                        <?= empty($bookmark->name()) ? $bookmark->id() : $bookmark->name() ?>
                    </span>
                </a>
                <?php if($bookmark->ispublished()){ ?>
                    <a href="<?= Wcms\Servicerss::atomfile($bookmark->id()) ?>" target="_blank" title="show Atom XML file" class="rss">
                        <i class="fa fa-rss"></i>
                    </a>
                <?php } ?>
            </li>
        <?php } ?>
                </ul>
        <h3>Personal</h3>
        
        <?php foreach ($personalbookmarks as $bookmark) { ?>
            <div>
                <a
                    href="<?= $this->url("home", $bookmark->params(), $bookmark->query()) ?>&display=<?= $display ?>"
                    data-current="<?= isset($queryaddress) && $bookmark->query() === $queryaddress ? '1' : '0' ?>"
                    class="bookmark"
                    title="<?= $bookmark->description() ?>"
                >
                    <?= $bookmark->icon() ?>
                    <span>
                        <?= empty($bookmark->name()) ? $bookmark->id() : $bookmark->name() ?>
                    </span>
                </a>
            </div>
        <?php } ?>
    </div>
</div>
