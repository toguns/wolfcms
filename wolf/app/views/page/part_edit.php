
<div id="part-<?= $index; ?>-content" class="page">
    <div class="page" id="page-<?= $index; ?>">
        <div class="part" id="part-<?= $index; ?>">
            <input id="part_<?= ($index - 1); ?>_name" name="part[<?= ($index - 1); ?>][name]" type="hidden" value="<?= $page_part->name; ?>" />
            <?php if (isset($page_part->id)): ?>
                <input id="part_<?= ($index - 1); ?>_id" name="part[<?= ($index - 1); ?>][id]" type="hidden" value="<?= $page_part->id; ?>" />
            <?php endif; ?>
            <p>
                <label for="part_<?= ($index - 1); ?>_filter_id"><?= __('Filter'); ?></label>
                <select id="part_<?= ($index - 1); ?>_filter_id" class="filter-selector" name="part[<?= ($index - 1); ?>][filter_id]">
                    <option value=""<?php if ($page_part->filter_id == '') echo ' selected="selected"'; ?>>&#8212; <?= __('none'); ?> &#8212;</option>
                    <?php foreach (Filter::findAll() as $filter): ?> 
                        <option value="<?= $filter; ?>"<?php if ($page_part->filter_id == $filter) echo ' selected="selected"'; ?>><?= Inflector::humanize($filter); ?></option>
                    <?php endforeach; ?> 
                </select>
            </p>
            <div>
                <textarea class="textarea markitup<?php
                if ($page_part->filter_id != "") {
                    echo ' ' . $page_part->filter_id;
                }
                ?>" id="part_<?= ($index - 1); ?>_content" name="part[<?= ($index - 1); ?>][content]" style="width: 100%" rows="20" cols="40"><?= htmlentities($page_part->content, ENT_COMPAT, 'UTF-8'); ?></textarea>
            </div>
        </div>
    </div>
</div>