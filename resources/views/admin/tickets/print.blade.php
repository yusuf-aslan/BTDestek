<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İş Emri - {{ $ticket->tracking_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 40px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-weight: bold;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .content-table th, .content-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .content-table th {
            background-color: #f2f2f2;
            width: 30%;
        }
        .section-title {
            background-color: #333;
            color: #fff;
            padding: 5px 10px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .footer-signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .print-btn { display: none; }
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn">Yazdır</button>

    <div class="header">
        <h1>{{ $settings->site_title }}</h1>
        <p>TEKNİK SERVİS İŞ EMRİ / TALEP FORMU</p>
    </div>

    <div class="section-title">Talep Bilgileri</div>
    <table class="content-table">
        <tr>
            <th>Takip Numarası</th>
            <td>{{ $ticket->tracking_number }}</td>
        </tr>
        <tr>
            <th>Talep Tarihi</th>
            <td>{{ $ticket->created_at->format('d.m.Y H:i') }}</td>
        </tr>
        <tr>
            <th>Talep Sahibi</th>
            <td>{{ $ticket->name }}</td>
        </tr>
        <tr>
            <th>Bölüm / Oda No</th>
            <td>{{ $ticket->department_room }}</td>
        </tr>
        <tr>
            <th>Dahili / Tel</th>
            <td>{{ $ticket->phone_number }}</td>
        </tr>
        <tr>
            <th>IP Adresi / Hostname</th>
            <td>{{ $ticket->ip_address }} / {{ $ticket->computer_name ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Sorun Detayı</div>
    <table class="content-table">
        <tr>
            <th>Kategori</th>
            <td>{{ $ticket->category->name }}</td>
        </tr>
        <tr>
            <th>Konu</th>
            <td>{{ $ticket->subject }}</td>
        </tr>
        <tr>
            <th>Açıklama</th>
            <td>{{ $ticket->description }}</td>
        </tr>
    </table>

    @if($ticket->status === 'çözüldü')
    <div class="section-title">Çözüm Bilgileri</div>
    <table class="content-table">
        <tr>
            <th>Çözüm Tarihi</th>
            <td>{{ $ticket->resolved_at ? $ticket->resolved_at->format('d.m.Y H:i') : '-' }}</td>
        </tr>
        <tr>
            <th>Teknisyen</th>
            <td>{{ $ticket->resolver->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Çözüm Notu</th>
            <td>{{ $ticket->resolution_note }}</td>
        </tr>
    </table>
    @endif

    <div style="display: flex; justify-content: space-between; margin-top: 80px;">
        <div style="width: 200px; text-align: center; border-top: 1px solid #000; padding-top: 10px;">
            <strong>Teslim Eden / Talep Sahibi</strong><br>
            İmza
        </div>
        <div style="width: 200px; text-align: center; border-top: 1px solid #000; padding-top: 10px;">
            <strong>Teslim Alan / Teknisyen</strong><br>
            İmza
        </div>
    </div>

    <div style="margin-top: 50px; font-size: 10px; color: #666; text-align: center;">
        Bu belge sistem tarafından otomatik olarak oluşturulmuştur. Basım Tarihi: {{ now()->format('d.m.Y H:i') }}
    </div>

    <script>
        // Auto-print if needed
        // window.print();
    </script>
</body>
</html>
