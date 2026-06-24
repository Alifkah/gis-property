<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            padding: 32px 24px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .content {
            padding: 32px 24px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 8px;
            color: #0f172a;
        }
        .intro-text {
            font-size: 16px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
        }
        .property-card {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .property-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 8px;
        }
        .property-price {
            font-size: 22px;
            font-weight: 800;
            color: #4f46e5;
            margin-bottom: 16px;
        }
        .property-specs {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .property-specs td {
            padding: 6px 0;
            font-size: 14px;
            color: #475569;
        }
        .property-specs td.label {
            font-weight: 600;
            color: #1e293b;
            width: 40%;
        }
        .btn-container {
            text-align: center;
            margin-top: 24px;
        }
        .btn {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2), 0 2px 4px -1px rgba(79, 70, 229, 0.1);
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #4338ca;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.5;
        }
        .footer a {
            color: #64748b;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Samarinda Properti GIS</h1>
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
                            <td>: {{ $property->bedroom }}</td>
                        </tr>
                        <tr>
                            <td class="label">Kamar Mandi</td>
                            <td>: {{ $property->bathroom }}</td>
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
                <p>Email ini dikirim otomatis oleh Samarinda Properti GIS.<br>
                Jika Anda tidak ingin menerima email seperti ini lagi, silakan masuk ke akun Anda dan kelola preferensi notifikasi.</p>
                <p>&copy; {{ date('Y') }} Samarinda Properti GIS. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
