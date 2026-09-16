<?php

namespace App\Http\Controllers;

use App\Models\Halal;
use App\Models\Info;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class HalalController extends Controller implements HasMiddleware
{
    private const SUBMIT_THROTTLE_SECONDS = 10;

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        RateLimiter::for('halal-submit', function (Request $request) {
            return Limit::perSecond(1, self::SUBMIT_THROTTLE_SECONDS)->by($request->ip());
        });

        return [
            new Middleware('throttle:halal-submit', only: ['store']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('front-end.halal', [
            'infos' => (new Info())->getInfo(),
            'agencies_footer' => (new Partner())->getAgencies(),
            'submitThrottleSeconds' => self::SUBMIT_THROTTLE_SECONDS,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $textFields = ['nama_pelaku', 'nama_brand', 'tahun_berdiri', 'luas_usaha', 'alamat', 'bahan', 'cara_pembuatan'];
        foreach ($textFields as $field) {
            if (isset($input[$field])) {
                $input[$field] = trim(strip_tags($input[$field]));
            }
        }

        $numericFields = ['nik_ktp', 'nib', 'npwp', 'no_whatsapp', 'modal_awal', 'pendapatan_minggu'];
        foreach ($numericFields as $field) {
            if (isset($input[$field])) {
                $input[$field] = preg_replace('/[^0-9]/', '', $input[$field]);
            }
        }

        if (isset($input['no_whatsapp']) && str_starts_with($input['no_whatsapp'], '0')) {
            $input['no_whatsapp'] = '62' . substr($input['no_whatsapp'], 1);
        }

        if (isset($input['email'])) {
            $input['email'] = strtolower(trim($input['email']));
        }

        $request->merge($input);

        $validated = $request->validate([
            'nama_pelaku'       => 'required|string|min:3|max:255',
            'nama_brand'        => 'required|string|min:3|max:255',
            'nik_ktp'           => 'required|digits:16',
            'nib'               => 'required|digits_between:9,16',
            'npwp'              => 'required|digits_between:1,16',
            'modal_awal'        => 'required|numeric|min:0',
            'tahun_berdiri'     => 'required|string|max:255',
            'luas_usaha'        => 'required|string|max:255',
            'pendapatan_minggu' => 'required|numeric|min:0',
            'no_whatsapp'       => 'required|digits_between:9,17',
            'email'             => 'required|email|max:255',
            'alamat'            => 'required|string|min:5|max:1000',
            'bahan'             => 'required|string|min:5|max:2000',
            'cara_pembuatan'    => 'required|string|min:10|max:5000',
            'konfirmasi'        => 'required|accepted',
        ]);

        Halal::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pengajuan sertifikasi halal Anda berhasil dikirim.',
            ]);
        }

        return redirect()->back()->with('success', 'Data pengajuan sertifikasi halal Anda berhasil dikirim.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
