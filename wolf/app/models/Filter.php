<?php

/*
 * Wolf CMS - Content Management Simplified. <http://www.wolfcms.org>
 * Copyright (C) 2009-2010 Martijn van der Kleijn <martijn.niji@gmail.com>
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
 * Class Filter
 *
 * This is a part of the Plugin API of Wolf CMS. It provide a "interface" to
 * add a new filter to the Wolf CMS administration.
 */
class Filter {

    static array $filters = [];
    private static array $filtersLoaded = [];

    /**
     * Add a new filter to Wolf CMS
     *
     * @param filter_id string  The Filter plugin folder name
     * @param file      string  The file where the Filter class is
     */
    public static function add(string $filterId, string $file) {
        self::$filters[$filterId] = $file;
    }

    /**
     * Remove a filter to Wolf CMS
     *
     * @param filter_id string  The Filter plugin folder name
     */
    public static function remove(string $filterId) {
        if (isset(self::$filters[$filterId])){
            unset(self::$filters[$filterId]);
        }
    }

    /**
     * Find all active filters id
     *
     * @return array
     */
    public static function findAll() {
        return array_keys(self::$filters);
    }

    /**
     * Get a instance of a filter
     *
     * @param filter_id string  The Filter plugin folder name
     *
     * @return mixed   if founded an object, else false
     */
    public static function get(string $filterId) {
        if (!isset(self::$filtersLoaded[$filterId])) {
            if (isset(self::$filters[$filterId])) {
                $file = CORE_ROOT . '/plugins/' . self::$filters[$filterId];
                if (file_exists($file)) {
                    include $file;

                    $filterClass = Inflector::camelize($filterId);
                    self::$filtersLoaded[$filterId] = new $filterClass();
                    return self::$filtersLoaded[$filterId];
                }
            } else{
                return false;
            }
        } else{
            return self::$filtersLoaded[$filterId];
        }
    }

}

// end Filter class

