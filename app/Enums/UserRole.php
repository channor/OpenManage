<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = "super_admin";
    case EMPLOYEE = "employee";
    case PAYROLL_CLERK = "payroll_clerk";
    case HR_MANAGER = "hr_manager";
    case HR_COORDINATOR = "hr_coordinator";
}
