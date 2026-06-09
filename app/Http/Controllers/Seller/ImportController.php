<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyAlert;
use App\Services\SimpleXlsxReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function show(): View
    {
        return view('seller.import');
    }

    public function downloadTemplate(): Response
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_impor_properti.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $csvContent = chr(0xEF).chr(0xBB).chr(0xBF). // BOM UTF-8
            "judul,tipe,deskripsi,harga,luas_tanah,luas_bangunan,kamar_tidur,kamar_mandi,latitude,longitude\n".
            "Rumah Cantik Minimalis Samarinda Ulu,Rumah,Rumah 2 lantai siap huni dekat dengan pusat kota dan bebas banjir.,750000000,120,90,3,2,-0.494231,117.141203\n".
            "Tanah Kavling Strategis Sambutan,Tanah,Tanah kavling matang siap bangun ruko atau hunian pribadi.,250000000,150,0,0,0,-0.508124,117.189531\n";

        return response($csvContent, 200, $headers);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'max:5120'],
        ]);

        $file = $request->file('csv_file');
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, ['csv', 'txt', 'xlsx', 'xls'], true)) {
            return redirect()->back()->withErrors(['csv_file' => 'Format file harus berupa CSV atau Excel (.xlsx).']);
        }

        $rows = [];
        $errors = [];

        if ($extension === 'xlsx') {
            try {
                $rawRows = SimpleXlsxReader::parse($file->getRealPath());
                if (empty($rawRows)) {
                    return redirect()->back()->withErrors(['csv_file' => 'File Excel kosong atau tidak valid.']);
                }

                // First row is headers
                $rawHeaders = array_shift($rawRows);
                $headers = array_map(function ($h) {
                    return strtolower(trim((string) $h));
                }, $rawHeaders);

                // Required headers verification
                $required = ['judul', 'tipe', 'harga', 'luas_tanah', 'latitude', 'longitude'];
                $missing = [];
                foreach ($required as $req) {
                    if (! in_array($req, $headers, true)) {
                        $missing[] = $req;
                    }
                }

                if (! empty($missing)) {
                    return redirect()->back()->withErrors([
                        'csv_file' => 'Kolom wajib berikut tidak ditemukan di Excel: '.implode(', ', $missing),
                    ]);
                }

                $lineNum = 1;
                foreach ($rawRows as $data) {
                    $lineNum++;

                    // Skip empty rows
                    if (empty(array_filter($data, fn ($v) => trim((string) $v) !== ''))) {
                        continue;
                    }

                    // Pad or slice data to match headers length
                    if (count($data) < count($headers)) {
                        $data = array_pad($data, count($headers), '');
                    } elseif (count($data) > count($headers)) {
                        $data = array_slice($data, 0, count($headers));
                    }

                    // Combine row values with headers
                    $row = array_combine($headers, $data);
                    $row = array_map('trim', $row);

                    // Validation rules for this specific row
                    $validator = Validator::make($row, [
                        'judul' => ['required', 'string', 'max:150'],
                        'tipe' => ['required', 'string', 'in:Rumah,Tanah,Ruko,Apartemen'],
                        'deskripsi' => ['nullable', 'string', 'max:2000'],
                        'harga' => ['required', 'numeric', 'min:0'],
                        'luas_tanah' => ['required', 'integer', 'min:1'],
                        'luas_bangunan' => ['nullable', 'integer', 'min:0'],
                        'kamar_tidur' => ['nullable', 'integer', 'min:0'],
                        'kamar_mandi' => ['nullable', 'integer', 'min:0'],
                        'latitude' => ['required', 'numeric', 'between:-90,90'],
                        'longitude' => ['required', 'numeric', 'between:-180,180'],
                    ]);

                    if ($validator->fails()) {
                        foreach ($validator->errors()->all() as $msg) {
                            $errors[] = "Baris {$lineNum}: {$msg}";
                        }

                        continue;
                    }

                    $rows[] = $row;
                }
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['csv_file' => 'Gagal membaca file Excel: '.$e->getMessage()]);
            }
        } else {
            // CSV parser
            $path = $file->getRealPath();
            if (($handle = fopen($path, 'r')) !== false) {
                // Read UTF-8 BOM if present
                $bom = fread($handle, 3);
                if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                    rewind($handle);
                }

                // Detect delimiter dynamically (comma vs semicolon)
                $firstLine = fgets($handle);
                $commaCount = substr_count($firstLine, ',');
                $semicolonCount = substr_count($firstLine, ';');
                $delimiter = $commaCount >= $semicolonCount ? ',' : ';';

                rewind($handle);
                if ($bom === chr(0xEF).chr(0xBB).chr(0xBF)) {
                    fread($handle, 3); // skip BOM again
                }

                $headers = fgetcsv($handle, 1000, $delimiter);
                if (! $headers) {
                    fclose($handle);

                    return redirect()->back()->withErrors(['csv_file' => 'File CSV kosong atau tidak memiliki header.']);
                }

                // Normalize header names to lowercase and trim spaces
                $headers = array_map(function ($h) {
                    return strtolower(trim($h));
                }, $headers);

                // Required headers verification
                $required = ['judul', 'tipe', 'harga', 'luas_tanah', 'latitude', 'longitude'];
                $missing = [];
                foreach ($required as $req) {
                    if (! in_array($req, $headers, true)) {
                        $missing[] = $req;
                    }
                }

                if (! empty($missing)) {
                    fclose($handle);

                    return redirect()->back()->withErrors([
                        'csv_file' => 'Kolom wajib berikut tidak ditemukan di CSV: '.implode(', ', $missing),
                    ]);
                }

                $lineNum = 1;
                while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
                    $lineNum++;

                    // Skip empty lines
                    if (count($data) === 1 && empty($data[0])) {
                        continue;
                    }

                    // Pad or slice data to match headers length
                    if (count($data) < count($headers)) {
                        $data = array_pad($data, count($headers), '');
                    } elseif (count($data) > count($headers)) {
                        $data = array_slice($data, 0, count($headers));
                    }

                    // Combine row values with headers
                    $row = array_combine($headers, $data);
                    $row = array_map('trim', $row);

                    // Validation rules for this specific row
                    $validator = Validator::make($row, [
                        'judul' => ['required', 'string', 'max:150'],
                        'tipe' => ['required', 'string', 'in:Rumah,Tanah,Ruko,Apartemen'],
                        'deskripsi' => ['nullable', 'string', 'max:2000'],
                        'harga' => ['required', 'numeric', 'min:0'],
                        'luas_tanah' => ['required', 'integer', 'min:1'],
                        'luas_bangunan' => ['nullable', 'integer', 'min:0'],
                        'kamar_tidur' => ['nullable', 'integer', 'min:0'],
                        'kamar_mandi' => ['nullable', 'integer', 'min:0'],
                        'latitude' => ['required', 'numeric', 'between:-90,90'],
                        'longitude' => ['required', 'numeric', 'between:-180,180'],
                    ]);

                    if ($validator->fails()) {
                        foreach ($validator->errors()->all() as $msg) {
                            $errors[] = "Baris {$lineNum}: {$msg}";
                        }

                        continue;
                    }

                    $rows[] = $row;
                }

                fclose($handle);
            } else {
                return redirect()->back()->withErrors(['csv_file' => 'Gagal membuka file CSV.']);
            }
        }

        if (! empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['csv_errors' => $errors]);
        }

        if (empty($rows)) {
            return redirect()->back()->withErrors(['csv_file' => 'Tidak ada data properti yang valid untuk diimpor.']);
        }

        // Perform import inside database transaction
        DB::beginTransaction();
        try {
            $isPgsql = DB::getDriverName() === 'pgsql';
            $userId = auth()->id();

            foreach ($rows as $row) {
                $lat = (float) $row['latitude'];
                $lng = (float) $row['longitude'];

                $property = Property::query()->create([
                    'user_id' => $userId,
                    'type' => $row['tipe'],
                    'title' => $row['judul'],
                    'description' => $row['deskripsi'] ?? null,
                    'price' => $row['harga'],
                    'land_area' => $row['luas_tanah'],
                    'building_area' => isset($row['luas_bangunan']) && $row['luas_bangunan'] !== '' ? (int) $row['luas_bangunan'] : 0,
                    'bedroom' => isset($row['kamar_tidur']) && $row['kamar_tidur'] !== '' ? (int) $row['kamar_tidur'] : 0,
                    'bathroom' => isset($row['kamar_mandi']) && $row['kamar_mandi'] !== '' ? (int) $row['kamar_mandi'] : 0,
                    'status' => 'Tersedia',
                    'geom' => $isPgsql
                        ? DB::raw("ST_SetSRID(ST_MakePoint({$lng}, {$lat}), 4326)")
                        : "POINT({$lng} {$lat})",
                ]);

                PropertyAlert::checkAndNotify($property);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['csv_file' => 'Terjadi kesalahan sistem saat menyimpan data: '.$e->getMessage()]);
        }

        return redirect()->route('seller.listings.index')
            ->with('success', count($rows).' properti berhasil diimpor secara massal.');
    }
}
