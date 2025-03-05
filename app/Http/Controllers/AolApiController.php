<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AolApiController extends Controller
{
    public function getDataSetApi()
    {
        // sudah bisa akses
        $data = [
            'status' => 'success',
            'data' => [
                'access_token' => 'c284c7d1-4f3c-463a-a49a-575d645c3e61',
                'token_type' => 'bearer',
                'refresh_token' => '42bf59ed-0ae0-43e7-8356-eff5c99f558d',
                'expires_in' => 1295999,
                'scope' => 'item_view sales_invoice_view item_save',
                'user' => [
                    'referrer' => null,
                    'name' => 'aldimf26@gmail.com',
                    'nickname' => 'aldimf26@gmail.com',
                    'mobile' => null,
                    'id' => 834608,
                    'email' => 'aldimf26@gmail.com'
                ],
                'redirect_uri' => 'https://sarang.ptagafood.com/aol',
                'code' => 'iGPx6ZzdFU0SNd0uk67Q'
            ]
        ];

        return $data;

    }
    public function index()
    {
        $data = [
            'title' => 'AOL API',
            'redirect_uri' => 'https://sarang.ptagafood.com/aol'
        ];
        return view('aol.index',$data);
    }

    public function read()
    {
        $headers = [
            'Authorization' => 'Basic MzM5MzQyOGEtMGM5Ni00ZDJjLTk1NGEtNjA4OTA2Y2IyYmMwOmIzYWJjZTAwZjQyYTgwNzZmMjc4ZWIyOWQ1OGMzYjFk',
        ];
        $url = "https://account.accurate.id/api/db-list.do";
        $response = Http::withHeaders($headers)
                ->asForm() // Gunakan form-encoded untuk body
                ->get($url);
            // Cek jika request berhasil (status code 200)
            if ($response->successful()) {
                $result = $response->json();
                return response()->json([
                    'status' => 'success',
                    'data' => $result,
                ]);
            }
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
