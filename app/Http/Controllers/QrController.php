<?php

namespace App\Http\Controllers;

use App\Models\StorageRow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class QrController extends Controller
{
    /**
     * ساخت QR Code برای ردیف انبار
     */
    private function generateQr(StorageRow $row)
    {
        $url = route('rows.show', $row);

        $qr = new QrCode(
            data: $url,
            size: 420,
            margin: 14
        );

        return (new SvgWriter())->write($qr);
    }

    /**
     * نمایش مستقیم تصویر QR
     */
    public function show(StorageRow $row)
    {
        $result = $this->generateQr($row);

        return response($result->getString(), 200, [
            'Content-Type' => 'image/svg+xml; charset=utf-8',
            'Content-Disposition' => 'inline; filename="storage-row-'.$row->id.'.svg"',
            'Cache-Control' => 'private, max-age=300',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * نمایش صفحه جزئیات ردیف همراه QR
     */
    public function detail(StorageRow $row)
    {
        $row->load(
            'rack.aisle.warehouse',
            'inventories.product'
        );

        $result = $this->generateQr($row);

        $qrDataUri = $result->getDataUri();

        return view('structure.row', compact(
            'row',
            'qrDataUri'
        ));
    }
}