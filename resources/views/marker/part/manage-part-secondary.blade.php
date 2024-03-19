@extends('layouts.index')

@section('content')
    <div class="card card-sb">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold">
                    <i class="fa fa-plus-circle fa-sm"></i> Atur Part Secondary
                </h5>
                <a href="{{ route('part') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-reply"></i> Kembali ke Part
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="#" method="post">
                <div class="row">
                    <input type="hidden" class="form-control form-control-sm" name="id" id="id" value="{{ $part->id }}" readonly>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label><small><b>Kode Part</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="kode" id="kode" value="{{ $part->kode }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label><small><b>No. WS</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="ws" id="ws" value="{{ $part->act_costing_ws }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label><small><b>Buyer</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="buyer" id="buyer" value="{{ $part->buyer }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label><small><b>Style</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="style" id="style" value="{{ $part->style }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label><small><b>Color</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="color" id="color" value="{{ $part->color }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label><small><b>Panel</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="panel" id="panel" value="{{ $part->panel }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label><small><b>Parts</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="part_details" id="part_details" value="{{ $part->part_details }}" readonly>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12 mb-3">
            <div class="card card-info h-100">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="card-title fw-bold">
                                <i class="fa fa-list fa-sm"></i> Tambah Part Secondary :
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" id="store-secondary" name='form'>
                        <div class="row">
                            <div class="col-6 col-md-3">
                                <div class="mb-4">
                                    <label><small><b>Part</b></small></label>
                                    <select class="form-control select2bs4" id="part" name="part" style="width: 100%;">
                                        <option selected="selected" value="">Pilih Part</option>
                                        @foreach ($masterParts as $part)
                                            <option value="{{ $part->kode }}">
                                                {{ $part->nama_part . ' - ' . $part->bagian }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="mb-4">
                                    <label><small><b>Cons</b></small></label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name="cons" id="cons">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="border-radius: 0 3px 3px 0;">METER</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="mb-4">
                                    <label><small><b>Tujuan</b></small></label>
                                    <select class="form-control select2bs4" id="tujuan" name="tujuan" style="width: 100%;" onchange="getProses();">
                                        <option selected="selected" value="">Pilih Tujuan</option>
                                        @foreach ($masterTujuan as $tujuan)
                                            <option value="{{ $tujuan->kode }}">
                                                {{ $tujuan->tujuan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="row align-items-end">
                                    <div class="col-9">
                                        <div class="mb-4">
                                            <label><small><b>Proses</b></small></label>
                                            <select class="form-control select2bs4 w-100" id="proses" name="proses" style="width: 100%;">
                                                <option selected="selected" value="">Pilih Proses</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="mb-4">
                                            <label><small><b>&nbsp;</b></small></label>
                                            <button type="button" class="btn btn-block bg-primary" name="save" id="save">
                                                <i class="fa fa-save"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table id="datatable-part-list" class="table table-bordered table-sm w-100">
                            <thead>
                                <tr>
                                    <th>Part</th>
                                    <th>Cons</th>
                                    <th>Satuan</th>
                                    <th>Tujuan</th>
                                    <th>Proses</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-script')
    <script>
        // Initial Function
        document.addEventListener("DOMContentLoaded", () => {
            clearData();
            dataTableReload();
        });

        document.getElementById("save").addEventListener("click", () => {
            save();
        });

        // Reset Form
        function clearData() {
            $("#proses").val('').trigger('change');
            $("#tujuan").val('').trigger('change');
            $("#part").val('').trigger('change');
            $("#cons").val('').trigger('change');
        }

        // Build Datatable
        function dataTableReload() {
            let datatable = $("#datatable-part-list").DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                paging: false,
                destroy: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('show-part-list') }}',
                    dataType: 'json',
                    dataSrc: 'data',
                    data: function(d) {
                        d.kode = $('#kode').val();
                    },
                },
                columns: [
                    {
                        data: 'nama_part',
                    },
                    {
                        data: 'cons_part',
                    },
                    {
                        data: 'unit_cons_part',
                    },
                    {
                        data: 'tujuan',
                    },
                    {
                        data: 'proses',
                    },
                ],
            });
        }

        // Set Proses Options
        function getProses() {
            let options = $.ajax({
                type: "GET",
                url: '{{ route('get-master-secondaries-filter') }}',
                data: {
                    tujuan_kode: document.form.tujuan.value
                },
                async: false
            }).responseText;

            if (options != "") {
                $("#proses").html(options);
            }
        };

        // Save Secondary Parting
        function save() {
            let id = document.getElementById("id").value;
            let kode = document.getElementById("kode").value;
            let tujuan = document.form.tujuan.value;
            let part = document.form.part.value;
            let cons = document.form.cons.value;
            let proses = document.form.proses.value;
            $.ajax({
                type: "post",
                url: '{{ route('store-part-secondary') }}',
                data: {
                    id: id,
                    kode: kode,
                    tujuan: tujuan,
                    part: part,
                    cons: cons,
                    proses: proses
                },
                success: function(res) {
                    if (res.icon == 'success') {
                        iziToast.success({
                            message: res.message,
                            position: 'topCenter'
                        });
                    } else {
                        iziToast.error({
                            message: res.message,
                            position: 'topCenter'
                        });
                    }

                    clearData();
                    dataTableReload();
                },
            });
        };
    </script>
@endsection
