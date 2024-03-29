@extends('layouts.index')

@section('content')
    <div class="card card-sb">
        <div class="card-header">
            <h5 class="card-title fw-bold mb-0"><i class="fas fa-scroll fa-sm"></i> Spreading</h5>
        </div>
        <div class="card-body">
            <a href="{{ route('create-spreading') }}" class="btn btn-success btn-sm mb-3">
                <i class="fas fa-plus"></i>
                Baru
            </a>
            <div class="row justify-content-between align-items-end g-3 mb-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-end gap-3 mb-3">
                        <div>
                            <label class="form-label"><small>Tanggal Awal</small></label>
                            <input type="date" class="form-control form-control-sm" onchange="dataTableReload()" id="tanggal_awal" name="tanggal_awal">
                        </div>
                        <div>
                            <label class="form-label"><small>Tanggal Akhir</small></label>
                            <input type="date" class="form-control form-control-sm" onchange="dataTableReload()" id="tanggal_akhir" name="tanggal_akhir" value="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <button class="btn btn-primary btn-sm" onclick="dataTableReload()"><i class="fa fa-search"></i> </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex justify-content-end align-items-end gap-1 mb-3">
                        <a href="{{ url('manual-form-cut/create') }}" target="_blank" class="btn btn-sm btn-sb"><i class="fas fa-clipboard-list"></i> Manual</a>
                        {{-- <a href="{{ url('pilot-form-cut/create') }}" target="_blank" class="btn btn-sm btn-sb-secondary"><i class="fas fa-clipboard-list"></i> Pilot</a> --}}
                        {{-- <button type="button" onclick="updateNoCut()" class="btn btn-sm btn-sb"><i class="fas fa-sync-alt"></i> Generate No. Cut</button> --}}
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="datatable" class="table table-bordered table-sm w-100">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Tanggal</th>
                            <th>No. Form</th>
                            <th>No. Meja</th>
                            <th>No. Marker</th>
                            <th>Size Ratio</th>
                            <th>Panel</th>
                            <th>Color</th>
                            <th>Style</th>
                            <th>No. WS</th>
                            <th>Status</th>
                            <th>Qty Ply</th>
                            <th>Ket.</th>
                            <th>Plan</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Meja Modal -->
        <div class="modal fade" id="editMejaModal" tabindex="-1" aria-labelledby="editMejaModalLabel" aria-hidden="true">
            <form action="{{ route('update-spreading') }}" method="post" onsubmit="submitForm(this, event)">
                @method('PUT')
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-sb text-light">
                            <h1 class="modal-title fs-5" id="editMejaModalLabel"><i class="fa fa-edit"></i> Edit Meja</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="max-height: 65vh !important;">
                            <div class="row">
                                <input type="hidden" id="edit_id" name="edit_id">
                                <div class="col-12 col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label"><small>No. Meja</small></label>
                                        <select class="form-select form-select-sm select2manual" id="edit_meja_id" name="edit_meja_id">
                                            <option value="">-</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ strtoupper($user->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Tanggal Form</small></label>
                                        <input type="text" class="form-control" id="edit_tanggal" name="edit_tanggal" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><small>No. Form</small></label>
                                        <input type="text" class="form-control" id="edit_no_form" name="edit_no_form" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Marker</small></label>
                                        <input type="text" class="form-control" id="edit_marker_input_kode" name="edit_marker_input_kode" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><small>WS</small></label>
                                        <input type="text" class="form-control" id="edit_act_costing_ws" name="edit_act_costing_ws" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Color</small></label>
                                        <input type="text" class="form-control" id="edit_color" name="edit_color" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Panel</small></label>
                                        <input type="text" class="form-control" id="edit_panel" name="edit_panel" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label"><small>P. Marker</small></label>
                                        <input type="text" class="form-control" id="edit_panjang_marker" name="edit_panjang_marker" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Unit P. Marker</small></label>
                                        <input type="text" class="form-control" id="edit_unit_panjang_marker" name="edit_unit_panjang_marker" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Comma Marker</small></label>
                                        <input type="text" class="form-control" id="edit_comma_marker" name="edit_comma_marker" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Unit Comma Marker</small></label>
                                        <input type="text" class="form-control" id="edit_unit_comma_marker" name="edit_unit_comma_marker" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Lebar Marker</small></label>
                                        <input type="text" class="form-control" id="edit_lebar_marker" name="edit_lebar_marker" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Unit Lebar Marker</small></label>
                                        <input type="text" class="form-control" id="edit_unit_lebar_marker" name="edit_unit_lebar_marker" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><small>PO Marker</small></label>
                                        <input type="text" class="form-control" id="edit_po_marker" name="edit_po_marker" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Gelar QTY</small></label>
                                        <input type="text" class="form-control" id="edit_gelar_qty_marker" name="edit_gelar_qty_marker" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Ply QTY</small></label>
                                        <input type="text" class="form-control" id="edit_qty_ply" name="edit_qty_ply" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Urutan Marker</small></label>
                                        <input type="text" class="form-control" id="edit_urutan_marker" name="edit_urutan_marker" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Cons. Marker</small></label>
                                        <input type="text" class="form-control" id="edit_cons_marker" name="edit_cons_marker" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-6 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Tipe Marker</small></label>
                                        <input type="text" class="form-control" id="edit_tipe_marker" name="edit_tipe_marker" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-6 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Keterangan</small></label>
                                        <textarea class="form-control" id="edit_notes" name="edit_notes" rows="1" readonly></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <table id="datatable-ratio" class="table table-bordered table-striped table-sm w-100">
                                        <thead>
                                            <tr>
                                                <th>Size</th>
                                                <th>Ratio</th>
                                                <th>Qty Cutting</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fa fa-times fa-sm"></i> Tutup</button>
                            <button type="submit" class="btn btn-success"><i class="fa fa-save fa-sm"></i> Simpan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Edit Status Modal -->
        <div class="modal fade" id="editStatusModal" tabindex="-1" aria-labelledby="editStatusModalLabel" aria-hidden="true">
            <form action="{{ route('update-status') }}" method="post" onsubmit="submitForm(this, event)">
                @method('PUT')
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-sb text-light">
                            <h1 class="modal-title fs-5" id="editStatusModalLabel">Edit Status</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="max-height: 65vh !important;">
                            <div class="row">
                                <input type="hidden" id="edit_status_id" name="edit_status_id">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select select2manual" name="edit_status" id="edit_status">
                                            <option value="idle">IDLE</option>
                                            <option value="form">PENGERJAAN FORM CUTTING</option>
                                            <option value="form detail">PENGERJAAN FORM CUTTING DETAIL</option>
                                            <option value="form spreading">PENGERJAAN FORM CUTTING SPREADING</option>
                                            <option value="finish">PENGERJAAN SELESAI</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fa fa-times fa-sm"></i> Tutup</button>
                            <button type="submit" class="btn btn-success"><i class="fa fa-save fa-sm"></i> Simpan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('custom-script')
    <script>
        $(document).ready(() => {
            let oneWeeksBefore = new Date(new Date().setDate(new Date().getDate() - 7));
            let oneWeeksBeforeDate = ("0" + oneWeeksBefore.getDate()).slice(-2);
            let oneWeeksBeforeMonth = ("0" + (oneWeeksBefore.getMonth() + 1)).slice(-2);
            let oneWeeksBeforeYear = oneWeeksBefore.getFullYear();
            let oneWeeksBeforeFull = oneWeeksBeforeYear + '-' + oneWeeksBeforeMonth + '-' + oneWeeksBeforeDate;

            $("#tanggal_awal").val(oneWeeksBeforeFull).trigger("change");

            window.addEventListener("focus", () => {
                dataTableReload();
            });

            $('#edit_meja_id').select2({
                theme: 'bootstrap4',
                dropdownParent: $("#editMejaModal")
            });

            $('#edit_status').select2({
                theme: 'bootstrap4',
                dropdownParent: $("#editStatusModal")
            });
        });

        let datatable = $("#datatable").DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('spreading') }}',
                data: function(d) {
                    d.tanggal_awal = $('#tanggal_awal').val();
                    d.tanggal_akhir = $('#tanggal_akhir').val();
                },
            },
            columns: [
                {
                    data: 'id'
                },
                {
                    data: 'tanggal'
                },
                {
                    data: 'no_form'
                },
                {
                    data: 'name'
                },
                {
                    data: 'marker_input_kode'
                },
                {
                    data: 'marker_details'
                },
                {
                    data: 'panel'
                },
                {
                    data: 'color'
                },
                {
                    data: 'style'
                },
                {
                    data: 'act_costing_ws'
                },
                {
                    data: 'status_form'
                },
                {
                    data: 'ply_progress'
                },
                {
                    data: 'notes'
                },
                {
                    data: 'tanggal_plan'
                },
            ],
            columnDefs: [
                {
                    targets: [5],
                    render: (data, type, row, meta) => {
                        let color = "";

                        if (row.status_form == 'finish') {
                            color = '#087521';
                        } else if (row.status_form == 'marker') {
                            color = '#2243d6';
                        } else if (row.status_form == 'form') {
                            color = '#2243d6';
                        } else if (row.status_form == 'form detail') {
                            color = '#2243d6';
                        } else if (row.status_form == 'form spreading') {
                            color = '#2243d6';
                        }

                        return  "<span style='font-weight: 600; color: "+ color + "' >" + (data ? data.replace(/,/g, '<br>') : '-') + "</span>"
                    }
                },
                {
                    targets: [10],
                    className: "text-center",
                    render: (data, type, row, meta) => {
                        icon = "";

                        switch (data) {
                            case "idle":
                            case "pilot marker":
                            case "pilot form detail":
                                icon = `<i class="fas fa-file fa-lg"></i>`;
                                break;
                            case "marker":
                            case "form":
                            case "form detail":
                            case "form spreading":
                                icon = `<i class="fas fa-sync-alt fa-spin fa-lg" style="color: #2243d6;"></i>`;
                                break;
                            case "finish":
                                icon = `<i class="fas fa-check fa-lg" style="color: #087521;"></i>`;
                                break;
                        }

                        return icon;
                    }
                },
                {
                    targets: [11],
                    render: (data, type, row, meta) => {
                        return `
                            <div class="progress border border-sb position-relative" style="min-width: 50px;height: 21px">
                                <p class="position-absolute" style="top: 50%;left: 50%;transform: translate(-50%, -50%);">`+row.total_ply+`/`+row.qty_ply+`</p>
                                <div class="progress-bar" style="background-color: #75baeb;width: `+((row.total_ply/row.qty_ply)*100)+`%" role="progressbar"></div>
                            </div>
                        `;
                    }
                },
                {
                    targets: [0],
                    render: (data, type, row, meta) => {
                        let btnEditMeja = row.status_form == 'idle' ? "<a href='javascript:void(0);' class='btn btn-primary btn-sm' onclick='editData(" + JSON.stringify(row) + ", \"editMejaModal\", [{\"function\" : \"dataTableRatioReload()\"}]);'><i class='fa fa-edit'></i></a>" : "";
                        let btnEditStatus = row.status_form != 'idle' ? "<a href='javascript:void(0);' class='btn btn-primary btn-sm' onclick='editData(" + JSON.stringify({'status_id' : row.id, 'status' : row.status_form}) + ", \"editStatusModal\", [{\"function\" : \"dataTableRatio1Reload()\"}]);'><i class='fa fa-cog'></i></a>" : "";
                        let btnDelete = row.status_form == 'idle' ? "<a href='javascript:void(0);' class='btn btn-danger btn-sm' data='" + JSON.stringify(row) + "' data-url='"+'{{ route('destroy-spreading') }}'+"/"+row.id+"' onclick='deleteData(this);'><i class='fa fa-trash'></i></a>" : "";
                        let btnProcess = "";

                        if (row.tipe_form == 'manual') {
                            btnProcess = (row.qty_ply > 0 && row.meja_id != '' && row.meja_id != null && row.app == 'y') || row.status_form != 'idle' ?
                                `<a class='btn btn-success btn-sm' href='{{ route('process-manual-form-cut') }}/` + row.id + `' data-bs-toggle='tooltip' target='_blank'><i class='fa `+ (row.status_form == "finish" ? `fa-search-plus` : `fa-plus`) +`'></i></a>` :
                                "";
                        } else if (row.tipe_form == 'pilot') {
                            btnProcess = (row.qty_ply > 0 && row.meja_id != '' && row.meja_id != null && row.app == 'y') || row.status_form != 'idle' ?
                                `<a class='btn btn-success btn-sm' href='{{ route('process-pilot-form-cut') }}/` + row.id + `' data-bs-toggle='tooltip' target='_blank'><i class='fa `+(row.status_form == "finish" ? `fa-search-plus` : `fa-plus`)+`'></i></a>` :
                                "";
                        } else {
                            btnProcess = (row.qty_ply > 0 && row.meja_id != '' && row.meja_id != null && row.app == 'y') || row.status_form != 'idle' ?
                                `<a class='btn btn-success btn-sm' href='{{ route('process-form-cut-input') }}/` + row.id + `' data-bs-toggle='tooltip' target='_blank'><i class='fa `+(row.status_form == "finish" ? `fa-search-plus` : `fa-plus`)+`'></i></a>` :
                                "";
                        }

                        return `<div class='d-flex gap-1 justify-content-center'>` + btnEditMeja + btnEditStatus + btnProcess + btnDelete + `</div>`;
                    }
                },
                {
                    targets: '_all',
                    render: (data, type, row, meta) => {
                        let color = "";

                        if (row.status_form == 'finish') {
                            color = '#087521';
                        } else if (row.status_form == 'marker') {
                            color = '#2243d6';
                        } else if (row.status_form == 'form') {
                            color = '#2243d6';
                        } else if (row.status_form == 'form detail') {
                            color = '#2243d6';
                        } else if (row.status_form == 'form spreading') {
                            color = '#2243d6';
                        }

                        return  "<span style='font-weight: 600; color: "+ color + "' >" + (data ? data : '-') + "</span>"
                    }
                },
                {
                    targets: [1, 2, 3, 4, 5, 6, 7, 8, 9, 13],
                    className: "text-nowrap"
                }
            ],
            rowCallback: function( row, data, index ) {
                if (data['tipe_form'] == 'manual') {
                    $('td', row).css('background-color', '#e7dcf7');
                    $('td', row).css('border', '0.15px solid #d0d0d0');
                } else if (data['tipe_form'] == 'pilot') {
                    $('td', row).css('background-color', '#c5e0fa');
                    $('td', row).css('border', '0.15px solid #d0d0d0');
                }
            }
        });

        let datatableRatio = $("#datatable-ratio").DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('get-marker-ratio') }}',
                data: function(d) {
                    d.marker_input_kode = $('#edit_marker_input_kode').val();
                },
            },
            columns: [
                {
                    data: 'size'
                },
                {
                    data: 'ratio'
                },
                {
                    data: 'qty_cutting'
                },
            ]
        });

        let datatableRatio1 = $("#datatable-ratio-1").DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('get-marker-ratio') }}',
                data: function(d) {
                    d.marker_input_kode = $('#edit_marker_input_kode').val();
                },
            },
            columns: [
                {
                    data: 'size'
                },
                {
                    data: 'ratio'
                },
                {
                    data: 'qty_cutting'
                },
            ]
        });

        function dataTableReload() {
            datatable.ajax.reload();
        }

        function dataTableRatioReload() {
            datatableRatio.ajax.reload();
        }

        function dataTableRatio1Reload() {
            datatableRatio1.ajax.reload();
        }

        $('#datatable thead tr').clone(true).appendTo('#datatable thead');
        $('#datatable thead tr:eq(1) th').each(function(i) {
            if (i != 0 && i != 9 && i != 10) {
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control form-control-sm"/>');

                $('input', this).on('keyup change', function() {
                    if (datatable.column(i).search() !== this.value) {
                        datatable
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            } else {
                $(this).empty();
            }
        });

        function updateNoCut() {
            $.ajax({
                url: '{{ route('form-cut-update-no-cut') }}',
                type: "put",
                success: function(res) {
                    console.log("success", res);
                },
                error: function(jqXHR) {
                    console.log("error", jqXHR);
                }
            });
        }
    </script>
@endsection
