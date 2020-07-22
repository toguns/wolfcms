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
 * class UserPermission
 *
 * @author Philippe Archambault <philippe.archambault@gmail.com>
 */
class UserPermission extends Record {

    const TABLE_NAME = 'user_permission';

    public $user_id = false;
    public $permission_id = false;

    public static function setPermissionsFor(int $userId, array $perms) {
        $tablename = self::tableNameFromClassName('UserPermission');

        // remove all perms of this user
        $sql = 'DELETE FROM ' . $tablename . ' WHERE user_id=' . $userId;

        self::logQuery($sql);

        self::$__CONN__->exec($sql);

        $sql = 'INSERT INTO ' . $tablename . ' (user_id, permission_id) VALUES ';
        // add the new perms
        foreach ($perms as $permName => $permId) {
            $sql .= ' (' . (int) $userId . ',' . (int) $permId . ')';
        }

        self::logQuery($sql);

        self::$__CONN__->exec($sql);
    }

}

// end UserPermission class
