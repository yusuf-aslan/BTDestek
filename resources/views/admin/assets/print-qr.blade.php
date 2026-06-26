<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Envanter Etiketi - {{ $asset->name }}</title>
    <style>
        @page {
            size: 80mm 50mm;
            margin: 0;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 5mm;
            width: 80mm;
            height: 50mm;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
        }
        .qr-section {
            width: 35mm;
            height: 35mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .info-section {
            width: 35mm;
            padding-left: 3mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .title {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 2pt;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2pt;
        }
        .detail {
            font-size: 7pt;
            margin-bottom: 1pt;
            color: #475569;
            line-height: 1.2;
        }
        .label {
            font-weight: bold;
            color: #64748b;
        }
        .footer {
            position: absolute;
            bottom: 2mm;
            right: 5mm;
            font-size: 5pt;
            color: #94a3b8;
        }
        .qr-code svg {
            width: 100% !important;
            height: auto !important;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="qr-section">
        <div class="qr-code">
            {!! $qrCode !!}
        </div>
    </div>

    <div class="info-section">
        <div class="title">TOTM ENVANTER</div>
        
        <div class="detail">
            <span class="label">İsim:</span><br>
            {{ Str::limit($asset->name, 25) }}
        </div>

        <div class="detail">
            <span class="label">Tür:</span>
            @php
                $types = [
                    'computer' => 'Bilgisayar',
                    'printer' => 'Yazıcı',
                    'network' => 'Ağ Cihazı',
                    'monitor' => 'Monitör',
                    'tablet' => 'Tablet',
                    'medical' => 'Tıbbi Cihaz',
                    'other' => 'Diğer',
                ];
            @endphp
            {{ $types[$asset->type] ?? $asset->type }}
        </div>

        @if($asset->serial_number)
        <div class="detail">
            <span class="label">S/N:</span>
            {{ $asset->serial_number }}
        </div>
        @endif

        @if($asset->location)
        <div class="detail">
            <span class="label">Birim:</span><br>
            {{ Str::limit($asset->location->anabirim, 20) }}
        </div>
        @endif
    </div>

    <div class="footer">
        ID: #{{ $asset->id }} | BT Destek v1.14
    </div>

</body>
</html>
