<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TrashController extends Controller
{
    public function analyzeAi(Request $request)
    {
        // 1. Cek apakah input berasal dari file upload biasa atau dari foto webcam (Base64)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageBytes = file_get_contents($file->getRealPath());
            $filename = $file->getClientOriginalName();
        } elseif ($request->has('image_base64')) {
            // Jika input berupa string Base64 dari webcam, bersihkan teks datanya
            $base64Data = $request->input('image_base64');
            $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
            $imageBytes = base64_decode($base64Data);
            $filename = 'webcam_capture.jpg';
        } else {
            return response()->json(['status' => 'error', 'message' => 'Gambar tidak ditemukan.'], 400);
        }

        try {
            // 2. Kirim data gambar ke Flask API (Port 5000)
            // Teks 'image' di bawah ini harus sama dengan request.files['image'] yang ada di kode Flask kamu
            $response = Http::attach('image', $imageBytes, $filename)
                            ->post('http://127.0.0.1:5000/predict');

            // 3. Jika Flask berhasil memberikan jawaban, teruskan hasilnya ke Blade dalam bentuk JSON
            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['status' => 'error', 'message' => 'Gagal mendapatkan respon dari Server AI.'], 500);

        } catch (\Exception $e) {
            // Menangkap error jika server Flask belum dinyalakan
            return response()->json(['status' => 'error', 'message' => 'Koneksi ke Server AI terputus: ' . $e->getMessage()], 500);
        }
    }
}
