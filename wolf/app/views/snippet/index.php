
<h1><?= __('MSG_SNIPPETS'); ?></h1>

<div id="site-map-def" class="index-def">
    <div class="snippet">
        <?= __('Snippet'); ?> (<a href="#" id="reorder-toggle"><?= __('reorder'); ?></a>)
    </div>
    <div class="modify"><?= __('Modify'); ?></div>
</div>

<ul id="snippets" class="index">
    <?php foreach ($snippets as $snippet): ?>
        <li id="snippet_<?= $snippet->id; ?>" class="snippet node <?= odd_even(); ?>">
            <img align="middle" alt="snippet-icon" src="<?= PATH_PUBLIC; ?>assets/admin/images/snippet.png" />
            <a href="<?= getUrl('snippet/edit/' . $snippet->id); ?>"><?= $snippet->name; ?></a>
            <img class="handle" src="<?= PATH_PUBLIC; ?>assets/admin/images/drag.gif" alt="<?= __('Drag and Drop'); ?>" align="middle" />
            <div class="remove">
                <?php if (AuthUser::hasPermission('snippet_delete')): ?>        
                    <a class="remove" href="<?= getUrl('snippet/delete/' . $snippet->id); ?>" onclick="return confirm('<?= __('Are you sure you wish to delete?'); ?> <?= $snippet->name; ?>?');"><img src="<?= PATH_PUBLIC; ?>assets/admin/images/icon-remove.gif" alt="<?= __('delete snippet icon'); ?>" title="<?= __('Delete snippet'); ?>" /></a>
                <?php endif; ?>
            </div>
        </li>
    <?php endforeach; ?>
</ul>

<style type="text/css" >
    .placeholder {
        height: 2.4em;
        line-height: 1.2em;
        border: 1px solid #fcefa1;
        background-color: #fbf9ee;
        color: #363636;
    }
</style>

<script type="text/javascript">
// <![CDATA[
    jQuery.fn.sortableSetup = function sortableSetup() {
        this.sortable({
            disabled: true,
            tolerance: 'intersect',
            containment: '#main',
            placeholder: 'placeholder',
            revert: true,
            handle: '.handle',
            cursor: 'crosshair',
            distance: '15',
            stop: function (event, ui) {
                var order = $(ui.item.parent()).sortable('serialize', {key: 'snippets[]'});
                $.post('<?= getUrl('snippet/reorder/'); ?>', {data: order});
            }
        })
                .disableSelection();

        return this;
    };

    $(document).ready(function () {
        $('ul#snippets').sortableSetup();
        $('#reorder-toggle').toggle(
                function () {
                    $('ul#snippets').sortable('option', 'disabled', false);
                    $('.handle').show();
                    $('#reorder-toggle').text('<?= __('disable reorder'); ?>');
                },
                function () {
                    $('ul#snippets').sortable('option', 'disabled', true);
                    $('.handle').hide();
                    $('#reorder-toggle').text('<?= __('reorder'); ?>');
                }
        )
    });

// ]]>
</script>
