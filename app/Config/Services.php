<?php

namespace Config;

use App\Models\StudentAddressModel;
use App\Models\StudentContactModel;
use App\Models\StudentModel;
use App\Models\UserModel;
use App\Services\AuthService;
use App\Services\StudentService;
use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function authService(bool $getShared = true): AuthService
    {
        if ($getShared) {
            return static::getSharedInstance('authService');
        }

        return new AuthService(new UserModel());
    }

    public static function studentService(bool $getShared = true): StudentService
    {
        if ($getShared) {
            return static::getSharedInstance('studentService');
        }

        return new StudentService(
            new StudentModel(),
            new StudentContactModel(),
            new StudentAddressModel(),
        );
    }
}
