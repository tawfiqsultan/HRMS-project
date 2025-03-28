<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Http\Requests\StoreDepartmentsRequest;
use App\Http\Requests\UpdateDepartmentsRequest;
use Illuminate\Http\Request;

class DepartmentsController extends Controller
{

    public function getAllDepartments()
    {
        $departments = Department::with('employees', 'manager')->get();

        if ($departments->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No departments found.',
                'data' => null
            ], 404);
        }

        $departments->transform(function ($department) {
            $department->manager_name = $department->manager ? $department->manager->FullName : null;

            $department->employeesCount = count($department->employees);
            unset($department->manager);
            return $department;
        });

        return response()->json([
            'success' => true,
            'message' => 'Departments retrieved successfully.',
            'data' => $departments
        ], 200);
    }

    public function getDepartment(Request $request)
    {
        try {
            $department = Department::with('employees', 'manager')->find($request->id);

            if (!$department) {
                return response()->json([
                    'success' => false,
                    'message' => 'Department not found.',
                    'data' => null
                ], 404);
            }

            $department->manager_name = $department->manager ? $department->manager->FullName : null;
            $department->employeesCount = count($department->employees);
            unset($department->manager);

            return response()->json([
                'success' => true,
                'message' => 'Department retrieved successfully.',
                'data' => $department
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found.',
                'error' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }


    public function addDepartment(Request $request)
    {
        try {
            $department = Department::create([
                'DepartmentName' => $request->DepartmentName,
                'DepartmentDesc' => $request->DepartmentDesc,
                'ManagerID' => $request->ManagerID,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Department added successfully.',
                'data' => $department
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Department not added.',
                'error' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function deleteDepartment(Request $request)
    {
        try {
            $department = Department::find($request->id);
            if (!$department) {
                return response()->json([
                    'success' => false,
                    'message' => 'Department not found.',
                    'data' => null
                ], 404);
            }

            $department->delete();

            return response()->json([
                'success' => true,
                'message' => 'Department deleted successfully.',
                'data' => $department
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Department not deleted.',
                'error' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
