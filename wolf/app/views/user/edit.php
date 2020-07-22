
<h1><?= __(ucfirst($action) . ' user'); ?></h1>

<form action="<?= $action == 'edit' ? getUrl('user/edit/' . $user->id) : getUrl('user/add');?>" method="post" autocomplete="off">
    <input id="csrf_token" name="csrf_token" type="hidden" value="<?= $csrf_token; ?>" />
    <table class="fieldset" cellpadding="0" cellspacing="0" border="0">
        <tr>

            <td class="label"><label for="user_name"><?= __('Name'); ?></label></td>
            <td class="field"><input class="textbox" id="user_name" maxlength="100" name="user[name]" size="100" type="text" value="<?= $user->name; ?>" /></td>
            <td class="help"><?= __('Required.'); ?></td>
        </tr>
        <tr>
            <td class="label"><label class="optional" for="user_email"><?= __('E-mail'); ?></label></td>
            <td class="field"><input class="textbox" id="user_email" maxlength="255" name="user[email]" size="255" type="text" value="<?= $user->email; ?>" /></td>

            <td class="help"><?= __('Optional. Please use a valid e-mail address.'); ?></td>
        </tr>
        <tr>
            <td class="label"><label for="user_username"><?= __('Username'); ?></label></td>
            <td class="field"><input class="textbox" id="user_username" maxlength="40" name="user[username]" size="40" type="text" value="<?= $user->username; ?>" <?= $action == 'edit' ? 'disabled="disabled" ' : ''; ?>/></td>
            <td class="help"><?= __('At least 3 characters. Must be unique.'); ?></td>
        </tr>

        <tr>
            <td class="label"><label for="user_password"><?= __('Password'); ?></label></td>
            <td class="field"><input class="textbox" id="user_password" maxlength="40" name="user[password]" size="40" type="password" value="" /></td>
            <td class="help" rowspan="2"><?= __('At least 5 characters.'); ?> <?php
                if ($action == 'edit') {
                    echo __('Leave password blank for it to remain unchanged.');
                }
                ?></td>
        </tr>
        <tr>
            <td class="label"><label for="user_confirm"><?= __('Confirm Password'); ?></label></td>

            <td class="field"><input class="textbox" id="user_confirm" maxlength="40" name="user[confirm]" size="40" type="password" value="" /></td>
        </tr>
                <?php if (AuthUser::hasPermission('user_edit')): ?>
            <tr>
                <td class="label"><?= __('Roles'); ?></td>
                <td class="field">
                    <?php $user_roles = ($user instanceof User) ? $user->roles() : []; ?>
    <?php foreach ($roles as $role): ?>
                        <span class="checkbox"><input<?php if (in_array($role->name, $user_roles)) echo ' checked="checked"'; ?>  id="user_role-<?= $role->name; ?>" name="user_role[<?= $role->name; ?>]" type="checkbox" value="<?= $role->id; ?>" />&nbsp;<label for="user_role-<?= $role->name; ?>"><?= __(ucwords($role->name)); ?></label></span>
            <?php endforeach; ?>
                </td>
                <td class="help"><?= __('Roles restrict user privileges and turn parts of the administrative interface on or off.'); ?></td>
            </tr>
<?php endif; ?>

        <tr>
            <td class="label"><label for="user_language"><?= __('Language'); ?></label></td>
            <td class="field">
                <select class="select" id="user_language" name="user[language]">
<?php foreach (Setting::getLanguages() as $code => $label): ?>
                        <option value="<?= $code; ?>"<?php if ($code == $user->language) echo ' selected="selected"'; ?>><?= $label; ?></option>
<?php endforeach; ?>
                </select>
            </td>
            <td class="help"><?= __('This will set your preferred language for the backend.'); ?></td>
        </tr>

    </table>

        <?php Observer::notify('user_edit_view_after_details', $user); ?>

    <p class="buttons">
        <button class="button" name="commit" type="submit" accesskey="s"><?= __('Save'); ?></button>
<?= __('or'); ?> <a href="<?= (AuthUser::hasPermission('user_view')) ? getUrl('user') : getUrl(); ?>"><?= __('Cancel'); ?></a>
    </p>

</form>

<script type="text/javascript">
// <![CDATA[
    function setConfirmUnload(on, msg) {
        window.onbeforeunload = (on) ? unloadMessage : null;
        return true;
    }

    function unloadMessage() {
        return '<?= __('You have modified this page.  If you navigate away from this page without first saving your data, the changes will be lost.'); ?>';
    }

    $(document).ready(function () {
        // Prevent accidentally navigating away
        $(':input').bind('change', function () {
            setConfirmUnload(true);
        });
        $('form').submit(function () {
            setConfirmUnload(false);
            return true;
        });
    });

    Field.activate('user_name');
// ]]>
</script>
