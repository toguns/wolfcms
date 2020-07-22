
<?php if (Dispatcher::getAction() == 'index'): ?>

    <p class="button"><a href="<?= getUrl('layout/add'); ?>"><img src="<?= ASSETS_PATH; ?>admin/images/layout.png" align="middle" alt="layout icon" /> <?= __('New Layout'); ?></a></p>

    <div class="box">
        <h2><?= __('What is a Layout?'); ?></h2>
        <p><?= __('Use layouts to apply a visual look to a Web page. Layouts can contain special tags to include page content and other elements such as the header or footer. Click on a layout name below to edit it or click <strong>Remove</strong> to delete it.'); ?></p>
    </div>

<?php endif; ?>