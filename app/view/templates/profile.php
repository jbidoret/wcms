<?php

use Wcms\Model;

$this->layout('layout', ['title' => 'profile', 'stylesheets' => [$css . 'back.css', $css . 'profile.css']]) ?>


<?php $this->start('page') ?>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'profile', 'pagelist' => $pagelist]) ?>


<main class="profile">
    <div class="panel">
        <div class="block panel-section">
            <h1><span><i class="fa fa-user"></i> Preferences</span></h1>
            <div class="scroll">
            <h2>Change some infos about you.</h2>
                <form action="<?= $this->url('profileupdate') ?>" method="post" id="preferences" class="pref-panel">
                    
                    <p class="field">
                        <label for="name">Display name (if none, user ID will be used)</label>
                        <input type="text" name="name" id="name" value="<?= $user->name() ?>" placeholder="<?= $user->id() ?> " maxlength="<?= Wcms\Item::LENGTH_SHORT_TEXT ?>" >
                    </p>
                    <p class="field">
                        <label for="url">associated url (can be a page ID)</label>
                        <input type="text" name="url" id="url" value="<?= $user->url() ?>" list="searchdatalist" placeholder="URL or page ID" maxlength="<?= Wcms\Item::LENGTH_SHORT_TEXT ?>" >
                    </p>
                    <p>When you tick the <em>remember-me</em> checkbox during login, you can choose how much time <strong>W</strong> will remember you.</p>
                    <p class="field">
                        <label for="cookie">Cookie conservation time <i>(In days)</i></label>
                        <input type="number" name="cookie" value="<?= $user->cookie() ?>" id="cookie" min="0" max="<?= Model::MAX_COOKIE_CONSERVATION ?>" >
                    </p>
                    <p class="field submit">                  
                        <input type="submit" value="update preferences">
                    </p>                
                </form>
            </div>
        </div>
    </div>
    
    <div class="panel">
        <div class="block panel-section">
            <h1><span><i class="fa fa-key"></i> Password</span></h1>

            <div class="scroll">
                <h2>Change your password</h2>
                <form action="<?= $this->url('profilepassword') ?>" method="post" id="password" class="pref-panel">
                    <p>Password have to be between <?= Wcms\Model::PASSWORD_MIN_LENGTH ?> and <?= Wcms\Model::PASSWORD_MAX_LENGTH ?> characters long.</p>    
                    <p class="field">
                        <label for="currentpassword">Actual password</label>
                        <input type="password" name="currentpassword" id="currentpassword" required>
                    </p>
                    <p class="field">
                        <label for="password1">New password</label>
                        <input type="password" name="password1" id="password1" minlength="<?= Wcms\Model::PASSWORD_MIN_LENGTH ?>" required>
                    </p>
                    <p class="field">
                        <label for="password2">Confirm new password</label>
                        <input type="password" name="password2" id="password2" minlength="<?= Wcms\Model::PASSWORD_MIN_LENGTH ?>" required>
                    </p>
                    <p class="field submit">
                        <input type="submit" value="update password">
                    </p>
                </form>
            </div>
        </div>
    </div>

    <section id="profile" class="panel">
        <div class="block">

            
            <h1><span>Info</span></h1>
            
            <div class="scroll">
                
                <table>
                    <thead>
                        <tr>
                            <th>Stat</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>id</td>
                            <td><?= $user->id() ?></td>
                        </tr>
                        <tr>
                            <td>connection counter</td>
                            <td><?= $user->connectcount() ?></td>
                        </tr>
                        <tr>
                            <td>account expirations</td>
                            <td><?= $user->expiredate('hrdi') ?></td>
                        </tr>
                    </tbody>
                </table>

                

                

            </div>


        </div>

    </section>

    

</main>

<?php $this->stop('page') ?>
