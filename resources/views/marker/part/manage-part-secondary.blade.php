@extends('layouts.index')

@section('custom-link')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="card card-sb">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold">
                    <i class="fa fa-cog fa-sm"></i> Atur Part Secondary
                </h5>
                <a href="{{ route('part') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-reply"></i> Kembali ke Part
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="#" method="post">
                <div class="row">
                    <input type="hidden" class="form-control form-control-sm" name="id" id="id"
                        value="{{ $part->id }}" readonly>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label><small><b>Kode Part</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="kode" id="kode"
                                value="{{ $part->kode }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label><small><b>No. WS</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="ws" id="ws"
                                value="{{ $part->act_costing_ws }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label><small><b>Buyer</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="buyer" id="buyer"
                                value="{{ $part->buyer }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label><small><b>Style</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="style" id="style"
                                value="{{ $part->style }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label><small><b>Color</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="color" id="color"
                                value="{{ $part->color }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label><small><b>Panel</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="panel" id="panel"
                                value="{{ $part->panel }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label><small><b>Parts</b></small></label>
                            <input type="text" class="form-control form-control-sm" name="part_details" id="part_details"
                                value="{{ $part->part_details }}" readonly>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12 mb-3">
            <div class="card card-primary h-100">
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
                                    <select class="form-control select2bs4" id="txtpart" name="txtpart"
                                        style="width: 100%;">
                                        <option selected="selected" value="">Pilih Part</option>
                                        @foreach ($data_part as $datapart)
                                            <option value="{{ $datapart->id }}">
                                                {{ $datapart->nama_part . ' - ' . $datapart->bag }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="mb-4">
                                    <label><small><b>Cons</b></small></label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name="txtcons" id="txtcons">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="border-radius: 0 3px 3px 0;">METER</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="mb-4">
                                    <label><small><b>Tujuan</b></small></label>
                                    <select class="form-control select2bs4" id="cbotuj" name="cbotuj"
                                        style="width: 100%;" onchange="getproses();">
                                        <option selected="selected" value="">Pilih Tujuan</option>
                                        @foreach ($data_tujuan as $datatujuan)
                                            <option value="{{ $datatujuan->isi }}">
                                                {{ $datatujuan->tampil }}
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
                                            <select class="form-control select2bs4 w-100" id="cboproses" name="cboproses" style="width: 100%;">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="mb-4">
                                            <label><small><b>&nbsp</b></small></label>
                                            <button type="button" class="btn btn-block bg-primary" name="simpan" id="simpan" onclick="simpan_data();"><i class="fa fa-save"></i></button>
                                            {{-- <input type="button" class="btn bg-primary w-100" name="simpan" id="simpan" value="Simpan" onclick="simpan_data();"> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table id="datatable_list_part" class="table table-bordered table-sm w-100">
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
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- Page specific script -->
    <script>
        //Initialize Select2 Elements
        $('.select2').select2()

        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

        cleardata();
        dataTableReload();

        function cleardata() {
            $("#cboproses").val('').trigger('change');
            $("#cbotuj").val('').trigger('change');
            $("#txtpart").val('').trigger('change');
            $("#txtcons").val('').trigger('change');
        }

        function getproses() {
            let cbotuj = document.form.cbotuj.value;
            let html = $.ajax({
                type: "GET",
                url: '{{ route('get_proses') }}',
                data: {
                    cbotuj: cbotuj
                },
                async: false
            }).responseText;

            console.log(html != "");

            if (html != "") {
                $("#cboproses").html(html);
            }
        };

        function dataTableReload() {
            let datatable = $("#datatable_list_part").DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                paging: false,
                destroy: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('datatable_list_part') }}',
                    dataType: 'json',
                    dataSrc: 'data',
                    data: function(d) {
                        d.id = $('#id').val();
                    },
                },
                columns: [{
                        data: 'nama_part',
                    },
                    {
                        data: 'cons',
                    },
                    {
                        data: 'unit',
                    },
                    {
                        data: 'tujuan',
                    },
                    {
                        data: 'proses',
                    },
                ],
                columnDefs: [
                    // {
                    //     targets: [5],
                    //     render: (data, type, row, meta) => {
                    //         return `<div class='d-flex gap-1 justify-content-center'> <a class='btn btn-warning btn-sm' href='{{ route('create-dc-in') }}/` +
                    //             row.no_form +
                    //             `' data-bs-toggle='tooltip'><i class='fas fa-qrcode'></i></a> </div>`;
                    //     }
                    // },
                    // {
                    //     targets: [1],
                    //     render: (data, type, row, meta) => {
                    //         return `
                    //         <input type="text" class="form-control" style="width:auto" name="txtcons" id="txtcons">`;
                    //     }
                    // },
                ]
            });
        }

        function simpan_data() {
            let id = document.getElementById("id").value;
            let cbotuj = document.form.cbotuj.value;
            let txtpart = document.form.txtpart.value;
            let txtcons = document.form.txtcons.value;
            let cboproses = document.form.cboproses.value;
            $.ajax({
                type: "post",
                url: '{{ route('store_part_secondary') }}',
                data: {
                    id: id,
                    cbotuj: cbotuj,
                    txtpart: txtpart,
                    txtcons: txtcons,
                    cboproses: cboproses
                },
                success: function(response) {
                    if (response.icon == 'salah') {
                        iziToast.warning({
                            message: response.msg,
                            position: 'topCenter'
                        });
                    } else {
                        iziToast.success({
                            message: response.msg,
                            position: 'topCenter'
                        });
                    }
                    dataTableReload();
                    cleardata();
                },
                // error: function(request, status, error) {
                //     alert(request.responseText);
                // },
            });
        };
    </script>
@endsection
