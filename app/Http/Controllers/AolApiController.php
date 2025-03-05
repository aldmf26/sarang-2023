<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AolApiController extends Controller
{
    public function getDataSetApi()
    {
        $redirect_uri = "https://sarang.ptagafood.com/aol";
        $code = "iGPx6ZzdFU0SNd0uk67Q";
        
        return [
            'redirect_uri' => $redirect_uri,
            'code' => $code
        ];
    }
    public function index()
    {
        $data = [
            'title' => 'AOL API',
            'redirect_uri' => $this->getDataSetApi()['redirect_uri']
        ];
        return view('aol.index',$data);
    }

    public function getToken()
    {
        // URL untuk mendapatkan token
        $url = 'https://account.accurate.id/oauth/token';

        // Header Authorization
        $headers = [
            'Authorization' => 'Basic MzM5MzQyOGEtMGM5Ni00ZDJjLTk1NGEtNjA4OTA2Y2IyYmMwOmIzYWJjZTAwZjQyYTgwNzZmMjc4ZWIyOWQ1OGMzYjFk',
        ];

        // Body request
        $data = [
            'grant_type' => 'authorization_code',
            'code' => 'xbkBpaBPVd4qDNM5n28D',
            'redirect_uri' => 'https://sarang.ptagafood.com/aol',
        ];

        try {
            // Kirim HTTP POST request menggunakan Laravel HTTP Client
            $response = Http::withHeaders($headers)
                ->asForm() // Gunakan form-encoded untuk body
                ->post($url, $data);

            // Cek jika request berhasil (status code 200)
            if ($response->successful()) {
                $result = $response->json();
                return response()->json([
                    'status' => 'success',
                    'data' => $result,
                ]);
            }

            // Jika gagal, kembalikan pesan error
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendapatkan token',
                'error' => $response->json(),
            ], $response->status());

        } catch (\Exception $e) {
            // Tangani error jika ada masalah jaringan atau lainnya
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
