
<!DOCTYPE html>
<html>
    <head>
        <title><?= __('Login') . ' - ' . Setting::get('admin_title'); ?></title>
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
            <h1><?= __('Login') . ' - ' . Setting::get('admin_title'); ?></h1>
            <?php if (Flash::get('error') !== null): ?>
                <div id="error" class="message" style="display: none;"><?= Flash::get('error'); ?></div>
            <?php endif; ?>
            <?php if (Flash::get('success') !== null): ?>
                <div id="success" class="message" style="display: none"><?= Flash::get('success'); ?></div>
            <?php endif; ?>
            <?php if (Flash::get('info') !== null): ?>
                <div id="info" class="message" style="display: none"><?= Flash::get('info'); ?></div>
            <?php endif; ?>
                <form action="<?= getUrl('login/login'); ?>" method="post" autocomplete="off">
                <div id="login-username-div">
                    <label for="login-username"><?= __('Username'); ?>:</label>
                    <input id="login-username" class="medium" type="text" name="login[username]" required/>
                </div>
                <div id="login-password-div">
                    <label for="login-password"><?= __('Password'); ?>:</label>
                    <input id="login-password" class="medium" type="password" name="login[password]" required />
                </div>
                <div class="clean"></div>
                <div style="margin-top: 6px">
                    <input id="login-remember-me" type="checkbox" class="checkbox" name="login[remember]" value="checked" />
                    <input id="login-redirect" type="hidden" name="login[redirect]" value="<?= $redirect; ?>" />
                    <label class="checkbox" for="login-remember-me"><?= __('Remember me for :min minutes.', [':min' => round(COOKIE_LIFE / 60)]); ?></label>
                </div>
                <div id="login_submit">
                    <button class="submit" type="submit" accesskey="s"><?= __('Login'); ?></button>
                    <span>(<a href="<?= getUrl('login/forgot'); ?>"><?= __('Forgot password?'); ?></a>)</span>
                </div>
            </form>
        </div>
        <p><?= __('website:') . ' <a href="' . URL_PUBLIC . '">' . Setting::get('admin_title') . '</a>'; ?></p>
        <script type="text/javascript" charset="utf-8">
            // <![CDATA[
            var loginUsername = document.getElementById('login-username');
            if (loginUsername.value == '') {
                loginUsername.focus();
            } else {
                document.getElementById('login-password').focus();
            }
            // ]]>
        </script>
    </body>
</html>
