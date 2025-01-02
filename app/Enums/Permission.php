<?php

namespace App\Enums;

enum Permission: string
{
    case VIEW = 'view';
    case VIEW_ANY = 'view_any';
    case VIEW_OWN = 'view_own';
    case CREATE = 'create';
    case UPDATE = 'update';
    case RESTORE = 'restore';
    case RESTORE_ANY = 'restore_any';
    case DELETE = 'delete';
    case DELETE_ANY = 'delete_any';
    case FORCE_DELETE = 'force_delete';
    case FORCE_DELETE_ANY = 'force_delete_any';
    case MANAGE_SETTINGS = 'manage_settings';
    case ABSENCE_REQUEST = 'absence_request';
    case ABSENCE_VIEW_SENSITIVE = 'absence_view_sensitive';
}
