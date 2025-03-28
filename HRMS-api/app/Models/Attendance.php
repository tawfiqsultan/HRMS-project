<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';
    protected $primaryKey = 'AttendanceID';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;


    protected $fillable = [
        'EmployeeID',
        'AttendanceDate',
        'CheckInTime',
        'CheckOutTime',
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'EmployeeID');
    }
}
