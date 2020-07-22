<?php
if (Dispatcher::getAction() == 'index'):
    ?>

    <?php if (AuthUser::hasPermission('snippet_add')): ?>
        <p class="button"><a href="<?= getUrl('snippet/add'); ?>"><img src="<?= PATH_PUBLIC; ?>assets/admin/images/snippet.png" align="middle" alt="snippet icon" /> <?= __('New Snippet'); ?></a></p>
    <?php endif; ?>

    <div class="box">
        <h2><?= __('What is a Snippet?'); ?></h2>
        <p><?= __('Snippets are generally small pieces of content which are included in other pages or layouts.'); ?></p>
    </div>
    <div class="box">
        <h2><?= __('Tag to use this snippet'); ?></h2>
        <p><?= __('Just replace <b>snippet</b> by the snippet name you want to include.'); ?></p>
        <p><code>&lt;?php $this->includeSnippet('snippet'); ?&gt;</code></p>
    </div>

<?php endif; ?>