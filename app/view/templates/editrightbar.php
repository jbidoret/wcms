<div id="rightbar"  class="collapsible panel">
    <input
        id="showeditorrightpanel"
        name="showeditorrightpanel"
        value="1"
        class="toggle"
        type="checkbox"
        form="workspace-form"
        <?= $workspace->showeditorrightpanel() === true ? 'checked' : '' ?>
    >
    <label for="showeditorrightpanel" class="toggle-label"><span>◧</span></label>
    <div id="rightbarpanel"  class="collapsible-content panel-section scroll" >
   
        <h1>Infos</h1>
        <?php if ($user->iseditor()) { ?>
            
            <h3>Authors</h3>
            <?php foreach ($editorlist as $editor) { ?>
                <div class="checkexternal">
                <input
                    type="checkbox"
                    name="authors[]"
                    id="<?= $editor->id() ?>"
                    value="<?= $editor->id() ?>"
                    form="update"
                    <?= in_array($editor->id(), $page->authors()) ? 'checked' : '' ?>

                    <?php /* safeguard against editor removing themself from authors too easily */ ?>
                    <?= !$user->issupereditor() && $editor->id() === $user->id() ? 'disabled=""' : '' ?>
                >
                <label for="<?= $editor->id() ?>" ><?= $editor->id() ?> <?= $editor->level() ?></label>
                </div>
            <?php } ?>

        <?php } ?>



        <h3>Stats</h3>

        <p>
            Edits:<br>
            <?= $page->editcount() ?>
        </p>
        <p>
            Displays:<br>
            <?= $page->displaycount() ?>
        </p>
        <p>
            Visits:<br>
            <?= $page->visitcount() ?>
        </p>
            

    </div>

</div>
