<?php

/*
 * Wolf CMS - Content Management Simplified. <http://www.wolfcms.org>
 * Copyright (C) 2009-2010 Martijn van der Kleijn <martijn.niji@gmail.com>
 *
 * This file is part of Wolf CMS. Wolf CMS is licensed under the GNU GPLv3 license.
 * Please see license.txt for the full license text.
 */

/**
 * @package Models
 *
 * @author Martijn van der Kleijn <martijn.niji@gmail.com>
 * @copyright Martijn van der Kleijn, 2010
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 License
 */

/**
 * UserRole
 *
 * @author Martijn van der Kleijn <martijn.niji@gmail.com>
 * @since Wolf version 0.7.0
 */
class UserRole extends Record {

    const TABLE_NAME = 'user_role';

    public $user_id = false;
    public $role_id = false;

    public static function setRolesFor(int $userId, array $roles) {

        Record::deleteWhere('UserRole', 'user_id = :user_id', [':user_id' => $userId]);

        foreach ($roles as $role => $role_id) {
            Record::insert('UserRole', ['user_id' => (int) $userId, 'role_id' => (int) $role_id]);
        }
    }

}
