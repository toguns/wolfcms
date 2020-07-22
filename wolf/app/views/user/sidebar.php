<?php
if (Dispatcher::getAction() == 'index'):
    ?>

    <p class="button"><a href="<?= getUrl('user/add'); ?>"><img src="<?= PATH_PUBLIC; ?>assets/admin/images/user.png" align="middle" alt="user icon" /> <?= __('New User'); ?></a></p>

    <div class="box">
        <h2><?= __('Where do the avatars come from?'); ?></h2>
        <p><?= __('The avatars are automatically linked for those with a <a href="http://www.gravatar.com/" target="_blank">Gravatar</a> (a free service) account.'); ?></p>
    </div>

<?php endif; ?>