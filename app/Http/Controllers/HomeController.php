<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
