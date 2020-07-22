
<!DOCTYPE html>
<html>
    <head>
        <title><?= __('Forgot password'); ?></title>
        <base href="<?= trim(BASE_URL, '?/') . '/'; ?>" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link href="<?= PATH_PUBLIC; ?>assets/admin/themes/<?= Setting::get('theme'); ?>/login.css" id="css_theme" media="screen" rel="Stylesheet" type="text/css" />
        <script type="text/javascript" charset="utf-8" src="<?= PATH_PUBLIC; ?>assets/admin/javascripts/jquery-1.8.3.min.js"></script>
        <script type="text/javascript">
            // <![CDATA[
            $(document).ready(function () {
                (function showMessages(e) {
                    e.fadeIn('slow')
                            .animate({opacity: 1.0}, 1500)
                            .fadeOut('slow', function () {
                                if ($(this).next().attr('class') == 'message') {
                                    showMessages($(this).next());
                                }
                                $(this).remove();
                            })
                })($(".message:first"));

                $("input:visible:enabled:first").focus();
            });
            // ]]>
        </script>
    </head>
    <body>
        <div id="dialog">
            <h1><?= __('Forgot password'); ?></h1>
            <?php if (Flash::get('error') !== null): ?>
                <div id="error" class="message" style="display: none;"><?= Flash::get('error'); ?></div>
            <?php endif; ?>
            <?php if (Flash::get('success') !== null): ?>
                <div id="success" class="message" style="display: none"><?= Flash::get('success'); ?></div>
            <?php endif; ?>
            <?php if (Flash::get('info') !== null): ?>
                <div id="info" class="message" style="display: none"><?= Flash::get('info'); ?></div>
            <?php endif; ?>
            <form action="<?= getUrl('login', 'forgot'); ?>" method="post" autocomplete="off">
                <div>
                    <label for="forgot-email"><?= __('Email address'); ?>:</label>
                    <input class="long" id="forgot-email" type="email" name="forgot[email]" value="<?= $email; ?>" required />
                </div>
                <div id="forgot-submit">
                    <button class="submit" type="submit" accesskey="s"><?= __('Send password'); ?></button>
                    <span>(<a href="<?= getUrl('login'); ?>"><?= __('Login'); ?></a>)</span>
                </div>
            </form>
        </div>
        <p><?= __('website:') . ' <a href="' . URL_PUBLIC . '">' . Setting::get('admin_title') . '</a>'; ?></p>
    </body>
</html>
