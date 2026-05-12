<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetPrintController extends Controller
{
    public function show(Asset $asset)
    {
        // QR Kod içeriği: Cihaz detay sayfası veya sadece seri no/ID
        // Şimdilik sistem içindeki düzenleme sayfasını veya sadece temel bilgileri içeren bir string yapalım.
        // Daha sonra bir 'Kamuya açık envanter sorgulama' sayfası eklenirse oraya yönlendirilebilir.
        $qrData = route('filament.admin.resources.assets.edit', $asset);
        
        $qrCode = QrCode::size(150)->generate($qrData);

        return view('admin.assets.print-qr', compact('asset', 'qrCode'));
    }
}
