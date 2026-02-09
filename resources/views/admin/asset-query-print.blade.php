<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envanter Sorgulama Raporu - {{ $anabirim }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3b82f6;
        }
        
        .header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 10px;
        }
        
        .header .meta {
            font-size: 14px;
            color: #666;
        }
        
        .filter-info {
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .filter-info h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #1e40af;
        }
        
        .filter-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .filter-info strong {
            color: #1e40af;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }
        
        thead {
            background: #1e40af;
            color: white;
        }
        
        th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        tbody tr:hover {
            background: #f0f9ff;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .badge-gray {
            background: #f3f4f6;
            color: #374151;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        
        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .summary h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #1e40af;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }
            
            @page {
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏥 Hastane BT Destek Sistemi</h1>
        <div class="meta">
            <strong>Envanter Sorgulama Raporu</strong>
        </div>
    </div>

    <div class="filter-info">
        <h3>📋 Filtre Bilgileri</h3>
        <p><strong>Ana Birim:</strong> {{ $anabirim }}</p>
        <p><strong>Model:</strong> {{ $model === 'all' ? 'Tümü' : $model }}</p>
        <p><strong>Yazdırma Tarihi:</strong> {{ $printDate }}</p>
        <p><strong>Toplam Kayıt:</strong> {{ $assets->count() }} adet</p>
    </div>

    @if($assets->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 20%">Cihaz Adı</th>

                    <th style="width: 12%">Marka Model</th>
                    <th style="width: 15%">Alt Birim</th>

                    <th style="width: 9%">Durum</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $index => $asset)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $asset->name }}</strong></td>

                        <td>{{ $asset->model ?? '-' }}</td>
                        <td>{{ $asset->location->altbirim ?? '-' }}</td>

                        <td>
                            @php
                                $statusText = match($asset->status) {
                                    'active' => 'Aktif',
                                    'retired' => 'Hurda',
                                    default => $asset->status
                                };
                                $statusClass = match($asset->status) {
                                    'active' => 'badge-success',
                                    'retired' => 'badge-danger',
                                    default => 'badge-gray'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <h3>📊 Özet İstatistikler</h3>
            <p><strong>Toplam Envanter:</strong> {{ $assets->count() }} adet</p>
            <p><strong>Aktif Envanterler:</strong> {{ $assets->where('status', 'active')->count() }} adet</p>

        </div>
    @else
        <div style="text-align: center; padding: 40px; background: #fef3c7; border-radius: 8px; color: #92400e;">
            <p style="font-size: 16px;">❌ Seçilen kriterlere uygun envanter bulunamadı.</p>
        </div>
    @endif

    <div class="footer">
        <p>Bu rapor <strong>{{ $printDate }}</strong> tarihinde sistemden otomatik olarak oluşturulmuştur.</p>
        <p style="margin-top: 5px; font-size: 11px;">Hastane BT Destek Sistemi - Envanter Yönetimi</p>
    </div>

    <script>
        // Auto-print on load
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
