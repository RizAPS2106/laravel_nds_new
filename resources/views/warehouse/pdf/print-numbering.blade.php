<!DOCTYPE html>
<html>
<head>
    <title>Stocker</title>
    <style>
        @page { margin: 0.5px; }

        body { margin: 0.5px; }

        * {
            font-size: 4.5px;
        }

        img {
            width: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td, table th{
            text-align: left;
            vertical-align: top;
            padding: 0.3px;
            width: 100%;
        }
    </style>
</head>
<body>
    @foreach ($dataNumbering as $numbering)
        <table style="{{ $loop->last ? '' : 'page-break-after: always;' }}">
            <tr>
                <td>{{ $numbering['kode'] }}</td>
                <td rowspan="6" style="vertical-align: middle; text-align: center;">
                    <img src="data:image/png;base64, {!! $qrCode[$loop->index] !!}">
                </td>
            </tr>
            <tr>
                <td>{{ $numbering['no_cut_size'] }}</td>
            </tr>
            <tr>
                <td>{{ $ws }}</td>
            </tr>
            <tr>
                <td>{{ $color }}</td>
            </tr>
            <tr>
                <td>{{ $kode }}</td>
            </tr>
            <tr>
                <td>{{ $numbering['size'] }}</td>
            </tr>
        </table>
    @endforeach
</body>
</html>
