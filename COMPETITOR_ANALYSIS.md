# Fitur Analisis Kompetitor dengan Buffer Zone

## Deskripsi
Fitur ini memungkinkan seller untuk menganalisis properti kompetitor di sekitar listing mereka menggunakan analisis geospasial dengan buffer zone (radius).

## Fitur Utama

### 1. **Analisis Geospasial**
- Menggunakan query `ST_DWithin` (PostgreSQL) untuk mencari properti dalam radius tertentu
- Fallback ke perhitungan Haversine untuk SQLite
- Buffer zone dapat disesuaikan: 500m, 1km, 2km, 3km, 5km

### 2. **Visualisasi Peta**
- **Marker Merah**: Properti Anda (lebih besar)
- **Marker Biru**: Properti kompetitor
- **Circle Buffer**: Visualisasi radius pencarian dengan garis putus-putus
- Popup interaktif dengan detail properti

### 3. **Statistik Kompetitor**
- Total kompetitor dalam radius
- Harga rata-rata kompetitor
- Harga per m² rata-rata
- Posisi harga Anda (di atas/di bawah/kompetitif)

### 4. **Tabel Perbandingan**
- Daftar kompetitor terurut berdasarkan jarak
- Perbandingan harga, luas tanah, harga per m²
- Status properti (Tersedia/Terjual)
- Link ke detail properti

## Cara Menggunakan

1. Login sebagai seller
2. Buka menu **"Analisis Kompetitor"** di sidebar
3. Pilih properti Anda dari dropdown
4. Pilih radius pencarian (default: 1km)
5. Klik **"Analisis Kompetitor"**
6. Lihat hasil analisis:
   - Statistik di bagian atas
   - Peta dengan buffer zone
   - Tabel detail kompetitor

## Teknologi

### Backend
- **PostgreSQL**: Query geospasial `ST_DWithin`, `ST_Distance`, `ST_Contains`
- **SQLite Fallback**: Perhitungan jarak Haversine
- **Laravel Policy**: Authorization untuk memastikan seller hanya bisa analisis properti sendiri

### Frontend
- **Leaflet.js**: Visualisasi peta interaktif
- **OpenStreetMap**: Base map tiles
- **Fetch API**: AJAX request untuk data real-time

## API Endpoint

```
GET /seller/competitor-analysis/{property}?radius={radius_in_meters}
```

**Response:**
```json
{
  "property": {
    "id": 1,
    "title": "Rumah Minimalis",
    "type": "Rumah",
    "price": 500000000,
    "land_area": 100,
    "lat": -0.5,
    "lng": 117.15,
    "price_per_sqm": 5000000
  },
  "competitors": [
    {
      "id": 2,
      "title": "Rumah Modern",
      "price": 450000000,
      "land_area": 90,
      "distance_m": 250.5,
      "lat": -0.501,
      "lng": 117.151,
      "price_per_sqm": 5000000,
      "status": "Tersedia"
    }
  ],
  "statistics": {
    "total_competitors": 5,
    "avg_price": 475000000,
    "min_price": 400000000,
    "max_price": 550000000,
    "avg_land_area": 95,
    "avg_price_per_sqm": 5000000,
    "price_position": "kompetitif",
    "radius_m": 1000
  }
}
```

## File yang Dibuat/Dimodifikasi

### Baru:
- `app/Http/Controllers/Seller/CompetitorAnalysisController.php`
- `resources/views/seller/competitor-analysis.blade.php`
- `COMPETITOR_ANALYSIS.md`

### Dimodifikasi:
- `routes/web.php` - Tambah route competitor analysis
- `resources/views/seller/index.blade.php` - Tambah menu
- `resources/views/seller/create.blade.php` - Tambah menu
- `resources/views/seller/edit.blade.php` - Tambah menu
- `resources/views/seller/profile.blade.php` - Tambah menu

## Manfaat untuk Demo SIG

1. **Menunjukkan Spatial Query**: `ST_DWithin`, `ST_Distance`, `ST_Contains`
2. **Buffer Zone Visualization**: Circle dengan radius dinamis
3. **Spatial Analysis**: Perhitungan jarak, perbandingan lokasi
4. **Practical Use Case**: Analisis pasar properti berbasis lokasi
5. **Interactive Map**: Visualisasi data geospasial yang menarik

## Screenshot Fitur

### Halaman Analisis
- Dropdown pilih properti
- Dropdown pilih radius
- Tombol "Analisis Kompetitor"

### Hasil Analisis
- 4 Card statistik (Total, Harga Rata-rata, Harga/m², Posisi)
- Info properti Anda
- Peta dengan buffer zone dan marker
- Tabel perbandingan detail

## Tips Demo

1. Pastikan ada beberapa properti dengan tipe sama dalam radius
2. Gunakan radius 1-2km untuk hasil optimal
3. Tunjukkan perbedaan marker (merah vs biru)
4. Klik marker untuk melihat popup detail
5. Scroll tabel untuk melihat perbandingan lengkap
6. Highlight posisi harga (kompetitif/di atas/di bawah rata-rata)
