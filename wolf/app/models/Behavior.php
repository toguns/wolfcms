<?php

/*
 * Wolf CMS - Content Management Simplified. <http://www.wolfcms.org>
 * Copyright (C) 2008-2010 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Copyright (C) 2008 Philippe Archambault <philippe.archambault@gmail.com>
 *
 * This file is part of Wolf CMS. Wolf CMS is licensed under the GNU GPLv3 license.
 * Please see license.txt for the full license text.
 */

/**
 * @package Models
 *
 * @author Philippe Archambault <philippe.archambault@gmail.com>
 * @copyright Philippe Archambault, 2008
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 License
 */

/**
 * Class Behavior
 *
 * This is a part of the Plugin API of Wolf CMS. It provide a "interface" to
 * add and remove behavior "page type" to Wolf CMS.
 *
 * @since Wolf version 0.5
 */
class Behavior {

    private static array $loadedFiles = [];
    private static array $behaviors = [];

    /**
     * Add a new behavior to Wolf CMS
     *
     * @param behavior_id string  The Behavior plugin folder name
     * @param file      string  The file where the Behavior class is
     */
    public static function add(string $behaviorId, string $file) {
        self::$behaviors[$behaviorId] = $file;
    }

    /**
     * Remove a behavior to Wolf CMS
     *
     * @param behavior_id string  The Behavior plugin folder name
     */
    public static function remove(string $behaviorId) {
        self::$behaviors[$behaviorId] = null;
        unset(self::$behaviors[$behaviorId]);
    }

    /**
     * Load a behavior and return it
     *
     * @param behavior_id string  The Behavior plugin folder name
     * @param page        object  Will be pass to the behavior
     * @param params      array   Params that fallow the page with this behavior (passed to the behavior too)
     *
     * @return object
     */
    public static function load(string $behaviorId, &$page, $params) {
        if (!empty(self::$behaviors[$behaviorId])) {
            $file = CORE_ROOT . '/plugins/' . self::$behaviors[$behaviorId];
            $behaviorClass = Inflector::camelize($behaviorId);

            if (isset(self::$loadedFiles[$file])) {
                return new $behaviorClass($page, $params);
            }

            if (file_exists($file)) {
                include $file;
                self::$loadedFiles[$file] = true;
                return new $behaviorClass($page, $params);
            } else {
                exit("Behavior $behaviorId not found!");
            }
        }
    }

    /**
     * Load a behavior and return it
     *
     * @param behavior_id string  The Behavior plugin folder name
     *
     * @return string   class name of the page
     */
    public static function loadPageHack(string $behaviorId) {
        $behaviorPageClass = Inflector::camelize('page_' . $behaviorId);

        if (class_exists($behaviorPageClass, false)) {
            return $behaviorPageClass;
        } else {
            return 'Page';
        }
    }

    /**
     *
      Find all active Behaviors id

      return array
     */
    public static function findAll() {
        return array_keys(self::$behaviors);
    }

}

// end Behavior class
