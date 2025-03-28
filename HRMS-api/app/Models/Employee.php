<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\department;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employee';
    protected $primaryKey = 'EmployeeID';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'FullName',
        'PhoneNumber',
        'Salary',
        'StartDate',
        'DepartmentID',
    ];


    public function department()
    {
        return $this->belongsTo(Department::class, 'DepartmentID');
    }

    public function departmentsManaged()
    {
        return $this->hasMany(Department::class, 'ManagerID');
    }

    /**
     * العلاقة مع جدول Attendance.
     * Employee.employee_id -> Attendance.employee_id
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'EmployeeID');
    }

    /**
     * العلاقة مع جدول Payroll.
     * Employee.employee_id -> Payroll.employee_id
     */
    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'EmployeeID');
    }

    /**
     * العلاقة مع جدول Users (حساب المستخدم).
     * Employee.employee_id -> Users.employee_id
     */
    public function user()
    {
        return $this->hasOne(User::class, 'EmployeeID');
    }
}
