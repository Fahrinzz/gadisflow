<?php

namespace App\Http\Controllers;

use App\Models\Document;

class DashboardController extends Controller
{
    public function index()
    {
        $counts = [
            'quotation' => Document::where('type', 'quotation')->count(),
            'invoice' => Document::where('type', 'invoice')->count(),
            'delivery_order' => Document::where('type', 'delivery_order')->count(),
        ];

        $unpaidTotal = Document::where('type', 'invoice')
            ->where('status', '!=', 'Paid')
            ->get()
            ->sum(fn ($doc) => $doc->balance);

        $recent = Document::latest()->take(10)->get();

        return view('dashboard', compact('counts', 'unpaidTotal', 'recent'));
    }
}
