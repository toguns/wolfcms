<?php
// @todo clean up code/solution
$pagetmp = Flash::get('page');
$parttmp = Flash::get('page_parts');
$tagstmp = Flash::get('page_tag');
if ($pagetmp != null && !empty($pagetmp) && $parttmp != null && !empty($parttmp) && $tagstmp != null && !empty($tagstmp)) {
    $page = $pagetmp;
    $page_parts = $parttmp;
    $tags = $tagstmp;
}

if ($action == 'edit') {
    ?>
    <span style="float: right;"><a id="site-view-page" onclick="target = '_blank'" onkeypress="target = '_blank'" href="<?php
    echo URL_PUBLIC;
    echo (USE_MOD_REWRITE == false) ? '?' : '';
    echo $page->path();
    echo ($page->path() != '') ? URL_SUFFIX : '';
    ?>"><?= __('View this page'); ?></a></span>
                               <?php } ?>

<h1><?= __(ucfirst($action) . ' Page'); ?></h1>

<form id="page_edit_form" action="<?php
                               if ($action == 'add')
                                   echo getUrl('page/add');
                               else
                                   echo getUrl('page/edit/' . $page->id);
                               ?>" method="post" autocomplete="off">

    <input id="page_parent_id" name="page[parent_id]" type="hidden" value="<?= $page->parent_id; ?>" />
    <input id="csrf_token" name="csrf_token" type="hidden" value="<?= $csrf_token; ?>" />
    <div class="form-area">
        <div id="metainfo-tabs" class="content tabs">
            <ul class="tabNavigation">
                <li class="tab"><a href="#pagetitle"><?= __('Page Title'); ?></a></li>
                <li class="tab"><a href="#metadata"><?= __('Metadata'); ?></a></li>
                <li class="tab"><a href="#settings"><?= __('Settings'); ?></a></li>
                <?php Observer::notify('view_page_edit_tab_links', $page); ?>
            </ul>
        </div>
        <div id="metainfo-content" class="pages">
            <div id="pagetitle" class="page">
                <div id="div-title" class="title" title="<?= __('Page Title'); ?>">
                    <input class="textbox" id="page_title" maxlength="255" name="page[title]" size="255" type="text" value="<?= $page->title; ?>" />
                </div>
            </div>
            <div id="metadata" class="page">
                <div id="div-metadata" title="<?= __('Metadata'); ?>">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <?php if ($page->parent_id != 0) : ?>
                            <tr>
                                <td class="label"><label for="page_slug"><?= __('Slug'); ?></label></td>
                                <td class="field"><input class="textbox" id="page_slug" maxlength="100" name="page[slug]" size="100" type="text" value="<?= $page->slug; ?>" /></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="label"><label for="page_breadcrumb"><?= __('Breadcrumb'); ?></label></td>
                            <td class="field"><input class="textbox" id="page_breadcrumb" maxlength="160" name="page[breadcrumb]" size="160" type="text" value="<?= htmlentities($page->breadcrumb, ENT_COMPAT, 'UTF-8'); ?>" /></td>
                        </tr>
                        <tr>
                            <td class="label optional"><label for="page_keywords"><?= __('Keywords'); ?></label></td>
                            <td class="field"><input class="textbox" id="page_keywords" maxlength="255" name="page[keywords]" size="255" type="text" value="<?= $page->keywords; ?>" /></td>
                        </tr>
                        <tr>
                            <td class="label optional"><label for="page_description"><?= __('Description'); ?></label></td>
                            <td class="field"><textarea class="textarea" id="page_description" name="page[description]" rows="2" cols="3"><?= $page->description; ?></textarea></td>
                        </tr>
                        <tr>
                            <td class="label optional"><label for="page_tags"><?= __('Tags'); ?></label></td>
                            <td class="field"><input class="textbox" id="page_tags" maxlength="255" name="page_tag[tags]" size="255" type="text" value="<?= join(', ', $tags); ?>" /></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="settings" class="page">
                <div id="div-settings" title="<?= __('Settings'); ?>">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <?php if ($page->parent_id != 0) : ?>
                            <tr>
                                <td class="label"><label for="page_id"><?= __('Page id'); ?></label></td>
                                <td class="field"><input class="textbox" id="page_id" maxlength="100" name="unused" size="100" type="text" value="<?= $page->id; ?>" disabled="disabled"/></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="label"><label for="page_layout_id"><?= __('Layout'); ?></label></td>
                            <td class="field">
                                <select id="page_layout_id" name="page[layout_id]">
                                    <option value="0">&#8212; <?= __('inherit'); ?> &#8212;</option>
                                    <?php foreach ($layouts as $layout): ?>
                                        <option value="<?= $layout->id; ?>"<?= $layout->id == $page->layout_id ? ' selected="selected"' : ''; ?>><?= $layout->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><label for="page_behavior_id"><?= __('Page Type'); ?></label></td>
                            <td class="field">
                                <select id="page_behavior_id" name="page[behavior_id]">
                                    <option value=""<?php if ($page->behavior_id == '') echo ' selected="selected"'; ?>>&#8212; <?= __('none'); ?> &#8212;</option>
                                    <?php foreach ($behaviors as $behavior): ?>
                                        <option value="<?= $behavior; ?>"<?php if ($page->behavior_id == $behavior) echo ' selected="selected"'; ?>><?= Inflector::humanize($behavior); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <?php if (isset($page->created_on)): ?>
                            <tr>
                                <td class="label"><label for="page_created_on"><?= __('Created date'); ?></label></td>
                                <td class="field">
                                    <input id="page_created_on" maxlength="10" name="page[created_on]" size="10" type="text" value="<?= substr($page->created_on, 0, 10); ?>" />
                                    <img class="datepicker" onclick="displayDatePicker('page[created_on]');" src="<?= PATH_PUBLIC; ?>assets/admin/images/icon_cal.gif" alt="<?= __('Show Calendar'); ?>" />
                                    <input id="page_created_on_time" maxlength="8" name="page[created_on_time]" size="8" type="text" value="<?= substr($page->created_on, 11); ?>" />
                                    <?php if (isset($page->published_on)): ?>
                                        &nbsp; <label for="page_published_on"><?= __('Published date'); ?></label>
                                        <input id="page_published_on" maxlength="10" name="page[published_on]" size="10" type="text" value="<?= substr($page->published_on, 0, 10); ?>" />
                                        <img onclick="displayDatePicker('page[published_on]');" src="<?= PATH_PUBLIC; ?>assets/admin/images/icon_cal.gif" alt="<?= __('Show Calendar'); ?>" />
                                        <input id="page_published_on_time" maxlength="8" name="page[published_on_time]" size="8" type="text" value="<?= substr($page->published_on, 11); ?>" />
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if (isset($page->published_on)): ?>
                                <tr>
                                    <td class="label">
                                        <label for="page_valid_until"><?= __('Valid until date'); ?></label>
                                    </td>
                                    <td class="field">
                                        <input id="page_valid_until" maxlength="10" name="page[valid_until]" size="10" type="text" value="<?= substr($page->valid_until, 0, 10); ?>" />
                                        <img onclick="displayDatePicker('page[valid_until]');" src="<?= PATH_PUBLIC; ?>assets/admin/images/icon_cal.gif" alt="<?= __('Show Calendar'); ?>" />
                                        <input id="page_valid_until_time" maxlength="8" name="page[valid_until_time]" size="8" type="text" value="<?= substr($page->valid_until, 11); ?>" />
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if (AuthUser::hasPermission('page_edit')): ?>
                            <tr>
                                <td class="label"><label for="page_needs_login"><?= __('Login:'); ?></label></td>
                                <td class="field">
                                    <select id="page_needs_login" name="page[needs_login]" title="<?= __('When enabled, users have to login before they can view the page.'); ?>">
                                        <option value="<?= Page::LOGIN_INHERIT; ?>"<?= $page->needs_login == Page::LOGIN_INHERIT ? ' selected="selected"' : ''; ?>><?= __('&#8212; inherit &#8212;'); ?></option>
                                        <option value="<?= Page::LOGIN_NOT_REQUIRED; ?>"<?= $page->needs_login == Page::LOGIN_NOT_REQUIRED ? ' selected="selected"' : ''; ?>><?= __('not required'); ?></option>
                                        <option value="<?= Page::LOGIN_REQUIRED; ?>"<?= $page->needs_login == Page::LOGIN_REQUIRED ? ' selected="selected"' : ''; ?>><?= __('required'); ?></option>
                                    </select>
                                    <input id="page_is_protected" name="page[is_protected]" class="checkbox" type="checkbox" value="1"<?php if ($page->is_protected) echo ' checked="checked"'; ?><?php if (!AuthUser::hasPermission('admin_edit')) echo ' disabled="disabled"'; ?>/><label for="page_is_protected" title="<?= __('When enabled, only users who are an administrator can edit the page.'); ?>"> <?= __('Protected'); ?> </label>
                                </td>
                            </tr>
                        <?php endif; ?>

                    </table>
                </div>
            </div>
            <?php Observer::notify('view_page_edit_tabs', $page); ?>
        </div>

        <div id="part-tabs" class="content tabs">
            <div id="tab-toolbar" class="tab_toolbar">
                <a href="#" id="add-part" title="<?= __('Add Tab'); ?>"><img src="<?= PATH_PUBLIC; ?>assets/admin/images/plus.png" alt="<?= __('Add Tab'); ?> icon" /></a>
                <a href="#" id="delete-part" title="<?= __('Remove Tab'); ?>"><img src="<?= PATH_PUBLIC; ?>assets/admin/images/minus.png" alt="<?= __('Remove Tab'); ?> icon" /></a>
            </div>
            <ul class="tabNavigation">
                <?php foreach ($page_parts as $key => $page_part) { ?>
                    <li id="part-<?= $key + 1; ?>-tab" class="tab"><a href="#part-<?= $key + 1; ?>-content"><?= $page_part->name; ?></a></li>
                <?php } ?>
            </ul>
        </div>
        <div id="part-content" class="pages">
            <?php
            $index = 1;
            foreach ($page_parts as $page_part) {
                echo new View('page/part_edit', ['index' => $index, 'page_part' => $page_part]);
                $index++;
            }
            ?>
        </div>

        <?php Observer::notify('view_page_after_edit_tabs', $page); ?>

        <div class="row">
            <?php if (!isset($page->id) || $page->id != 1): ?>
                <p><label for="page_status_id"><?= __('Status'); ?></label>
                    <select id="page_status_id" name="page[status_id]">
                        <option value="<?= Page::STATUS_DRAFT; ?>"<?= $page->status_id == Page::STATUS_DRAFT ? ' selected="selected"' : ''; ?>><?= __('Draft'); ?></option>
                        <option value="<?= Page::STATUS_PREVIEW; ?>"<?= $page->status_id == Page::STATUS_PREVIEW ? ' selected="selected"' : ''; ?>><?= __('Preview'); ?></option>
                        <option value="<?= Page::STATUS_PUBLISHED; ?>"<?= $page->status_id == Page::STATUS_PUBLISHED ? ' selected="selected"' : ''; ?>><?= __('Published'); ?></option>
                        <option value="<?= Page::STATUS_HIDDEN; ?>"<?= $page->status_id == Page::STATUS_HIDDEN ? ' selected="selected"' : ''; ?>><?= __('Hidden'); ?></option>
                        <option value="<?= Page::STATUS_ARCHIVED; ?>"<?= $page->status_id == Page::STATUS_ARCHIVED ? ' selected="selected"' : ''; ?>><?= __('Archived'); ?></option>
                    </select>
                </p>
            <?php endif; ?>
            <?php Observer::notify('view_page_edit_plugins', $page); ?>
        </div>

        <p><small>
                <?php if (isset($page->updated_on)): ?>
                    <?= __('Last updated by :username on :date', [':username' => $page->updated_by_name, ':date' => date('D, j M Y', strtotime($page->updated_on))]); ?>
                <?php endif; ?>
                &nbsp;
            </small></p>

    </div>
    <p class="buttons">
        <button class="button" name="commit" type="submit" accesskey="s"><?= __('Save and Close'); ?></button>
        <button class="button" name="continue" type="submit" accesskey="e"><?= __('Save and Continue Editing'); ?></button>
        <?= __('or'); ?> <a href="<?= getUrl('page'); ?>"><?= __('Cancel'); ?></a>
    </p>

</form>

<div id="boxes">
    <!-- #Demo dialog -->
    <div id="dialog" class="window">
        <div class="titlebar">
            Demo dialog
            <a href="#" class="close">[x]</a>
        </div>
        <div class="content">
            <p>This is just a demo.</p>
        </div>
    </div>

    <!-- Add part dialog -->
    <div id="add-part-dialog" class="window">
        <div class="titlebar">
            <div id="busy" class="busy" style="display: none;"><img alt="Spinner" src="<?= PATH_PUBLIC; ?>assets/admin/images/spinner.gif" /></div>
            <?= __('Add Part'); ?>
            <a href="" class="close">[x]</a>
        </div>
        <div class="content">
            <form autocomplete="off" action="<?php //echo get_url('page/addPart');    ?>" method="post">
                <div>
                    <input id="part-index-field" name="part[index]" type="hidden" value="<?= $index; ?>" />
                    <input id="part-name-field" maxlength="100" name="part[name]" type="text" />
                    <input id="add-part-button" name="commit" type="submit" value="<?= __('Add'); ?>" />
                </div>
            </form>
        </div>
    </div>
    <?php Observer::notify('view_page_edit_popup', $page); ?>

</div>

<script type="text/javascript">
// <![CDATA[
    function setConfirmUnload(on, msg) {
        window.onbeforeunload = (on) ? unloadMessage : null;
        return true;
    }

    function unloadMessage() {
        return '<?= __('You have modified this page.  If you navigate away from this page without first saving your data, the changes will be lost.'); ?>';
    }

    jQuery.fn.spinnerSetup = function spinnerSetup() {
        this.each(function () {
            var pid = $(this).attr('id')
            $('#' + pid).hide()  // hide it initially
                    .ajaxStop(function () {
                        $('#' + pid).hide();
                    });
        });

        return this;
    };

    $(document).ready(function () {
        $(".busy").spinnerSetup();

        var editAction = '<?= $action; ?>';

        if (editAction == 'add') {
            $('#page_title').change(function () {
                $('#page_slug').val(toSlug(this.value));
                $('#page_breadcrumb').val(this.value);
            });
        }

        // Store PHP value for later reference
        var partIndex = <?= $index; ?>;

        // Prevent accidentally navigating away
        $('form#page_edit_form :input').bind('change', function () {
            setConfirmUnload(true);
        });
        $('form#page_edit_form').submit(function () {
            setConfirmUnload(false);
            return true;
        });

        // Do the metainfo tab thing
        $('div#metainfo-tabs ul.tabNavigation li a').bind('click', function (event) {
            $('div#metainfo-content > div.page').hide().filter(this.hash).show();
            /* Get index and current page id*/
            var i = $(this).parent('li').index();
            var pageID = page_id();

            $('div#metainfo-tabs ul.tabNavigation a.here').removeClass('here');
            $(this).addClass('here');

            $(this).trigger('metaInfoTabFocus', [i, this.hash]);
            document.cookie = "meta_tab=" + pageID + ':' + i;
            return false;
        });

        // Do the parts tab thing
        $('div#part-tabs ul.tabNavigation a').live('click', function (event) {
            $('div#part-content > div.page').hide().filter(this.hash).show();
            /* Get index and current page id */
            var i = $(this).parent('li').index();
            var pageID = page_id();

            $('div#part-tabs ul.tabNavigation a.here').removeClass('here');
            $(this).addClass('here');

            document.cookie = "page_tab=" + pageID + ':' + i;
            $(this).trigger('pageTabFocus', [i, this.hash]);
            return false;
        });

        (function () {
            var id, metaTab, pageTab,
                    pageId = page_id(),
                    meta = document.cookie.match(/meta_tab=(\d+):(\d+);/),
                    part = document.cookie.match(/page_tab=(\d+):(\d+);/);

            if (meta && pageId == meta[1]) {
                metaTab = (meta[2]) ? meta[2] : 0;
            } else {
                metaTab = 0;
            }

            if (part && pageId == part[1]) {
                pageTab = (part[2]) ? part[2] : 0;
            } else {
                pageTab = 0;
            }

            $('div#metainfo-content > div.page').hide();
            $('div#metainfo-tabs ul.tabNavigation li a').eq(metaTab).click();

            $('div#part-content > div.page').hide();
            $('div#part-tabs ul.tabNavigation li a').eq(pageTab).click();
        })();

        // Do the add part button thing
        $('#add-part').click(function () {

            // START show popup
            var id = 'div#boxes div#add-part-dialog';

            $('div#add-part-dialog div.content form input#part-name-field').val('');

            //Get the screen height and width
            var maskHeight = $(document).height();
            var maskWidth = $(window).width();

            //Set height and width to mask to fill up the whole screen
            $('#mask').css({'width': maskWidth, 'height': maskHeight, 'top': 0, 'left': 0});

            //transition effect
            $('#mask').show();
            $('#mask').fadeTo("fast", 0.5);

            //Get the window height and width
            var winH = $(window).height();
            var winW = $(window).width();

            //Set the popup window to center
            $(id).css('top', winH / 2 - $(id).height() / 2);
            $(id).css('left', winW / 2 - $(id).width() / 2);

            //transition effect
            $(id).fadeIn("fast"); //2000

            $(id + " :input:visible:enabled:first").focus();
            // END show popup
        });

        // Do the submit add part window thing
        $('div#add-part-dialog div.content form').submit(function (e) {
            e.preventDefault();

            if (valid_part_name($('div#add-part-dialog div.content form input#part-name-field').val())) {
                $('div#part-tabs ul.tabNavigation').append('<li id="part-' + partIndex + '-tab" class="tab">\n\
                                                             <a href="#part-' + partIndex + '-content">' + $('div#add-part-dialog div.content form input#part-name-field').val() + '</a></li>');

                $('div#part-tabs ul.tabNavigation li#part-' + partIndex + '-tab a').click();
                $('div#add-part-dialog div.content form input#part-index-field').val(partIndex);

                $('#busy').show();

                $.post('<?= getUrl('page/addPart'); ?>',
                        $('div#add-part-dialog div.content form').serialize(),
                        function (data) {
                            $('div#part-content').append(data);
                            $('#busy').hide();
                        });

                partIndex++;

                // Make sure users save changes
                setConfirmUnload(true);
            }

            $('#mask, .window').hide();

            return false;
        });

        // Do the delete part button thing
        $('#delete-part').click(function () {
            // Delete the tab
            var partRegEx = /part-(\d+)-tab/i;
            var myRegEx = new RegExp(partRegEx);
            var matched = myRegEx.exec($('div#part-tabs ul.tabNavigation li.tab a.here').parent().attr('id'));
            var removePart = matched[1];

            if (!confirm('<?= __('Delete the current tab?'); ?>')) {
                return;
            }

            $('div#part-tabs ul.tabNavigation li.tab a.here').remove();
            $('div#part-tabs ul.tabNavigation a').filter(':first').click();

            // Delete the content section
            $('div#part-' + removePart + '-content').remove();

            // Make sure users save changes
            setConfirmUnload(true);
        });


        // Make all modal dialogs draggable
        $("#boxes .window").draggable({
            addClasses: false,
            containment: 'window',
            scroll: false,
            handle: '.titlebar'
        })

        //if close button is clicked
        $('#boxes .window .close').click(function (e) {
            //Cancel the link behavior
            e.preventDefault();
            $('#mask, .window').hide();
        });

    });
// ]]>
</script>
