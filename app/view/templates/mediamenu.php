<nav id="navbar" class="hbar">

    <div class="hbar-section">
        <details class="dropdown">
            <summary>File</summary>
            <div class="dropdown-content">
                <form id=addmedia action="<?= $this->url('mediaupload') ?>" method="post" enctype="multipart/form-data" class="dropdown-section">
                    <h2>
                        Upload file(s)
                        <a href="<?= $this->url('info', [], '#media-upload') ?>" title="help !" class="help">?</a>
                    </h2>
                    <p class="field">
                        <label for="file">
                            <i class="fa fa-upload"></i> Upload from computer<br>
                            max upload file size : <?= $maxuploadsize ?>
                        </label>
                        <input type="hidden" name="route" value="<?= $mediaopt->getpathaddress() ?>">
                        <input type='file' id="file" name='file[]' multiple required>
                    </p>
                    <p class="field">
                        <label for="idclean">Clean filenames</label>
                        <input type="hidden" name="idclean" value="0">
                        <input type="checkbox" name="idclean" id="idclean" value="1" checked>
                    </p>                                
                    <?php if ($optimizeimage) : ?>
                        <p class="field">
                            <input type="hidden" name="convertimages" value="0">
                            <label for="convertimages">Optimize images for the Web</label>
                            <input type="checkbox" name="convertimages" id="convertimages" value="1" checked>                    
                        </p>
                    <?php endif ?>                    
                    <p class="field submit-field">
                        <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>">
                        <input type="submit" value="upload">
                    </p>
                </form>

                <form id="addurlmedia" action="<?= $this->url('mediaurlupload') ?>" method="post" class="dropdown-section">
                    <p class="field">
                        <input type="hidden" name="route" value="<?= $mediaopt->getpathaddress() ?>">
                        <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>">    
                        <label for="url"><i class="fa fa-cloud-upload"></i> Upload from URL</label>
                        <input type="text" name="url" id="url" placeholder="paste url here">                    
                    </p>
                    <p class="field submit-field">
                        <input type="submit" value="upload">
                    </p>
                </form>

                <form id="folderadd" action="<?= $this->url('mediafolderadd') ?>" method="post" class="dropdown-section">
                    <h2>New folder</h2>                
                    <p class="field">
                        <input type="hidden" name="route" value="<?= $mediaopt->getpathaddress() ?>">
                        <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>">
                        <label for="foldername"><i class="fa fa-folder"></i>  New folder</label>
                        <input type="text" name="foldername" id="foldername" placeholder="folder name" required>
                    </p>
                    <p class="field submit-field">
                        <input type="submit" value="create folder">
                    </p>
                </form>

                <?php if($user->issupereditor()) : ?>
                    <form action="<?= $this->url('mediafolderdelete') ?>" id="deletefolder" method="post" class="dropdown-section">
                        <h2>Delete folder</h2>
                        <p class="field">
                            <input type="hidden" name="route" value="<?= $mediaopt->getpathaddress('media') ?>">
                            <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>/">
                            <label for="confirmdeletefolder">Delete current folder and all it's content</label>
                            <input type="checkbox" name="deletefolder" id="confirmdeletefolder" value="1">
                        </p>
                        <p class="field submit-field">
                            <input type="submit" value="delete folder" >
                        </p>
                    </form>
                <?php endif ?>

                <div class="dropdown-section">
                    <h2>Magic folders</h2>
                    <label><i class="fa fa-font"></i> Fonts</label>
                    <p class="field submit-field">
                        <a href="<?= $this->url('mediafontface', [], $mediaopt->getpathaddress()) ?>" class="button">
                            <i class="fa fa-refresh"></i> Regenerate @fontface CSS file
                        </a>
                    </p>
                </div>
            </div>
        </details>


        <details class="dropdown">
            <summary>Edit</summary>
            <?php if($user->issupereditor()) : ?>                
                <form action="<?= $this->url('mediaedit') ?>" method="post" id="mediaedit" class="dropdown-content">
                    <div class="dropdown-section">
                        <h2>Move</h2>
                        <p class="field">
                            <input type="hidden" name="route" value="<?= $mediaopt->getpathaddress() ?>">
                            <input type="hidden" name="path" value="<?= $mediaopt->dir() ?>">
                            <label for="moveto">Move selected medias to a new directory</label>
                            <select name="dir" id="moveto" >
                                <option selected>---select destination---</option>
                                <option value="<?= Wcms\Model::MEDIA_DIR ?>">/</option>
                                <?php foreach ($pathlist as $path) : ?>
                                    <option value="<?= Wcms\Model::MEDIA_DIR . $path ?>"><?= $path ?></option>
                                <?php endforeach ?>
                            </select>
                        </p>
                        <p class="field submit-field">
                            <input type="submit" name="action" value="move" >
                        </p>
                    </div>    
                    <div class="dropdown-section">
                        <h2>Delete</h2>
                        <p class="field submit">
                            <input type="submit" name="action" value="Delete selected medias" >
                        </p>
                    </div>
                </form>
            <?php endif ?>
        </details>


        <details <?= $filtercode ? "open" : " " ?> class="dropdown">
            <summary>Filter</summary>
            <div class="dropdown-content">
                <form action="" method="post" class="dropdown-section">
                    <h2>Print folder content</h2>
                    <input type="hidden" name="query" value="1">
                    <input type="hidden" name="filename" value="0">
                    <p class="field">
                        <label for="filename">add filenames under images, sounds and videos</label>
                        <input type="checkbox" name="filename" id="filename" value="1" <?= $mediaopt->filename() ? "checked" : "" ?> >
                    </p>
                    <p class="field submit-field">
                        <input type="submit" value="generate">
                    </p>
                    <p class="field">
                        <label>Use this code to print the content of the current folder in a page</label>
                        <code class="select-all" ><?= $mediaopt->getcode() ?></code>
                    </p>
                </form>
            </div>
        </details>

    </div>

</nav>
