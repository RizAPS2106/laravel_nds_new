<!DOCTYPE html>
<html lang="en">

<table class="table">
    <tr>
        <td colspan='20' style="font-size: 16px;"><b>Laporan Detail Pengeluaran Barcode</b></td>
    </tr>
    <tr>
        <td colspan='20' style="font-size: 12px;">Periode {{ date('d-M-Y', strtotime($from)) }} s/d {{ date('d-M-Y', strtotime($to)) }}
        </td>
    </tr>
    <thead>
        <tr>
            <th>No</th>
            <th>No Bppb</th>
            <th>Tgl Bppb</th>
            <th>No Req</th>
            <th>Tujuan</th>
            <th>No barcode</th>
            <th>No Roll</th>
            <th>No Lot</th>
            <th>Qty Out</th>
            <th>Unit</th>
            <th>Id Item</th>
            <th>Id Jo</th>
            <th>No WS</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Warna</th>
            <th>Ukuran</th>
            <th>Keterangan</th>
            <th>Nama User</th>
            <th>Approve By</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach ($data as $item)
            <tr>
                <td>{{ $no++ }}.</td>
                <td>{{ $item->no_bppb }}</td>
                <td>{{ $item->tgl_bppb }}</td>
                <td>{{ $item->no_req }}</td>
                <td>{{ $item->tujuan }}</td>
                <td>{{ $item->no_barcode }}</td>
                <td>{{ $item->no_roll }}</td>
                <td>{{ $item->no_lot }}</td>
                <td>{{ $item->qty_out }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ $item->id_item }}</td>
                <td>{{ $item->id_jo }}</td>
                <td>{{ $item->ws }}</td>
                <td>{{ $item->goods_code }}</td>
                <td>{{ $item->itemdesc }}</td>
                <td>{{ $item->color }}</td>
                <td>{{ $item->size }}</td>
                <td>{{ $item->remark }}</td>
                <td>{{ $item->username }}</td>
                <td>{{ $item->confirm_by }}</td>

            </tr>
        @endforeach
    </tbody>

</table>

</html>
