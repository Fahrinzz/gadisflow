<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;

class CompanySettingController extends Controller
{
    public function edit()
    {
        $settings = CompanySetting::current();

        return view('settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'reg_no' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'email' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'bank_info' => 'nullable|string',
            'default_terms' => 'nullable|string',
            'next_number' => 'required|integer|min:1',
        ]);

        CompanySetting::current()->update($data);

        return redirect()->route('settings.edit')
            ->with('status', 'Company settings updated successfully.');
    }
}
