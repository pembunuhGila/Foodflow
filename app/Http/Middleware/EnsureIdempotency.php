<?php

namespace App\Http\Middleware;

use App\Models\IdempotencyKey;
use Closure;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotency
{
    /**
     * Mencegah pemrosesan ganda pada transaksi penting (Materi Pertemuan 10 - Langkah 8).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('Idempotency-Key');
        if (! $key) {
            return response()->json(['message' => 'Header Idempotency-Key wajib diisi.'], 400);
        }

        $userId = $request->user()?->id ?? 0;
        $hash = hash('sha256', $request->getContent());

        try {
            // Reservasi key: jika sudah ada, unique constraint menolak dan masuk ke catch
            $record = IdempotencyKey::create([
                'user_id' => $userId,
                'key' => $key,
                'request_hash' => $hash,
            ]);
        } catch (UniqueConstraintViolationException) {
            $record = IdempotencyKey::where('user_id', $userId)->where('key', $key)->first();
            if (! $record) {
                return response()->json(['message' => 'Gagal memverifikasi Idempotency-Key.'], 409);
            }

            if ($record->request_hash !== $hash) {
                return response()->json(['message' => 'Key ini sudah dipakai untuk request lain.'], 422);
            }

            if ($record->response_status === null) {
                return response()->json(['message' => 'Request dengan key ini masih diproses.'], 409);
            }

            // Request yang sama: putar ulang hasil lama tanpa memproses transaksi ulang
            return response($record->response_body, $record->response_status)
                ->header('Content-Type', 'application/json')
                ->header('Idempotent-Replayed', 'true');
        }

        $response = $next($request);

        if ($response->isSuccessful()) {
            $record->update([
                'response_status' => $response->getStatusCode(),
                'response_body' => $response->getContent(),
            ]);
        } else {
            // Jika request gagal (bukan 2xx), hapus reservasi key agar klien dapat mencoba lagi
            $record->delete();
        }

        return $response;
    }
}
