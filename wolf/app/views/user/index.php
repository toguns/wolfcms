<?php
use_helper('Gravatar');
?>
<h1><?= __('Users'); ?></h1>
<table id="users" class="index" cellpadding="0" cellspacing="0" border="0">
    <thead>
        <tr>
            <th><?= __('Name'); ?> / <?= __('Username'); ?></th>
            <th><?= __('Email'); ?></th>
            <th><?= __('Roles'); ?></th>
            <th><?= __('Modify'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?> 
            <tr class="node <?= odd_even(); ?>">
                <td class="user">
                    <?= Gravatar::img($user->email, ['align' => 'middle', 'alt' => 'user icon'], '32', URL_PUBLIC . 'assets/admin/images/user.png', 'g', USE_HTTPS); ?>
                    <a href="<?= getUrl('user/edit/' . $user->id); ?>"><?= $user->name; ?></a>
                    <small><?= $user->username; ?></small>
                </td>
                <td><?= $user->email; ?></td>
                <td><?= implode(', ', $user->roles()); ?></td>
                <td>
                    <?php if ($user->id > 1): ?>
                        <a href="<?= getUrl('user/delete/' . $user->id . '?csrf_token=' . SecureToken::generateToken(BASE_URL . 'user/delete/' . $user->id)); ?>" onclick="return confirm('<?= __('Are you sure you wish to delete') . ' ' . $user->name . '?'; ?>');"><img src="<?= PATH_PUBLIC; ?>assets/admin/images/icon-remove.gif" alt="<?= __('delete user icon'); ?>" title="<?= __('Delete user'); ?>" /></a>
                    <?php else: ?>
                        <img src="<?= PATH_PUBLIC; ?>assets/admin/images/icon-remove-disabled.gif" alt="<?= __('delete user icon disabled'); ?>" title="<?= __('Delete user unavailable'); ?>" />
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?> 
    </tbody>
</table>
