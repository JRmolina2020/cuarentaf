<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreditNote;
use Illuminate\Support\Facades\DB;

class CreditNoteController extends Controller
{
    public function index()
    {
        return CreditNote::latest()->get();
    }
}
