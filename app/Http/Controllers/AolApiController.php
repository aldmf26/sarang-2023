<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AolApiController extends Controller
{
    public function getDataSetApi()
    {
        $redirect_uri = "https://sarang.ptagafood.com/aol";
        $code = "2EQhSaOrGmZqxuusCEkW";
        
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

    public function tesApi()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic NDJmMTJhMTAtMDhkZi00YjkxLWIxZTQtYzQ0NjVkNjg2MDcyOmUxMzM0MTBlYjYzMjU5NjI1NWFkZmJlNWE0OTk5MGZl'
        ])->post('https://account.accurate.id/oauth/token', [
            'code' => $this->getDataSetApi()['code'],
            'redirect_uri' => $this->getDataSetApi()['redirect_uri'],
            'grant_type' => 'authorization_code',
        ]);
        dd($response->json());

        // $client = new \GuzzleHttp\Client();
        // $response = $client->request('POST', 'https://account.accurate.id/oauth/token', [
        //     'headers' => [
        //         'Authorization' => 'Basic ' . base64_encode('NDJmMTJhMTAtMDhkZi00YjkxLWIxZTQtYzQ0NjVkNjg2MDcyOmUxMzM0MTBlYjYzMjU5NjI1NWFkZmJlNWE0OTk5MGZl')
        //     ],
        //     'form_params' => [
        //         'code' => $this->getDataSetApi()['code'],
        //         'grant_type' => 'authorization_code',
        //         'redirect_uri' => $this->getDataSetApi()['redirect_uri']
        //     ]
        // ]);
        // dd(json_decode($response->getBody()->getContents(), true));
    }
}
