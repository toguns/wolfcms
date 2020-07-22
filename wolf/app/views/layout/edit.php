<h1><?= __(ucfirst($action) . ' layout'); ?></h1>

<form action="<?= $action == 'edit' ? getUrl('layout/edit/' . $layout->id) : getUrl('layout/add');?>" method="post" autocomplete="off">
    <input id="csrf_token" name="csrf_token" type="hidden" value="<?= $csrf_token; ?>" />
    <div class="form-area">
        <p class="title">
            <label for="layout_name"><?= __('Name'); ?></label>
            <input class="textbox" id="layout_name" maxlength="100" name="layout[name]" size="100" type="text" value="<?= $layout->name; ?>" />
        </p>

        <table class="fieldset" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td><label for="layout_content_type"><?= __('Content-Type'); ?></label></td>
                <td class="field"><input class="textbox" id="layout_content_type" maxlength="40" name="layout[content_type]" size="40" type="text" value="<?= $layout->content_type; ?>" /></td>
            </tr>
        </table>

        <p class="content">
            <label for="layout_content"><?= __('Body'); ?></label>
            <textarea class="textarea" cols="40" id="layout_content" name="layout[content]" rows="20" style="width: 100%"><?= htmlentities($layout->content, ENT_COMPAT, 'UTF-8'); ?></textarea>
        </p>
        <?php if (isset($layout->updated_on)) { ?>
            <p style="clear: left"><small><?= __('Last updated by'); ?> <?= $layout->updated_by_name; ?> <?= __('on'); ?> <?= date('D, j M Y', strtotime($layout->updated_on)); ?></small></p>
<?php } ?>
    </div>
    <p class="buttons">
        <button class="button" name="commit" type="submit" accesskey="s"><?= __('Save'); ?></button>
        <button class="button" name="continue" type="submit" accesskey="e"><?= __('Save and Continue Editing'); ?></button>
<?= __('or'); ?> <a href="<?= getUrl('layout'); ?>"><?= __('Cancel'); ?></a>
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

    document.getElementById('layout_name').focus();
// ]]>
</script>