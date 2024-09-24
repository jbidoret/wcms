<nav class="media navbar dropdown">


    <details>
        <summary>File</summary>
            <div class="submenu">
                
                <form id=addmedia action="<?= $this->url('mediaupload') ?>" method="post" enctype="multipart/form-data"  class="submenu-section">
                    <h2>Upload file(s)</h2>
                    <p class="field">
                        <label for="file">
                            <i class="fa fa-upload"></i> Upload from computer<br>
                            max upload file size : <?= $maxuploadsize ?>
                        </label>
                        
                        <input type='file' id="file" name='file[]' multiple required>
                    </p>
                    <p class="field">
                        <label for="idclean">clean filenames</label>
                        <input type="hidden" name="idclean" value="0">
                        <input type="checkbox" name="idclean" id="idclean" value="1" checked>
                    </p>
                    <p class="field submit">
                        <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>">
                        <input type="submit" value="upload">
                    </p>
                </form>

                <form id="addurlmedia" action="<?= $this->url('mediaurlupload') ?>" method="post"   class="submenu-section">

                    <p class="field">
                        <label for="url"><i class="fa fa-cloud-upload"></i> Upload from URL</label>                
                        <input type="text" name="url" id="url" placeholder="paste url here">
                        <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>">
                    </p>
                    <p class="field submit">
                        <input type="submit" value="upload">
                    </p>
                </form>

                <form id="folderadd" action="<?= $this->url('mediafolderadd') ?>" method="post" class="submenu-section">
                    <h2>New folder</h2>
                    <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>">
                    <p class="field">
                        <label for="foldername"><i class="fa fa-folder"></i>  New folder</label>
                        <input type="text" name="foldername" id="foldername" placeholder="folder name" required>
                    </p>
                    <p class="field submit">
                        <input type="submit" value="create folder">
                    </p>
                </form>


                <?php if($user->issupereditor()) { ?>
                <form action="<?= $this->url('mediafolderdelete') ?>" id="deletefolder" method="post" class="submenu-section">
                    <h2>Delete folder</h2>
                    <p class="field">
                        <label for="confirmdeletefolder">Delete current folder and all it's content</label>
                        <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>/">
                        <input type="checkbox" name="deletefolder" id="confirmdeletefolder" value="1">
                    </p>
                    <p class="field submit">                    
                        <input type="submit" value="delete folder" >
                    </p>
                </form>
                <?php } ?>
                <div class="submenu-section">
                    <h2>Magic folders</h2>
                    <p class="field">
                        <label><i class="fa fa-font"></i> Fonts</label>
                        <a href="<?= $this->url('mediafontface', [], $mediaopt->getpathadress()) ?>" class="button">
                            <i class="fa fa-refresh"></i>regenerate @fontface CSS file
                        </a>
                    </p>
                </div>
            </div>
    </details>


    <details>
        <summary>Edit</summary>

        <?php if($user->issupereditor()) { ?>
        
        <form action="<?= $this->url('mediaedit') ?>" method="post" id="mediaedit" class="submenu">
            <div class="submenu-section">
                <h2>Move</h2>
                <p class="field">
                    <input type="hidden" name="route" value="<?= $mediaopt->getaddress() ?>">
                    <input type="hidden" name="path" value="<?= $mediaopt->dir() ?>">
                    <label for="moveto">Move selected medias to a new directory</label>
                    
                    <select name="dir" id="moveto" >
                        <option selected>---select destination---</option>
                        <option value="<?= Wcms\Model::MEDIA_DIR ?>">/</option>
                        <?php
                            foreach ($pathlist as $path) {
                                echo '<option value="' . Wcms\Model::MEDIA_DIR . $path . '">' . $path . '</option>';
                            }
                            ?>
                    </select>
                </p>
                <p class="field submit">
                    <input type="submit" name="action" value="move" >
                </p>
            </div>
            <div class="submenu-section">
                <h2>Delete</h2>
                <p class="field submit">
                    <input type="submit" name="action" value="Delete selected medias" >
                </p>
            </div>
        </form>

        <?php } ?>
    </details>


    <details<?= $filtercode ? " open" : " " ?>>
        <summary>Filter</summary>
        <form action="" method="post" class="submenu">
            <div class="submenu-section">
                <h2>Print folder content</h2>
                
                <input type="hidden" name="query" value="1">
                <input type="hidden" name="filename" value="0">
                <p class="field">
                    <label for="filename">add filenames under images, sounds and videos</label>
                    <input type="checkbox" name="filename" id="filename" value="1" <?= $mediaopt->filename() ? "checked" : "" ?> >
                </p>
                <p class="field submit">
                    <input type="submit" value="generate">
                </p>

                <p class="field">
                    <label for="">Use this code to print the content of the current folder in a page</label>
                    <code class="select-all" ><?= $mediaopt->getcode() ?></code>
                </p>
            </div>
        </form>
    </details>


</nav>
