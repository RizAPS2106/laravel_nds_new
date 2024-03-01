<!DOCTYPE html>
<html lang="en">

<table class="table">
    <tr>
        <td colspan='36' style="font-size: 16px;"><b>Laporan Detail Pengeluaran</b></td>
    </tr>
    <tr>
        <td colspan='36' style="font-size: 12px;">Periode {{ date('d-M-Y', strtotime($from)) }} s/d {{ date('d-M-Y', strtotime($to)) }}
        </td>
    </tr>
    <thead>
        <tr>
            <th>No</th>
            <th>Trans #</th>
            <th>No Req</th>
            <th>Tgl. Trans</th>
            <th>Inv #</th>
            <th>Jenis Dok</th>
            <th>Nomor Aju</th>
            <th>Tgl Aju</th>
            <th>Nomor Daftar</th>
            <th>Tgl Daftar</th>
            <th>Tujuan</th>
            <th>Id Item</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Warna</th>
            <th>Ukuran</th>
            <th>Qty BPB</th>
            <th>Qty Good</th>
            <th>Qty Reject</th>
            <th>Satuan</th>
            <th>Berat Bersih</th>
            <th>Keterangan</th>
            <th>Nama User</th>
            <th>Approve By</th>
            <th>WS #</th>
            <th>Style #</th>
            <th>Curr</th>
            <th>Price</th>
            <th>Ws Actual</th>
            <th>Jenis Trans</th>
            <th>Panel</th>
            <th>Color Garment</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach ($data as $item)
            <tr>
                <td>{{ $no++ }}.</td>
                <td>{{ $item->bppbno }}</td>
                <td>{{ $item->bppbno_req }}</td>
                <td>{{ $item->bppbdate }}</td>
                <td>{{ $item->invno }}</td>
                <td>{{ $item->jenis_dok }}</td>
                <td>{{ $item->no_aju }}</td>
                <td>{{ $item->tanggal_aju }}</td>
                <td>{{ $item->bcno }}</td>
                <td>{{ $item->bcdate }}</td>
                <td>{{ $item->supplier }}</td>
                <td>{{ $item->id_item }}</td>
                <td>{{ $item->goods_code }}</td>
                <td>{{ $item->itemdesc }}</td>
                <td>{{ $item->color }}</td>
                <td>{{ $item->size }}</td>
                <td>{{ $item->qty }}</td>
                <td>{{ $item->qty_good }}</td>
                <td>{{ $item->qty_reject }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ $item->berat_bersih }}</td>
                <td>{{ $item->remark }}</td>
                <td>{{ $item->username }}</td>
                <td>{{ $item->confirm_by }}</td>
                <td>{{ $item->ws }}</td>
                <td>{{ $item->styleno }}</td>
                <td>{{ $item->curr }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->idws_act }}</td>
                <td>{{ $item->jenis_trans }}</td>
                <td>{{ $item->nama_panel }}</td>
                <td>{{ $item->color_gmt }}</td>

            </tr>
        @endforeach
    </tbody>

</table>

</html>
