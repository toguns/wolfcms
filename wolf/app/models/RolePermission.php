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
 * RolePermission
 *
 * @todo finish phpdoc
 *
 * @author Martijn van der Kleijn <martijn.niji@gmail.com>
 * @since Wolf version 0.7.0
 */
class RolePermission extends Record {

    const TABLE_NAME = 'role_permission';

    public $role_id = false;
    public $permission_id = false;

    public static function savePermissionsFor(string $roleId, array $permissions) {
        if (!Record::existsIn('Role', 'id = :role_id', [':role_id' => $roleId])){
            return false;
        }

        if (!self::deleteWhere('RolePermission', 'role_id = :role_id', [':role_id' => (int) $roleId])){
            return false;
        }

        foreach ($permissions as $perm) {
            $rp = new RolePermission(['role_id' => $roleId, 'permission_id' => $perm->id]);
            if (!$rp->save()){
                return false;
            }
        }

        return true;
    }

    public static function findPermissionsFor(int $roleId) {
        $roleperms = self::find([
                    'where' => 'role_id = :role_id',
                    'values' => [':role_id' => $roleId]
        ]);

        $perms = [];

        foreach ($roleperms as $role => $perm) {
            $perms[] = Permission::findById($perm->permission_id);
        }

        return $perms;
    }

}
