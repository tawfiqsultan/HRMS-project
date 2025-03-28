<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PayrollController extends Controller
{
    public function generatePayroll(Request $request)
    {
        DB::beginTransaction();
        try {
            $employees = Employee::with('department')->get();
            $payroll = [];
            foreach ($employees as $employee) {
                $emp_payroll = Payroll::create([
                    'EmployeeID' => $employee->EmployeeID,
                    'payrollMonth' => $request->payrollMonth,
                    'Base' => $employee->Salary,
                    'Bonus' => request('Bonus') ? request('Bonus') : 0,
                    'Deduction' => 0,
                    'Net' => $employee->Salary + request('Bonus')
                ]);

                $payroll[] = [
                    'ID' => $employee->EmployeeID,
                    'NAME' => $employee->FullName,
                    'DEPAERNENT' => $employee->department ? $employee->department->DepartmentName : null,
                    'SALARY' => $emp_payroll->Base,
                    'BONUS' => $emp_payroll->Bonus,
                    'DEDUCTION' => $emp_payroll->Deduction,
                    'NETSALLARY' => $emp_payroll->Net
                ];
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Payroll generated successfully.',
                'data'    => $payroll
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate payroll.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getPayroll(Request $request)
    {
        try {
            $payroll = Payroll::findOrFail($request->id);
            $employee = Employee::find($payroll->EmployeeID);

            return response()->json([
                'success' => true,
                'message' => 'Payroll retrieved successfully.',
                'data'    => [
                    'employee_name' => $employee->FullName,
                    'salary' => $payroll->Base,
                    'bouns' => $payroll->Bonus,
                    'Deduction' => $payroll->Deduction,
                    'net_salary' => $payroll->Net
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payroll.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getAllPayroll()
    {
        try {
            $payroll = Payroll::all();

            foreach ($payroll as $p) {
                $employee = Employee::with('department')->find($p->EmployeeID);
                $p->department_name = $employee->department ? $employee->department->DepartmentName : null;
                $p->employee_name = $employee->FullName;
            }

            return response()->json([
                'success' => true,
                'message' => 'Payroll retrieved successfully.',
                'data'    => $payroll
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payroll.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function updatePayroll(Request $request)
    {
        DB::beginTransaction();
        try {
            $payroll = Payroll::findOrFail($request->id);
            $payroll->update([
                'Bonus' => $request->Bonus ? $request->Bonus : $payroll->Bonus,
                'Deduction' => $request->Deduction ? $request->Deduction : $payroll->Deduction,
                'Net' => $payroll->Base + ($request->Bonus - $request->Deduction)
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Payroll updated successfully.',
                'data'    => $payroll
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payroll.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getEmployeePayrolls(Request $request)
    {
        try {
            $payrolls = Payroll::where('EmployeeID', $request->id)->get();
            $employee = Employee::find($request->id);

            return response()->json([
                'success' => true,
                'message' => 'Employee payrolls retrieved successfully.',
                'data'    => [
                    'employee_name' => $employee->FullName,
                    'payrolls' => $payrolls
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get employee payrolls.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
