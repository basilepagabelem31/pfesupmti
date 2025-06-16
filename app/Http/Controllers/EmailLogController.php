<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;

class EmailLogController extends Controller
{
    public function index()
    {
        $logs=EmailLog::with('user','template')->orderBy('created_at','desc')->paginate('5');
        
        return view('admin.email_logs.index',compact('logs'));
    }
}