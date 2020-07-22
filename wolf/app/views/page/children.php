
<ul<?php
if ($level == 1)
    echo ' id="site-map" class="sortable tree-root"';
else
    echo ' class="sortable child"';
?>>
    <?php foreach ($childrens as $child): ?> 
        <li id="page_<?= $child->id; ?>" class="node level-<?php
        echo $level;
        if (!$child->has_children)
            echo ' no-children';
        else if ($child->is_expanded)
            echo ' children-visible';
        else
            echo ' children-hidden';
        ?>">
            <div class="content-children">
                <div class="page">
                    <span class="w1">
                        <?php if ($child->has_children): ?><img align="middle" alt="toggle children" class="expander<?php if ($child->is_expanded) echo ' expanded'; ?>" src="<?= PATH_PUBLIC; ?>assets/admin/images/<?= $child->is_expanded ? 'collapse' : 'expand'; ?>.png" title="" /><?php endif; ?>
                        <?php if (!AuthUser::hasPermission('page_edit') || (!AuthUser::hasPermission('admin_edit') && $child->is_protected)): ?>
                            <img align="middle" class="icon" src="<?= PATH_PUBLIC; ?>assets/admin/images/page.png" alt="page icon" /> <span class="title protected"><?= $child->title; ?></span> <img class="handle_reorder" src="<?= PATH_PUBLIC; ?>assets/admin/images/drag_to_sort.gif" alt="<?= __('Drag and Drop'); ?>" align="middle" />
                        <?php else: ?>
                            <a class="edit-link" href="<?= getUrl('page/edit/' . $child->id); ?>" title="<?= $child->id . ' | ' . $child->slug; ?>"><img align="middle" class="icon" src="<?= PATH_PUBLIC; ?>assets/admin/images/page.png" alt="page icon" /> <span class="title"><?= $child->title; ?></span></a> <img class="handle_reorder" src="<?= PATH_PUBLIC; ?>assets/admin/images/drag_to_sort.gif" alt="<?= __('Drag and Drop'); ?>" align="middle" /> <img class="handle_copy" src="<?= PATH_PUBLIC; ?>assets/admin/images/drag_to_copy.gif" alt="<?= __('Drag to Copy'); ?>" align="middle" />
                            <?php endif; ?>
    <?php if (!empty($child->behavior_id)): ?> <small class="info">(<?= Inflector::humanize($child->behavior_id); ?>)</small><?php endif; ?> 
                        <img align="middle" alt="" class="busy" id="busy-<?= $child->id; ?>" src="<?= PATH_PUBLIC; ?>assets/admin/images/spinner.gif" title="" />
                    </span>
                </div>
                <div class="page-layout"><?php
                    $layout = Layout::findById($child->layout_id);
                    echo isset($layout->name) ? htmlspecialchars($layout->name) : __('inherit');
                    ?></div>
                <?php
                switch ($child->status_id) {
                    case Page::STATUS_DRAFT: echo '<div class="status draft-status">' . __('Draft') . '</div>';
                        break;
                    case Page::STATUS_PREVIEW: echo '<div class="status preview-status">' . __('Preview') . '</div>';
                        break;
                    case Page::STATUS_PUBLISHED: echo '<div class="status published-status">' . __('Published') . '</div>';
                        break;
                    case Page::STATUS_HIDDEN: echo '<div class="status hidden-status">' . __('Hidden') . '</div>';
                        break;
                    case Page::STATUS_ARCHIVED: echo '<div class="status archived-status">' . __('Archived') . '</div>';
                        break;
                }
                ?>
                <div class="view-page"><a class="view-link" href="<?php
                    echo URL_PUBLIC;
                    echo $child->path();
                    echo ($child->path() != '') ? URL_SUFFIX : '';
                    ?>" target="_blank"><img src="<?= PATH_PUBLIC; ?>assets/admin/images/magnify.png" align="middle" alt="<?= __('View Page'); ?>" title="<?= __('View Page'); ?>" /></a></div>
                <div class="modify">
                    <a class="add-child-link" href="<?= getUrl('page/add', $child->id); ?>"><img src="<?= PATH_PUBLIC; ?>assets/admin/images/plus.png" align="middle" title="<?= __('Add child'); ?>" alt="<?= __('Add child'); ?>" /></a>&nbsp;
                    <?php if (!$child->is_protected || AuthUser::hasPermission('page_delete')): ?>
                        <a class="remove" href="<?= getUrl('page/delete/' . $child->id . '?csrf_token=' . SecureToken::generateToken(BASE_URL . 'page/delete/' . $child->id)); ?>" onclick="return confirm('<?= __('Are you sure you wish to delete'); ?> <?= $child->title; ?> <?= __('and its underlying pages'); ?>?');"><img src="<?= PATH_PUBLIC; ?>assets/admin/images/icon-remove.gif" align="middle" alt="<?= __('Remove page'); ?>" title="<?= __('Remove page'); ?>" /></a>&nbsp;
    <?php endif; ?>
                    <a href="#" id="copy-<?= $child->id; ?>" class="copy-page"><img src="<?= PATH_PUBLIC; ?>assets/admin/images/copy.png" align="middle" title="<?= __('Copy Page'); ?>" alt="<?= __('Copy Page'); ?>" /></a>
                </div>
            </div><!-- /.content-children -->
        <?php if ($child->is_expanded) echo $child->children_rows; ?>
        </li>
<?php endforeach; ?>
</ul>