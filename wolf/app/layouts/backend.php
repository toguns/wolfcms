<?php
/**
 * @package Layouts
 */
/* Security measure */
if (!defined('IN_CMS')) {
    exit();
}

// Redirect to front page if user doesn't have appropriate roles.
if (!AuthUser::hasPermission('admin_view')) {
    header('Location: ' . URL_PUBLIC . ' ');
    exit();
}

// Setup some stuff...
$ctrl = Dispatcher::getController(Setting::get('default_tab'));

// Allow for nice title.
// @todo improve/clean this up.
if (!isset($title) || trim($title) == '') {
    $title = ($ctrl == 'plugin') ? Plugin::$controllers[Dispatcher::getAction()]->label : ucfirst($ctrl) . 's';
    if (isset($this->vars['content_for_layout']->vars['action'])) {
        $tmp = $this->vars['content_for_layout']->vars['action'];
        $title .= ' - ' . ucfirst($tmp);

        if ($tmp == 'edit' && isset($this->vars['content_for_layout']->vars['page'])) {
            $tmp = $this->vars['content_for_layout']->vars['page'];
            $title .= ' - ' . $tmp->title;
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php
use_helper('Kses');
echo $title . ' | ' . kses(Setting::get('admin_title'), []);
?></title>

        <link rel="favourites icon" href="<?= PATH_PUBLIC; ?>assets/admin/images/favicon.ico" />
        <link href="<?= PATH_PUBLIC; ?>assets/admin/stylesheets/admin.css" media="screen" rel="Stylesheet" type="text/css" />
        <link href="<?= PATH_PUBLIC; ?>assets/admin/themes/<?= Setting::get('theme'); ?>/styles.css" id="css_theme" media="screen" rel="Stylesheet" type="text/css" />

        <!-- IE6 PNG support fix -->
        <!--[if lt IE 7]>
            <script type="text/javascript" charset="utf-8" src="<?= PATH_PUBLIC; ?>wolf/admin/javascripts/unitpngfix.js"></script>
        <![endif]-->
        <script type="text/javascript" charset="utf-8" src="<?= PATH_PUBLIC; ?>assets/admin/javascripts/cp-datepicker.js"></script>
        <script type="text/javascript" charset="utf-8" src="<?= PATH_PUBLIC; ?>assets/admin/javascripts/wolf.js"></script>
        <script type="text/javascript" charset="utf-8" src="<?= PATH_PUBLIC; ?>assets/admin/javascripts/jquery-1.8.3.min.js"></script> 
        <script type="text/javascript" charset="utf-8" src="<?= PATH_PUBLIC; ?>assets/admin/javascripts/jquery-ui-1.10.3.min.js"></script>
        <script type="text/javascript" charset="utf-8" src="<?= PATH_PUBLIC; ?>assets/admin/javascripts/jquery.ui.nestedSortable.js"></script>

        <?php Observer::notify('view_backend_layout_head', CURRENT_PATH); ?>

        <script type="text/javascript" src="<?= PATH_PUBLIC; ?>assets/admin/markitup/jquery.markitup.js"></script>
        <link rel="stylesheet" type="text/css" href="<?= PATH_PUBLIC; ?>assets/admin/markitup/skins/simple/style.css" />

        <?php foreach (Plugin::$plugins as $pluginId => $plugin): ?>
            <?php if (file_exists(ASSETS_ROOT . 'plugins/' . $pluginId . '/' . $pluginId . '.js')): ?>
                <script type="text/javascript" charset="utf-8" src="<?= PLUGINS_ASSETS_PATH; ?><?= $pluginId . '/' . $pluginId; ?>.js"></script>
            <?php endif; ?>
            <?php if (file_exists(ASSETS_ROOT . 'plugins/' . $pluginId . '/' . $pluginId . '.css')): ?>
                <link href="<?= PLUGINS_ASSETS_PATH; ?><?= $pluginId . '/' . $pluginId; ?>.css" media="screen" rel="Stylesheet" type="text/css" />
            <?php endif; ?>
        <?php endforeach; ?>
        <?php foreach (Plugin::$stylesheets as $pluginId => $stylesheet): ?>
            <link type="text/css" href="<?= PLUGINS_ASSETS_PATH; ?><?= $stylesheet; ?>" media="screen" rel="Stylesheet" />
        <?php endforeach; ?>
        <?php foreach (Plugin::$javascripts as $jscriptPluginId => $javascript): ?>
            <script type="text/javascript" charset="utf-8" src="<?= PLUGINS_ASSETS_PATH; ?><?= $javascript; ?>"></script>
        <?php endforeach; ?>

        <script type="text/javascript">
            // <![CDATA[
            $(document).ready(function () {
                (function showMessages(e) {
                    e.fadeIn('slow')
                            .animate({opacity: 1.0}, Math.min(5000, parseInt(e.text().length * 50)))
                            .fadeOut('slow', function () {
                                if ($(this).next().attr('class') == 'message') {
                                    showMessages($(this).next());
                                }
                                $(this).remove();
                            })
                })($('.message:first'));

                $('input:visible:enabled:first').focus();

                // Get the initial values and activate filter
                $('.filter-selector').each(function () {
                    var $this = $(this);
                    $this.data('oldValue', $this.val());

                    if ($this.val() == '') {
                        return true;
                    }
                    var elemId = $this.attr('id').slice(0, -10);
                    var elem = $('#' + elemId + '_content');
                    $this.trigger('wolfSwitchFilterIn', [$this.val(), elem]);
                });

                $('.filter-selector').live('change', function () {
                    var $this = $(this);
                    var newFilter = $this.val();
                    var oldFilter = $this.data('oldValue');
                    $this.data('oldValue', newFilter);
                    var elemId = $this.attr('id').slice(0, -10);
                    var elem = $('#' + elemId + '_content');
                    $(this).trigger('wolfSwitchFilterOut', [oldFilter, elem]);
                    $(this).trigger('wolfSwitchFilterIn', [newFilter, elem]);
                });
            });
            // ]]>
        </script>

        <?php $action = Dispatcher::getAction(); ?>
    </head>
    <body id="body_<?= $ctrl . '_' . Dispatcher::getAction(); ?>">
        <!-- Div to allow for modal dialogs -->
        <div id="mask"></div>

        <div id="header">
            <div id="site-title"><a href="<?= getUrl(); ?>"><?= Setting::get('admin_title'); ?></a></div>
            <div id="mainTabs">
                <ul>
                    <li id="page-plugin" class="plugin"><a href="<?= getUrl('page'); ?>"<?php if ($ctrl == 'page') echo ' class="current"'; ?>><?= __('Pages'); ?></a></li>
                    <?php if (AuthUser::hasPermission('snippet_view')): ?>
                        <li id="snippet-plugin" class="plugin"><a href="<?= getUrl('snippet'); ?>"<?php if ($ctrl == 'snippet') echo ' class="current"'; ?>><?= __('MSG_SNIPPETS'); ?></a></li>
                    <?php endif; ?>
                    <?php if (AuthUser::hasPermission('layout_view')): ?>
                        <li id="layout-plugin" class="plugin"><a href="<?= getUrl('layout'); ?>"<?php if ($ctrl == 'layout') echo ' class="current"'; ?>><?= __('Layouts'); ?></a></li>
                    <?php endif; ?>

                    <?php foreach (Plugin::$controllers as $pluginName => $plugin): ?>
                        <?php if ($plugin->show_tab && (AuthUser::hasPermission($plugin->permissions))): ?>
                            <?php Observer::notify('view_backend_list_plugin', $pluginName, $plugin); ?>
                            <li id="<?= $pluginName; ?>-plugin" class="plugin"><a href="<?= getUrl('plugin/' . $pluginName); ?>"<?php if ($ctrl == 'plugin' && $action == $pluginName) echo ' class="current"'; ?>><?= $plugin->label; ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if (AuthUser::hasPermission('admin_edit')): ?>
                        <li class="right"><a href="<?= getUrl('setting'); ?>"<?php if ($ctrl == 'setting') echo ' class="current"'; ?>><?= __('Administration'); ?></a></li>
                    <?php endif; ?>
                    <?php if (AuthUser::hasPermission('user_view')): ?>
                        <li class="right"><a href="<?= getUrl('user'); ?>"<?php if ($ctrl == 'user') echo ' class="current"'; ?>><?= __('Users'); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php if (Flash::get('error') !== null): ?>
            <div id="error" class="message" style="display: none;"><?= Flash::get('error'); ?></div>
        <?php endif; ?>
        <?php if (Flash::get('success') !== null): ?>
            <div id="success" class="message" style="display: none"><?= Flash::get('success'); ?></div>
        <?php endif; ?>
        <?php if (Flash::get('info') !== null): ?>
            <div id="info" class="message" style="display: none"><?= Flash::get('info'); ?></div>
        <?php endif; ?>
        <div id="main">
            <div id="content-wrapper">
                <div id="content">
                    <!-- content -->
                    <?= $content_for_layout; ?>
                    <!-- end content -->
                </div>
            </div>
            <?php if (isset($sidebar)) { ?>
                <div id="sidebar-wrapper">
                    <div id="sidebar">
                        <!-- sidebar -->
                        <?= $sidebar; ?>
                        <!-- end sidebar -->
                    </div>
                </div>
            <?php } ?>
        </div>

        <div id="footer">
            <p>
                <?= __('Thank you for using'); ?> <a href="http://www.wolfcms.org/" target="_blank">Wolf CMS</a> <?= CMS_VERSION; ?> | <a href="http://forum.wolfcms.org/" target="_blank"><?= __('Feedback'); ?></a> | <a href="http://docs.wolfcms.org/" target="_blank"><?= __('Documentation'); ?></a>
            </p>
            <?php if (DEBUG): ?>
                <p class="stats">
                    <?= __('Page rendered in'); ?> <?= execution_time(); ?> <?= __('seconds'); ?>
                    | <?= __('Memory usage:'); ?> <?= memory_usage(); ?>
                </p>
            <?php endif; ?>

            <p id="site-links">
                <?= __('You are currently logged in as'); ?> <a href="<?= getUrl('user/edit/' . AuthUser::getId()); ?>"><?= AuthUser::getRecord()->name; ?></a>
                <span class="separator"> | </span>
                <a href="<?= getUrl('login/logout' . '?csrf_token=' . SecureToken::generateToken(BASE_URL . 'login/logout')); ?>"><?= __('Log Out'); ?></a>
                <span class="separator"> | </span>
                <a id="site-view-link" href="<?= URL_PUBLIC; ?>" target="_blank"><?= __('View Site'); ?></a>
            </p>
        </div>
    </body>
</html>
