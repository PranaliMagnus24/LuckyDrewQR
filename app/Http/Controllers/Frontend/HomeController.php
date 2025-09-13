<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScannerSubmission;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function home()
    {
        return view('frontend.main-index');
    }
  // single exists check (optional)
    public function exists($unique_id)
    {
        $exists = ScannerSubmission::where('unique_id', $unique_id)->exists();
        return response()->json(['exists' => $exists]);
    }

    // bulk exists check - accepts JSON { ids: ['ID-1','ID-2', ...] }
    public function existsBulk(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids)) {
            return response()->json(['error' => 'Invalid payload'], 422);
        }

        $existing = ScannerSubmission::whereIn('unique_id', $ids)->pluck('unique_id')->toArray();

        return response()->json(['existing' => $existing]);
    }

    // Show either form view or details view for the unique_id
    // public function showScannerPage($unique_id)
    // {
    //     $record = ScannerSubmission::where('unique_id', $unique_id)->first();

    //     if ($record) {
    //         return view('frontend.scanner-details', ['record' => $record]);
    //     } else {
    //         return view('frontend.scanner-form', ['unique_id' => $unique_id]);
    //     }
    // }
    public function showScannerPage($unique_id)
{
    $raw = urldecode($unique_id);

    $plainPattern = '/^ID-\d{13}-\d+$/';

    if (preg_match($plainPattern, $raw)) {
        $unique_id = $raw;
    } else {
        $decoded = base64_decode($raw, true);
        if ($decoded !== false && preg_match($plainPattern, $decoded)) {
            $unique_id = $decoded;
        } else {
            $replaced = strtr($raw, '-_', '+/');
            $pad = strlen($replaced) % 4;
            if ($pad > 0) {
                $replaced .= str_repeat('=', 4 - $pad);
            }
            $decoded2 = base64_decode($replaced, true);
            if ($decoded2 !== false && preg_match($plainPattern, $decoded2)) {
                $unique_id = $decoded2;
            } else {
                $unique_id = $raw;
            }
        }
    }

    $record = ScannerSubmission::where('unique_id', $unique_id)->first();

    if ($record) {
        return view('frontend.scanner-details', ['record' => $record]);
    } else {
        return view('frontend.scanner-form', ['unique_id' => $unique_id]);
    }
}


    // POST submit
    // public function submit(Request $request)
    // {
    //     $validated = $request->validate([
    //         'unique_id' => 'required|string|max:255',
    //         'name' => 'required|string|max:255',
    //         'email' => ['required','email','max:255'],
    //         'phone' => 'required|numeric|digits:10',
    //     ]);

    //     $existing = ScannerSubmission::where('unique_id', $validated['unique_id'])->first();
    //     if ($existing) {
    //         if ($request->expectsJson()) {
    //             return response()->json([
    //                 'message' => 'This QR has already been registered.',
    //                 'record' => $existing
    //             ], 409);
    //         } else {
    //             return redirect()->back()->with('error', 'This QR has already been registered.');
    //         }
    //     }

    //     $record = ScannerSubmission::create($validated);

    //     if ($request->expectsJson()) {
    //         return response()->json([
    //             'message' => 'Saved successfully',
    //             'record' => $record
    //         ]);
    //     } else {
    //         return redirect()->route('scanner.show', ['unique_id' => $record->unique_id])
    //             ->with('success', 'Saved successfully');
    //     }
    // }
    public function submit(Request $request)
{
    // validation rules (email and phone must be unique in scanner_submissions)
    $rules = [
        'unique_id' => 'required|string|max:255',
        'name'      => 'required|string|max:255',
        'email'     => ['required', 'email', 'max:255', 'unique:scanner_submissions,email'],
        'phone'     => ['required', 'digits:10', 'unique:scanner_submissions,phone'],
    ];

    // optional custom messages
    $messages = [
        'email.unique' => 'This email is already registered.',
        'phone.unique' => 'This phone number is already registered.',
        'phone.digits' => 'Phone number must be 10 digits.',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    $validated = $validator->validated();

    // ensure only one entry per unique_id
    $existing = ScannerSubmission::where('unique_id', $validated['unique_id'])->first();
    if ($existing) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'This QR has already been registered.',
                'record' => $existing
            ], 409);
        } else {
            return redirect()->back()->with('error', 'This QR has already been registered.');
        }
    }

    // create record
    $record = ScannerSubmission::create($validated);

    if ($request->expectsJson()) {
        return response()->json([
            'message' => 'Saved successfully',
            'record' => $record
        ]);
    } else {
        return redirect()->route('scanner.show', ['unique_id' => $record->unique_id])
            ->with('success', 'Saved successfully');
    }
}

}
