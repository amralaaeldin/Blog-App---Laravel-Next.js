<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedException extends HttpException
{
    private $requiredRoles = [];

    private $requiredPermissions = [];

    public static function forRoles(array $roles): self
    {
        $message = __('User does not have the right roles.');

        if (config('permission.display_role_in_exception')) {
            foreach ($roles as &$role) {
                $role = __($role);
            }
            $message .= __(' Necessary roles are ') . implode(', ', $roles);
        }

        $exception = new static(403, $message, null, []);
        $exception->requiredRoles = $roles;

        return $exception;
    }

    public static function forPermissions(array $permissions): self
    {
        $message = __('User does not have the right permissions.');

        if (config('permission.display_permission_in_exception')) {
            foreach ($permissions as &$permission) {
                $permission = __($permission);
            }
            $message .= __(' Necessary permissions are ') . implode(', ', $permissions);
        }

        $exception = new static(403, $message, null, []);
        $exception->requiredPermissions = $permissions;

        return $exception;
    }

    public static function forRolesOrPermissions(array $rolesOrPermissions): self
    {
        $message = __('User does not have any of the necessary access rights.');

        if (config('permission.display_permission_in_exception') && config('permission.display_role_in_exception')) {
            foreach ($rolesOrPermissions as &$roleOrPermission) {
                $roleOrPermission = __($roleOrPermission);
            }
            $message .= __(' Necessary roles or permissions are ') . implode(', ', $rolesOrPermissions);
        }

        $exception = new static(403, $message, null, []);
        $exception->requiredPermissions = $rolesOrPermissions;

        return $exception;
    }

    public static function notLoggedIn(): self
    {
        return new static(403, __('User is not logged in.'), null, []);
    }

    public function getRequiredRoles(): array
    {
        return $this->requiredRoles;
    }

    public function getRequiredPermissions(): array
    {
        return $this->requiredPermissions;
    }
}
