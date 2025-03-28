<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AttendanceController extends Controller
{

    public function getAllAttendance()
    {
        try {
            $attendance = Attendance::with('employee')->get();

            if ($attendance->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No attendance found.',
                    'data'    => null
                ], 404);
            }

            $attendance->transform(function ($attendance) {
                $attendance->employee_name = $attendance->employee ? $attendance->employee->FullName : null;

                unset($attendance->employee);
                return $attendance;
            });

            return response()->json([
                'success' => true,
                'message' => 'attendance retrieved successfully.',
                'data'    => $attendance
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'failed to get attendance.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function startWorkDay()
    {
        DB::beginTransaction();
        try {
            $today = Carbon::now()->format('Y-m-d');

            if (Attendance::where('AttendanceDate', $today)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'work day already started.',
                    'data'    => null
                ], 400);
            }

            $employees = Employee::all();

            if ($employees->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employees found.',
                    'data'    => null
                ], 404);
            }

            $attendance = [];
            foreach ($employees as $employee) {
                Attendance::create([
                    'EmployeeID' => $employee->EmployeeID,
                    'AttendanceDate' => $today
                ]);

                $attendance[] = [
                    'EmployeeID' => $employee->EmployeeID,
                    'EmployeeName' => $employee->FullName,
                    'AttendanceDate' => $today
                ];
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'started work day successfully.',
                'data'    => $attendance
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'failed to start work day.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function getAttendance(Request $request)
    {
        try {
            $attendance = Attendance::with('employee')->find($request->id);
            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'attendance not found.',
                    'data'    => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'attendance retrieved successfully.',
                'data'    => $attendance
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'failed to get attendance.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function updateAttendance(Request $request)
    {
        $attendance = Attendance::find($request->id);
        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'attendance not found.',
                'data'    => null
            ], 404);
        }

        DB::beginTransaction();
        try {
            $attendance->update([
                'CheckInTime' => $request->CheckInTime ? $request->CheckInTime : $attendance->CheckInTime,
                'CheckOutTime' => $request->CheckOutTime ? $request->CheckOutTime : $attendance->CheckOutTime
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'attendance updated successfully.',
                'data'    => $attendance
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'failed to update attendance.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function checkIn(Request $request)
    {
        DB::beginTransaction();
        try {
            $today = Carbon::now()->format('Y-m-d');
            $attendance = Attendance::where('EmployeeID', $request->EmployeeID)
                ->where('AttendanceDate', $today)
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found in attendance list.',
                    'data'    => null
                ], 404);
            }

            if ($attendance->CheckInTime) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee already checked in.',
                    'data'    => null
                ], 400);
            }

            $attendance->update([
                'CheckInTime' => Carbon::now()->format('H:i:s')
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Employee checked in successfully.',
                'data'    => $attendance
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to check in.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function checkOut(Request $request)
    {
        DB::beginTransaction();
        try {
            $today = Carbon::now()->format('Y-m-d');
            $attendance = Attendance::where('EmployeeID', $request->EmployeeID)
                ->where('AttendanceDate', $today)
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found in attendance list.',
                    'data'    => null
                ], 404);
            }

            if ($attendance->CheckOutTime) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee already checked out.',
                    'data'    => null
                ], 400);
            }

            $attendance->update([
                'CheckOutTime' => Carbon::now()->format('H:i:s')
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Employee checked out successfully.',
                'data'    => $attendance
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to check out.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
