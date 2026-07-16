<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #FAF7F2;
            color: #1E293B;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .wrapper {
            width: 100%;
            background-color: #FAF7F2;
            padding: 40px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(15, 76, 92, 0.05);
            overflow: hidden;
            border: 1px solid #E2E8F0;
        }
        .header {
            background-color: #ffffff;
            padding: 24px;
            text-align: center;
            border-bottom: 4px solid #0F4C5C;
        }
        .header-logo {
            color: #0F4C5C;
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0;
        }
        .header-sub {
            color: #E36414;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .content {
            padding: 32px 24px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 8px;
            color: #0F172A;
        }
        .intro-text {
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
        }
        .property-card {
            background-color: #FAF7F2;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 28px;
        }
        .property-title {
            font-size: 16px;
            font-weight: 700;
            color: #0F172A;
            margin-top: 0;
            margin-bottom: 6px;
            line-height: 1.4;
        }
        .property-price {
            font-size: 18px;
            font-weight: 800;
            color: #0F4C5C;
            margin-bottom: 14px;
        }
        .property-specs {
            width: 100%;
            border-collapse: collapse;
        }
        .property-specs td {
            padding: 5px 0;
            font-size: 13px;
            color: #475569;
        }
        .property-specs td.label {
            font-weight: 700;
            color: #1E293B;
            width: 40%;
        }
        .btn-container {
            text-align: center;
            margin-top: 24px;
        }
        .btn {
            display: inline-block;
            background-color: #E36414;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 32px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(227, 100, 20, 0.2);
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #c9540e;
        }
        .footer {
            background-color: #ffffff;
            border-top: 1px solid #E2E8F0;
            padding: 24px;
            text-align: center;
            font-size: 11px;
            color: #94A3B8;
            line-height: 1.6;
        }
        .footer a {
            color: #0F4C5C;
            text-decoration: underline;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="header-logo">Samarinda Properti GIS</div>
                <div class="header-sub">Sistem Informasi Geografis Marketplace</div>
            </div>
            <div class="content">
                <p class="greeting">Halo {{ $recipient->name }},</p>
                <p class="intro-text">{{ $mailMessage }}</p>
                
                <div class="property-card">
                    <h2 class="property-title">{{ $property->title }}</h2>
                    <div class="property-price">Rp {{ number_format($property->price, 0, ',', '.') }}</div>
                    
                    <table class="property-specs">
                        <tr>
                            <td class="label">Tipe Properti</td>
                            <td>: {{ $property->type }}</td>
                        </tr>
                        <tr>
                            <td class="label">Kamar Tidur</td>
                            <td>: {{ $property->bedroom }} KT</td>
                        </tr>
                        <tr>
                            <td class="label">Kamar Mandi</td>
                            <td>: {{ $property->bathroom }} KM</td>
                        </tr>
                        <tr>
                            <td class="label">Luas Bangunan</td>
                            <td>: {{ $property->building_area }} m²</td>
                        </tr>
                        <tr>
                            <td class="label">Luas Tanah</td>
                            <td>: {{ $property->land_area }} m²</td>
                        </tr>
                    </table>
                </div>

                <div class="btn-container">
                    <a href="{{ $url }}" class="btn">Lihat Detail Properti</a>
                </div>
            </div>
            <div class="footer">
                <p>Email ini dikirim secara otomatis oleh Samarinda Properti GIS.<br>
                Jika Anda tidak ingin menerima pemberitahuan properti seperti ini lagi, Anda dapat <a href="{{ route('favorites.index') }}">menghentikan langganan alert properti</a> di akun Anda.</p>
                <p>&copy; {{ date('Y') }} Samarinda Properti GIS. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </div>
</body>
</html>
