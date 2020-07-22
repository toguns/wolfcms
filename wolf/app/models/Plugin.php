<?php

/*
 * Wolf CMS - Content Management Simplified. <http://www.wolfcms.org>
 * Copyright (C) 2008,2009,2010 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Copyright (C) 2008 Philippe Archambault <philippe.archambault@gmail.com>
 *
 * This file is part of Wolf CMS. Wolf CMS is licensed under the GNU GPLv3 license.
 * Please see license.txt for the full license text.
 */

/**
 * @package Models
 *
 * @author Martijn van der Kleijn <martijn.niji@gmail.com>
 * @author Philippe Archambault <philippe.archambault@gmail.com>
 *
 * @copyright Martijn van der Kleijn 2008-2010
 * @copyright Philippe Archambault 2008
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 License
 */

/**
 * class Plugin
 *
 * Provide a Plugin API to make wolf more flexible
 *
 * @author Philippe Archambault <philippe.archambault@gmail.com>
 */
class Plugin {

    static array $plugins = [];
    static array $pluginsInfos = [];
    static array $updatefileCache = [];
    static array $controllers = [];
    static array $javascripts = [];
    static array $stylesheets = [];

    /**
     * Initialize all activated plugin by including is index.php file.
     * Also load all language files for plugins available in plugins directory.
     */
    static function init() {
        $dir = PLUGINS_ROOT . DS;

        if ($handle = opendir($dir)) {
            while (false !== ($plugin_id = readdir($handle))) {
                $file = $dir . $plugin_id . DS . 'i18n' . DS . I18n::getLocale() . '-message.php';
                $default_file = PLUGINS_ROOT . DS . $plugin_id . DS . 'i18n' . DS . DEFAULT_LOCALE . '-message.php';

                if (file_exists($file)) {
                    $array = include $file;
                    I18n::add($array);
                }

                if (file_exists($default_file)) {
                    $array = include $default_file;
                    I18n::addDefault($array);
                }
            }
        }

        $plugins = unserialize(Setting::get('plugins'));
        if (is_array($plugins)) {
            self::$plugins = $plugins;
        } else {
            self::$plugins = [];
        }
        foreach (self::$plugins as $plugin_id => $tmp) {
            $file = PLUGINS_ROOT . DS . $plugin_id . DS . 'index.php';
            if (file_exists($file)) {
                include $file;
            }
        }
    }

    /**
     * Sets plugin information. Parameters include:
     *
     * Mandatory
     * - id,
     * - title,
     * - description,
     * - author,
     * - version,
     *
     * Optional
     * - license,
     * - update_url,
     * - require_wolf_version,
     * - require_php_extensions,
     * - website
     *
     * @param infos array Assoc array with plugin informations
     */
    static function setInfos(array $infos) {
        if (!isset($infos['type']) && defined('CMS_BACKEND')) {
            self::$pluginsInfos[$infos['id']] = (object) $infos;
            return;
        } else if (!isset($infos['type'])) {
            return;
        }

        if (defined('CMS_BACKEND') && ($infos['type'] == 'backend' || $infos['type'] == 'both')) {
            self::$pluginsInfos[$infos['id']] = (object) $infos;
            return;
        }

        if (!defined('CMS_BACKEND') && ($infos['type'] == 'frontend' || $infos['type'] == 'both')) {
            self::$pluginsInfos[$infos['id']] = (object) $infos;
            return;
        }
    }

    /**
     * Activate a plugin. This will execute the enable.php file of the plugin
     * when found.
     *
     * @param plugin_id string	The plugin name to activate
     */
    static function activate(string $pluginId) {
        self::$plugins[$pluginId] = 1;
        self::save();

        $file = PLUGINS_ROOT . '/' . $pluginId . '/enable.php';
        if (file_exists($file)) {
            include $file;
        }

        // TODO Check if we actually need this, gets rid of E_NOTICE for now
        if (isset(self::$controllers[$pluginId])) {
            $class_name = Inflector::camelize($pluginId) . 'Controller';
            AutoLoader::addFile($class_name, self::$controllers[$pluginId]->file);
        }
    }

    /**
     * Deactivate a plugin
     *
     * @param plugin_id string	The plugin name to deactivate
     */
    static function deactivate(string $pluginId) {
        if (isset(self::$plugins[$pluginId])) {
            unset(self::$plugins[$pluginId]);
            self::save();

            $file = PLUGINS_ROOT . '/' . $pluginId . '/disable.php';
            if (file_exists($file)) {
                include $file;
            }
        }
    }

    /**
     * Uninstall a plugin
     *
     * @param plugin_id string	The plugin name to uninstall
     */
    static function uninstall(string $pluginId) {
        if (isset(self::$plugins[$pluginId])) {
            unset(self::$plugins[$pluginId]);
            self::save();
        }

        $file = PLUGINS_ROOT . '/' . $pluginId . '/uninstall.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    /**
     * Save activated plugins to the setting 'plugins'
     */
    static function save() {
        Setting::saveFromData(['plugins' => serialize(self::$plugins)]);
    }

    /**
     * Find all plugins installed in the plugin folder
     *
     * @return array
     */
    static function findAll() {
        $dir = PLUGINS_ROOT . '/';

        if ($handle = opendir($dir)) {
            while (false !== ($plugin_id = readdir($handle))) {
                if (!isset(self::$plugins[$plugin_id]) && is_dir($dir . $plugin_id) && strpos($plugin_id, '.') !== 0) {
                    $file = PLUGINS_ROOT . '/' . $plugin_id . '/index.php';
                    if (file_exists($file)) {
                        include $file;
                    }
                }
            }
            closedir($handle);
        }

        ksort(self::$pluginsInfos);
        return self::$pluginsInfos;
    }

    /**
     * Given a plugin, checks a number of prerequisites as specified in plugin's setInfos().
     *
     * Possible checks:
     *
     * - require_wolf_version (a valid Wolf CMS version number)
     * - require_php_extensions (comma seperated list of required extensions)
     */
    public static function hasPrerequisites(object $plugin, array &$errors = []) {
        // Check require_wolf_version
        if (isset($plugin->require_wolf_version) && version_compare($plugin->require_wolf_version, CMS_VERSION, '>')) {
            $errors[] = __('The plugin requires a minimum of Wolf CMS version :v.', [':v' => $plugin->require_wolf_version]);
        }

        // Check require_php_extension
        if (isset($plugin->require_php_extensions)) {
            $exts = explode(',', $plugin->require_php_extensions);
            foreach ($exts as $ext) {
                if (!empty($ext) && !extension_loaded($ext)) {
                    $errors[] = __('One or more required PHP extension is missing: :exts', [':exts', $plugin->require_php_extentions]);
                }
            }
        }

        if (count($errors) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check the file mentioned as update_url for the latest plugin version available.
     * Messages that can be returned:
     * unknown - returned if the plugin doesn't provide an update url
     * latest  - returned if the plugin version matches the version number registerd at the url
     * error   - returned if the update url could not be reached or for any other reason
     *
     * @param plugin     object A plugin object.
     *
     * @return           string The latest version number or a localized message.
     */
    static function checkLatest(object $plugin) {
        $data = null;

        if (!defined('CHECK_UPDATES') || !CHECK_UPDATES) {
            return __('unknown');
        }

        // Check if plugin has update file url set
        if (!isset($plugin->update_url)) {
            return __('unknown');
        }

        // Check if update file was already cached and is no older than 30 minutes
        if (array_key_exists($plugin->update_url, Plugin::$updatefileCache) && (Plugin::$updatefileCache[$plugin->update_url]['time'] + 30 * 60) < time()) {
            unset(Plugin::$updatefileCache[$plugin->update_url]);
        }

        if (!array_key_exists($plugin->update_url, Plugin::$updatefileCache)) {
            // Read and cache the update file
            if (!$data = getContentFromUrl($plugin->update_url)) {
                return __('error');
            }
            Plugin::$updatefileCache[$plugin->update_url] = ['time' => time(), 'data' => $data];
        }

        $xml = simplexml_load_string(Plugin::$updatefileCache[$plugin->update_url]['data']);

        foreach ($xml as $node) {
            if ($plugin->id == $node->id)
                if ($plugin->version == $node->version) {
                    return __('latest');
                } else {
                    return (string) $node->version;
                }
        }

        return __('error');
    }

    /**
     * Add a controller (tab) to the administration
     *
     * @param plugin_id     string  The folder name of the plugin
     * @param label         string  The tab label
     * @param permissions   string  List of roles that will have the tab displayed
     *                              separate by coma ie: 'administrator,developer'
     * @param show_tab      boolean Either 'true' or 'false'. Defaults to true.
     *
     * @return void
     */
    static function addController(string $pluginId, ?string $label = null, ?string $permissions = null, bool $showTab = true) {
        if (!isset(self::$pluginsInfos[$pluginId])) {
            return;
        }

        $className = Inflector::camelize($pluginId) . 'Controller';
        $file = PLUGINS_ROOT . '/' . $pluginId . '/' . $className . '.php';

        if (!file_exists($file)) {
            if (defined('DEBUG') && DEBUG) {
                throw new Exception('Plugin controller file not found: ' . $file);
            }
            return false;
        }

        self::$controllers[$pluginId] = (object) [
                    'label' => ucfirst($label),
                    'class_name' => $className,
                    'file' => $file,
                    'permissions' => $permissions,
                    'show_tab' => $showTab
        ];

        AutoLoader::addFile($className, self::$controllers[$pluginId]->file);

        return true;
    }

    /**
     * Add a javascript file to be added to the html page for a plugin.
     * Backend only right now.
     *
     * @param $pluginId    string  The folder name of the plugin
     * @param $file         string  The path to the javascript file relative to plugin root
     */
    static function addJavascript(string $pluginId, string $file) {
        if (file_exists(PLUGINS_ROOT . '/' . $pluginId . '/' . $file)) {
            self::$javascripts[] = $pluginId . '/' . $file;
        }
    }

    /**
     * Add a stylesheet file to be added to the html page for a plugin.
     * Backend only right now.
     *
     * @param $pluginId    string  The folder name of the plugin
     * @param $file         string  The path to the stylesheet file relative to plugin root
     */
    static function addStylesheet(string $pluginId, string $file) {
        if (file_exists(PLUGINS_ROOT . '/' . $pluginId . '/' . $file)) {
            self::$stylesheets[] = $pluginId . '/' . $file;
        }
    }

    static function hasSettingsPage(string $pluginId) {
        $className = Inflector::camelize($pluginId) . 'Controller';

        return (array_key_exists($pluginId, Plugin::$controllers) && method_exists($className, 'settings'));
    }

    static function hasDocumentationPage(string $pluginId) {
        $className = Inflector::camelize($pluginId) . 'Controller';

        return (array_key_exists($pluginId, Plugin::$controllers) && method_exists($className, 'documentation'));
    }

    /**
     * Returns true if a plugin is enabled for use.
     *
     * @param string $pluginId
     */
    static function isEnabled(string $pluginId) {
        if (array_key_exists($pluginId, Plugin::$plugins) && Plugin::$plugins[$pluginId] == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns true when all settings for $plugin_id where deleted.
     *
     * @global constant $__CMS_CONN__
     * @param string    $pluginId
     * @return boolean  True when successful
     */
    static function deleteAllSettings(string $pluginId) {
        if (empty($pluginId)) {
            return false;
        }

        $tablename = TABLE_PREFIX . 'plugin_settings';

        $sql = "DELETE FROM $tablename WHERE plugin_id=:pluginid";

        Record::logQuery($sql);

        $stmt = Record::getConnection()->prepare($sql);

        return $stmt->execute([':pluginid' => $pluginId]);
    }

    /**
     * Stores all settings from a name<->value pair array in the database.
     *
     * @param array $array Array of name-value pairs
     * @param string $pluginId     The folder name of the plugin
     * @return bool Returns true if successful otherwise returns false.
     */
    static function setAllSettings(array $array, string $pluginId) {

        if (empty($array) || empty($pluginId)) {
            return false;
        }

        $tablename = TABLE_PREFIX . 'plugin_settings';

        $existingSettings = [];

        $sql = "SELECT name FROM $tablename WHERE plugin_id=:pluginid";

        Record::logQuery($sql);

        $stmt = Record::getConnection()->prepare($sql);

        $stmt->execute([':pluginid' => $pluginId]);

        while ($settingname = $stmt->fetchColumn()) {
            $existingSettings[$settingname] = $settingname;
        }

        $ret = false;

        foreach ($array as $name => $value) {
            if (array_key_exists($name, $existingSettings)) {
                $sql = "UPDATE $tablename SET value=:value WHERE name=:name AND plugin_id=:pluginid";
            } else {
                $sql = "INSERT INTO $tablename (value, name, plugin_id) VALUES (:value, :name, :pluginid)";
            }

            Record::logQuery($sql);

            $stmt = Record::getConnection()->prepare($sql);

            $ret = $stmt->execute([':pluginid' => $pluginId, ':name' => $name, ':value' => $value]);
        }

        return $ret;
    }

    /**
     * Allows you to store a single setting in the database.
     *
     * @param string $name          Setting name
     * @param string $value         Setting value
     * @param string $pluginId     Plugin folder name
     * @return bool Returns true upon success otherwise false.
     */
    static function setSetting(string $name, string $value, string $pluginId) {

        if (empty($name) || empty($value) || empty($pluginId)) {
            return false;
        }

        $tablename = TABLE_PREFIX . 'plugin_settings';

        $existingSettings = [];

        $sql = "SELECT name FROM $tablename WHERE plugin_id=:pluginid";

        Record::logQuery($sql);

        $stmt = Record::getConnection()->prepare($sql);

        $stmt->execute([':pluginid' => $pluginId]);

        while ($settingname = $stmt->fetchColumn()) {
            $existingSettings[$settingname] = $settingname;
        }

        if (in_array($name, $existingSettings)) {
            $sql = "UPDATE $tablename SET value=:value WHERE name=:name AND plugin_id=:pluginid";
        } else {
            $sql = "INSERT INTO $tablename (value, name, plugin_id) VALUES (:value, :name, :pluginid)";
        }


        Record::logQuery($sql);

        $stmt = Record::getConnection()->prepare($sql);

        return $stmt->execute([':pluginid' => $pluginId, ':name' => $name, ':value' => $value]);
    }

    /**
     * Retrieves all settings for a plugin and returns an array of name-value pairs.
     * Returns empty array when unsuccessful in retrieving the settings.
     *
     * @param <type> $pluginId
     */
    static function getAllSettings(?string $pluginId = null) {
        if ($pluginId == null) {
            return false;
        }

        $tablename = TABLE_PREFIX . 'plugin_settings';

        $settings = [];

        $sql = "SELECT name,value FROM $tablename WHERE plugin_id=:pluginid";

        Record::logQuery($sql);

        $stmt = Record::getConnection()->prepare($sql);

        $stmt->execute([':pluginid' => $pluginId]);

        while ($obj = $stmt->fetchObject()) {
            $settings[$obj->name] = $obj->value;
        }

        return $settings;
    }

    /**
     * Returns the value for a specified setting.
     * Returns false when unsuccessful in retrieving the setting.
     *
     * @param <type> $name
     * @param <type> $pluginId
     */
    static function getSetting(?string $name = null, ?string $pluginId = null) {
        if ($name == null || $pluginId == null)
            return false;

        $tablename = TABLE_PREFIX . 'plugin_settings';

        $existingSettings = [];

        $sql = "SELECT value FROM $tablename WHERE plugin_id=:pluginid AND name=:name LIMIT 1";

        Record::logQuery($sql);

        $stmt = Record::getConnection()->prepare($sql);

        $stmt->execute([':pluginid' => $pluginId, ':name' => $name]);

        return $stmt->fetchColumn();
    }

}

// end Plugin class
