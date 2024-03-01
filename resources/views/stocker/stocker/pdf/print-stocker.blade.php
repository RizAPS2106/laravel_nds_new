<!DOCTYPE html>
<html>
<head>
    <title>Stocker</title>
    <style>
        @page { margin: 5px; }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: bold;
            src: url({{ storage_path("OpenSans-Bold.ttf") }}) format('truetype');
        }

        body {
            margin: 5px;
            font-family: 'Open Sans', sans-serif;
            font-weight: bold;
        }

        * {
            font-size: 11px;
        }

        img {
            width: 60px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td, table th{
            text-align: left;
            vertical-align: middle;
            padding: 1.5px 3px;
            border: 1px solid;
            width: auto;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    @foreach ($dataStockers as $dataStocker)
        <table class="{{ $loop->index != 0 ? 'page-break' : '' }}">
            <tr>
                <td rowspan="3" style="vertical-align: middle; text-align: center; width: 35%;">
                    <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->generate($dataStocker->id_qr_stocker)) !!}">
                </td>
                <td>Bundle Qty : {{ $dataStocker->bundle_qty }}</td>
            </tr>
            <tr>
                <td>Size : {{ $dataStocker->size }}</td>
            </tr>
            <tr>
                <td>Range : {{ $dataStocker->range_awal." - ".$dataStocker->range_akhir }}</td>
            </tr>
        </table>
        <table style="margin-top: -0.5px;">
            <tr>
                <td colspan="6" style="text-align: center;">Deskripsi Item</td>
            </tr>
            <tr>
                <td style="border: none;border-left: 1px solid; border-top: 1px solid; border-bottom: 1px solid;white-space: nowrap;">Kode Stocker</td>
                <td style="border: none;border-left: none; border-top: 1px solid; border-bottom: 1px solid;text-align: center;width: auto;">:</td>
                <td colspan="4" style="border: none;border-right: 1px solid; border-top: 1px solid; border-bottom: 1px solid;">{{ $dataStocker->id_qr_stocker }}</td>
            </tr>
            <tr>
                <td style="border: none;border-left: 1px solid; border-top: 1px solid; border-bottom: 1px solid;white-space: nowrap;">Worksheet</td>
                <td style="border: none;border-left: none; border-top: 1px solid; border-bottom: 1px solid;text-align: center;width: auto;">:</td>
                <td colspan="4" style="border: none;border-right: 1px solid; border-top: 1px solid; border-bottom: 1px solid;">{{ $dataStocker->act_costing_ws }}</td>
            </tr>
            <tr>
                <td style="border: none;border-left: 1px solid; border-top: 1px solid; border-bottom: 1px solid;white-space: nowrap;">Buyer</td>
                <td style="border: none;border-left: none; border-top: 1px solid; border-bottom: 1px solid;text-align: center;width: auto;">:</td>
                <td colspan="4" style="border: none;border-right: 1px solid; border-top: 1px solid; border-bottom: 1px solid;">{{ $dataStocker->buyer }}</td>
            </tr>
            <tr>
                <td style="border: none;border-left: 1px solid; border-top: 1px solid; border-bottom: 1px solid;white-space: nowrap;">Style</td>
                <td style="border: none;border-left: none; border-top: 1px solid; border-bottom: 1px solid;text-align: center;width: auto;">:</td>
                <td colspan="4" style="border: none;border-right: 1px solid; border-top: 1px solid; border-bottom: 1px solid;">{{ substr($dataStocker->style, 0, 33).(strlen($dataStocker->style) > 33 ? '...' : '') }}</td>
            </tr>
            <tr>
                <td style="border: none;border-left: 1px solid; border-top: 1px solid; border-bottom: 1px solid;white-space: nowrap;">Color</td>
                <td style="border: none;border-left: none; border-top: 1px solid; border-bottom: 1px solid;text-align: center;width: auto;">:</td>
                <td colspan="4" style="border: none;border-right: 1px solid; border-top: 1px solid; border-bottom: 1px solid;">{{ $dataStocker->color }}</td>
            </tr>
            <tr>
                <td style="border: none;border-left: 1px solid; border-top: 1px solid; border-bottom: 1px solid;white-space: nowrap;">Part</td>
                <td style="border: none;border-left: none; border-top: 1px solid; border-bottom: 1px solid;text-align: center;width: auto;">:</td>
                <td colspan="4" style="border: none;border-right: 1px solid; border-top: 1px solid; border-bottom: 1px solid;">{{ $dataStocker->part }}</td>
            </tr>
            <tr>
                <td style="width: 16.5%; border: none;border-left: 1px solid; border-top: 1px solid; border-bottom: 1px solid;white-space: nowrap;">Shade</td>
                <td style="width: 16.5%; border: none;border-left: none; border-top: 1px solid; border-bottom: 1px solid;text-align: center;width: auto;">:</td>
                <td style="width: 16.5%; border: none;border-right: 1px solid; border-top: 1px solid; border-bottom: 1px solid;">{{ $dataStocker->shade }}</td>
                <td style="width: 16.5%; border: none;border-left: 1px solid; border-top: 1px solid; border-bottom: 1px solid;white-space: nowrap;">No. Cut</td>
                <td style="width: 16.5%; border: none;border-left: none; border-top: 1px solid; border-bottom: 1px solid;text-align: center;width: auto;">:</td>
                <td style="width: auto; border: none;border-right: 1px solid; border-top: 1px solid; border-bottom: 1px solid;">{{ $dataStocker->no_cut }}</td>
            </tr>
            <tr>
                <td style="border: none;border-left: 1px solid; border-top: 1px solid; border-bottom: 1px solid;white-space: nowrap;">Country</td>
                <td style="border: none;border-left: none; border-top: 1px solid; border-bottom: 1px solid;text-align: center;width: auto;">:</td>
                <td colspan="4" style="border: none;border-right: 1px solid; border-top: 1px solid; border-bottom: 1px solid;">{{ $dataStocker->dest }}</td>
            </tr>
            <tr>
                <td style="border: none;border-left: 1px solid; border-top: 1px solid; border-bottom: 1px solid;white-space: nowrap;">Note</td>
                <td style="border: none;border-left: none; border-top: 1px solid; border-bottom: 1px solid;text-align: center;width: auto;">:</td>
                <td colspan="4" style="border: none;border-right: 1px solid; border-top: 1px solid; border-bottom: 1px solid;">{{ $dataStocker->notes }}</td>
            </tr>
        </table>
    @endforeach
</body>
</html>
