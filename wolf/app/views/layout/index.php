
<h1><?= __('Layouts'); ?></h1>

<div id="site-map-def" class="index-def">
    <div class="layout">
        <?= __('Layout'); ?> (<a href="#" id="reorder-toggle"><?= __('reorder'); ?></a>)
    </div>
    <div class="modify"><?= __('Modify'); ?></div>
</div>

<ul id="layouts" class="index">
    <?php foreach ($layouts as $layout) { ?>
        <li id="layout_<?= $layout->id; ?>" class="layout node <?= odd_even(); ?>">
            <img align="middle" alt="layout-icon" src="<?= ASSETS_PATH; ?>admin/images/layout.png" title="" />
            <a href="<?= getUrl('layout/edit/' . $layout->id); ?>"><?= $layout->name; ?></a>
            <img class="handle" src="<?= ASSETS_PATH; ?>admin/images/drag.gif" alt="<?= __('Drag and Drop'); ?>" align="middle" />
            <div class="remove"><a href="<?= getUrl('layout/delete/' . $layout->id); ?>?csrf_token=<?= SecureToken::generateToken(BASE_URL . 'layout/delete/' . $layout->id); ?>" onclick="return confirm('<?= __('Are you sure you wish to delete'); ?> <?= $layout->name; ?>?');"><img alt="<?= __('delete layout icon'); ?>" title="<?= __('Delete layout'); ?>" src="<?= ASSETS_PATH; ?>admin/images/icon-remove.gif" /></a></div>
        </li>
    <?php } ?>
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
                var order = $(ui.item.parent()).sortable('serialize', {key: 'layouts[]'});
                $.post('<?= getUrl('layout/reorder/'); ?>', {data: order});
            }
        })
                .disableSelection();

        return this;
    };

    $(document).ready(function () {
        $('ul#layouts').sortableSetup();
        $('#reorder-toggle').toggle(
                function () {
                    $('ul#layouts').sortable('option', 'disabled', false);
                    $('.handle').show();
                    $('#reorder-toggle').text('<?= __('disable reorder'); ?>');
                },
                function () {
                    $('ul#layouts').sortable('option', 'disabled', true);
                    $('.handle').hide();
                    $('#reorder-toggle').text('<?= __('reorder'); ?>');
                }
        )
    });

// ]]>
</script>
