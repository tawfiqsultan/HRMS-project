<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class EmployeeController extends Controller
{
    public function getAllEmployees()
    {
        $employees = Employee::with('department')->get();

        if ($employees->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No employees found.',
                'data' => null
            ], 404);
        }

        $employees->transform(function ($employee) {
            $employee->department_name = $employee->department ? $employee->department->DepartmentName : null;

            unset($employee->department);
            return $employee;
        });

        return response()->json([
            'success' => true,
            'message' => 'Employees retrieved successfully.',
            'data' => $employees
        ], 200);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function addEmployee(Request $request)
    {
        DB::beginTransaction();
        try {
            $employee = Employee::create([
                'FullName' => $request->FullName,
                'PhoneNumber' => $request->PhoneNumber,
                'Salary'   => $request->Salary,
                'StartDate' => $request->StartDate,
                'DepartmentID' => $request->DepartmentID
            ]);


            $user = User::create([
                'EmployeeID' => $employee->EmployeeID,
                'UserEmail'      => $request->email,
                'UserPassword'   => Hash::make($request->password),
                'role'       => 'employee'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee added successfully.',
                'data'    => $employee
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error adding employee.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function deleteEmployee(Request $request)
    {
        $employee = Employee::find($request->id);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
                'data' => null
            ], 404);
        }

        DB::beginTransaction();
        try {
            $employee->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully.',
                'data' => $employee
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting employee.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //v1 of updateEmployee
    public function updateEmployee(Request $request)
    {
        $employee = Employee::find($request->id);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
                'data' => null
            ], 404);
        }

        DB::beginTransaction();
        try {
            $employee->update([
                'FullName' => $request->FullName,
                'PhoneNumber' => $request->PhoneNumber,
                'Salary'   => $request->Salary,
                'StartDate' => $request->StartDate,
                'DepartmentID' => $request->DepartmentID
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully.',
                'data' => $employee
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating employee.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //v2 of updateEmployee
    // public function updateEmployee(Request $request)
    // {
    //     $employee = Employee::find($request->id);
    //     if (!$employee) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Employee not found.',
    //             'data' => null
    //         ], 404);
    //     }

    //     $updateData = [];

    //     if ($request->has('FullName')) {
    //         $updateData['FullName'] = $request->FullName;
    //     }
    //     if ($request->has('PhoneNumber')) {
    //         $updateData['PhoneNumber'] = $request->PhoneNumber;
    //     }
    //     if ($request->has('Salary')) {
    //         $updateData['Salary'] = $request->Salary;
    //     }
    //     if ($request->has('StartDate')) {
    //         $updateData['StartDate'] = $request->StartDate;
    //     }
    //     if ($request->has('DepartmentID')) {
    //         $updateData['DepartmentID'] = $request->DepartmentID;
    //     }

    //     if (empty($updateData)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No data provided for update.',
    //             'data' => null
    //         ], 400);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $employee->update($updateData);
    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Employee updated successfully.',
    //             'data' => $employee
    //         ], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error updating employee.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function getEmployee(Request $request)
    {
        $employee = Employee::with('department')->find($request->id);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
                'data' => null
            ], 404);
        }

        $employee->department_name = $employee->department ? $employee->department->DepartmentName : null;
        unset($employee->department);

        return response()->json([
            'success' => true,
            'message' => 'Employee retrieved successfully.',
            'data' => $employee
        ], 200);
    }
}
