<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class HomeController extends Controller
{
    //// HỖ TRỢ BẢO LÃNH TỘI PHẠM VIEW
    public function viewCriminalBail()
    {
        return view('partials.criminal_bail');
    }
    //// HỒ SƠ HỖ TRỢ XỬ ÁN VIEW
    public function viewProcRecords()
    {
        return view('partials.proc_records');
    }
    //// HỒ SƠ HỖ TRỢ TRUY NÃ VIEW
    public function viewWantedSupport()
    {
        return view('partials.wanted_support');
    }
    //// ĐƠN XIN NGHỈ PHÉP VIEW
    public function viewTakeLeave()
    {
        return view('partials.take_leave');
    }
    public function viewEmployeeTable()
    {
        $cucTruong = Employee::with(['user', 'position', 'rank'])
            ->where('position_id', 9)
            ->get();

        $phoCucTruong = Employee::with(['user', 'position', 'rank'])
            ->where('position_id', 8)
            ->get();

        $troLy = Employee::with(['user', 'position', 'rank'])
            ->where('position_id', 7)
            ->get();

        $thuKy = Employee::with(['user', 'position', 'rank'])
            ->where('position_id', 6)
            ->get();


        return view('employee_table', compact(
            'cucTruong',
            'phoCucTruong',
            'troLy',
            'thuKy',
        ));
    }
}
