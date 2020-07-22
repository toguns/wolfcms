
<h1><?= __(ucfirst($action) . ' snippet'); ?></h1>

<form action="<?= $action == 'edit' ? getUrl('snippet/edit/' . $snippet->id) : getUrl('snippet/add');?>" method="post" autocomplete="off">
    <input id="csrf_token" name="csrf_token" type="hidden" value="<?= $csrf_token; ?>" />
    <div class="form-area">
        <h3><?= __('Name'); ?></h3>
        <div id="meta-pages" class="pages">
            <p class="title">
                <input class="textbox" id="snippet_name" maxlength="100" name="snippet[name]" size="255" type="text" value="<?= $snippet->name; ?>" />
            </p>
        </div>

        <h3><?= __('Body'); ?></h3>
        <div id="pages" class="pages">
            <div class="page" style="">
                <p>
                    <label for="snippet_filter_id"><?= __('Filter'); ?></label>
                    <select id="snippet_filter_id" class="filter-selector" name="snippet[filter_id]">
                        <option value=""<?php if ($snippet->filter_id == '') echo ' selected="selected"'; ?>>&#8212; <?= __('none'); ?> &#8212;</option>
                        <?php foreach ($filters as $filter): ?>
                            <option value="<?= $filter; ?>"<?php if ($snippet->filter_id == $filter) echo ' selected="selected"'; ?>><?= Inflector::humanize($filter); ?></option>
<?php endforeach; ?>
                    </select>
                </p>
                <textarea class="textarea" cols="40" id="snippet_content" name="snippet[content]" rows="20" style="width: 100%"><?= htmlentities($snippet->content, ENT_COMPAT, 'UTF-8'); ?></textarea>
<?php if (isset($snippet->updated_on)): ?>
                    <p style="clear: left">
                        <small><?= __('Last updated by'); ?> <?= $snippet->updated_by_name; ?> <?= __('on'); ?> <?= date('D, j M Y', strtotime($snippet->updated_on)); ?></small>
                    </p>
<?php endif; ?>
            </div>
        </div>
    </div>
    <p class="buttons">
<?php if (($action == 'edit' && AuthUser::hasPermission('snippet_edit')) || ($action == 'add' && AuthUser::hasPermission('snippet_add'))): ?>
            <input class="button" name="commit" type="submit" accesskey="s" value="<?= __('Save'); ?>" />
            <input class="button" name="continue" type="submit" accesskey="e" value="<?= __('Save and Continue Editing'); ?>" />
            <?= __('or'); ?> 
        <?php else: ?>
            <?= ($action == 'add') ? __('You do not have permission to add snippets!') : __('You do not have permission to edit snippets!'); ?> 
<?php endif; ?>
        <a href="<?= getUrl('snippet'); ?>"><?= __('Cancel'); ?></a>
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

    document.getElementById('snippet_name').focus();
// ]]>
</script>