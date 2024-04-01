@extends('layouts.index')

@section('content')
    <div class="card card-sb">
        <div class="card-header">
            <h5 class="card-title fw-bold mb-0"><i class="fas fa-cut fa-sm"></i> Form Cutting</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-end gap-3 mb-3">
                <div class="d-flex align-items-end gap-3 mb-3">
                    <div>
                        <label class="form-label"><small>Tanggal Awal</small></label>
                        <input type="date" class="form-control form-control-sm" id="tanggal_awal" name="tanggal_awal" onchange="dataTableReload()">
                    </div>
                    <div>
                        <label class="form-label"><small>Tanggal Akhir</small></label>
                        <input type="date" class="form-control form-control-sm" id="tanggal_akhir" name="tanggal_akhir" value="{{ date('Y-m-d') }}" onchange="dataTableReload()">
                    </div>
                    <div>
                        <button class="btn btn-primary btn-sm" onclick="dataTableReload()"><i class="fa fa-search"></i></button>
                    </div>
                </div>

                <div class="d-flex align-items-end gap-3 mb-3">
                    <a href="{{ url('manual-form-cut/create') }}" target="_blank" class="btn btn-sm btn-sb"><i class="fas fa-clipboard-list"></i> Manual</a>
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
                            <th>Qty Ply</th>
                            <th>Color</th>
                            <th>Panel</th>
                            <th>Style</th>
                            <th>No. WS</th>
                            <th>Status</th>
                            <th>Ket.</th>
                            <th>App</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Form Detail -->
        <div class="modal fade" id="detailSpreadingModal" tabindex="-1" aria-labelledby="detailSpreadingModalLabel" aria-hidden="true">
            <form action="{{ route('update-spreading') }}" method="post" onsubmit="submitForm(this, event)">
                @method('PUT')
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-sb text-light">
                            <h1 class="modal-title fs-5" id="detailSpreadingModalLabel"><i class="fa fa-search fa-sm"></i> Detail Form</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="max-height: 65vh !important;">
                            <div class="row">
                                <input type="hidden" id="edit_id" name="edit_id">
                                <input type="hidden" id="edit_marker_id" name="edit_marker_id">
                                <div class="col-6 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><small>No. Form</small></label>
                                        <input type="text" class="form-control" id="edit_no_form" name="edit_no_form" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Tanggal</small></label>
                                        <input type="text" class="form-control" id="edit_tanggal" name="edit_tanggal" value="" readonly />
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
                                        <input type="text" class="form-control" id="edit_gelar_qty" name="edit_gelar_qty" value="" readonly />
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
                                <div class="col-4 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Tipe Marker</small></label>
                                        <input type="text" class="form-control" id="edit_tipe_marker" name="edit_tipe_marker" value="" readonly />
                                    </div>
                                </div>
                                <div class="col-4 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Meja</small></label>
                                        <input type="text" class="form-control" id="edit_meja" name="edit_meja" value="-" readonly />
                                    </div>
                                </div>
                                <div class="col-4 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><small>Keterangan</small></label>
                                        <textarea class="form-control" id="edit_notes" name="edit_notes" readonly></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <table id="datatable-ratio" class="table table-bordered table-striped table-sm w-100">
                                        <thead>
                                            <tr>
                                                <th>Size</th>
                                                <th>Ratio</th>
                                                <th>Cut Qty</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fa fa-times fa-sm"></i> Tutup</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('custom-script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let oneWeeksBefore = new Date(new Date().setDate(new Date().getDate() - 7));
            let oneWeeksBeforeDate = ("0" + oneWeeksBefore.getDate()).slice(-2);
            let oneWeeksBeforeMonth = ("0" + (oneWeeksBefore.getMonth() + 1)).slice(-2);
            let oneWeeksBeforeYear = oneWeeksBefore.getFullYear();
            let oneWeeksBeforeFull = oneWeeksBeforeYear + '-' + oneWeeksBeforeMonth + '-' + oneWeeksBeforeDate;

            $("#tanggal_awal").val(oneWeeksBeforeFull).trigger("change");

            window.addEventListener("focus", () => {
                $('#datatable').DataTable().ajax.reload(null, false);
            });
        });

        $('#datatable thead tr').clone(true).appendTo('#datatable thead');
        $('#datatable thead tr:eq(1) th').each(function(i) {
            if (i != 0 && i != 5 && i != 6 && i != 10 && i != 12) {
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

        var datatable = $("#datatable").DataTable({
            processing: true,
            ordering: false,
            serverSide: true,
            scrollX: "500px",
            scrollY: "500px",
            ajax: {
                url: '{{ route('form-cut-input') }}',
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
                    data: 'meja'
                },
                {
                    data: 'marker_input_kode'
                },
                {
                    data: 'marker_details'
                },
                {
                    data: undefined
                },
                {
                    data: 'color'
                },
                {
                    data: 'panel'
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
                    data: 'notes'
                },
                {
                    data: 'app_by_name'
                },
            ],
            columnDefs: [
                {
                    targets: [5],
                    className: "text-nowrap",
                    render: (data, type, row, meta) => {
                        let color = "";

                        if (row.status_form == 'finish') {
                            color = '#087521';
                        } else if (row.status_form == 'marker' ) {
                            color = '#2243d6';
                        } else if (row.status_form == 'form') {
                            color = '#2243d6';
                        } else if (row.status_form == 'form detail') {
                            color = '#2243d6';
                        } else if (row.status_form == 'form spreading') {
                            color = '#2243d6';
                        } else {
                            if (row.app != 'y') {
                                color = '#616161';
                            }
                        }

                        return  "<span style='font-weight: 600; color: "+ color + "' >" + (data ? data.replace(/,/g, '<br>') : '-') + "</span>"
                    }
                },
                {
                    targets: [6],
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
                    targets: [11],
                    className: "text-center",
                    render: (data, type, row, meta) => {
                        icon = "";

                        switch (data) {
                            case "idle":
                                if (row.app != 'y') {
                                    icon = `<i class="fas fa-file fa-lg" style="color: #616161;"></i>`;
                                } else {
                                    icon = `<i class="fas fa-file fa-lg"></i>`;
                                }
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
                    targets: [13],
                    className: "text-center",
                    render: (data, type, row, meta) => {
                        icon = "";
                        color = "";

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
                        } else {
                            if (row.app != 'y') {
                                color = '#616161';
                            }
                        }

                        switch (row.app) {
                            case "y":
                                icon = `<span class="text-nowrap" style="color: ` + color + `;font-weight: 600;">`+data+` <i class="fas fa-check fa-sm"></i></span>`;
                                break;
                            case "n":
                                icon = `<span class="text-nowrap" style="color: ` + color + `;font-weight: 600;">`+data+` <i class="fas fa-times fa-sm"></i></span>`;
                                break;
                            default:
                                icon = `<span class="text-nowrap" style="color: ` + color + `;font-weight: 600;">`+data+` <i class="fas fa-minus fa-sm"></i></span>`;
                                break;
                        }

                        return icon;
                    }
                },
                {
                    targets: [0],
                    render: (data, type, row, meta) => {
                        let btnEdit =
                            "<a href='javascript:void(0);' class='btn btn-primary btn-sm' onclick='editData(" + JSON.stringify(row) + ", \"detailSpreadingModal\", [{\"function\" : \"dataTableRatioReload()\"}]);'><i class='fa fa-search'></i></a>";

                        let btnProcess = "";

                        if (row.tipe_form_cut == 'manual') {
                            btnProcess = (row.qty_ply > 0 && row.meja_id != '' && row.meja_id != null && row.app == 'Y') || row.status_form != 'idle' ?`<a class='btn btn-success btn-sm' href='{{ route('process-manual-form-cut') }}/` +row.id + `' data-bs-toggle='tooltip' target='_blank'><i class='fa ` + (row.status_form == "finish" ? `fa-search-plus` : `fa-plus`) +`'></i></a>` :"";
                        } else if (row.tipe_form_cut == 'pilot') {
                            btnProcess = (row.qty_ply > 0 && row.meja_id != '' && row.meja_id != null && row.app == 'Y') || row.status_form != 'idle' ? `<a class='btn btn-success btn-sm' href='{{ route('process-pilot-form-cut') }}/` + row.id + `' data-bs-toggle='tooltip' target='_blank'><i class='fa `+(row.status_form == "finish" ? `fa-search-plus` : `fa-plus`)+`'></i></a>` : "";
                        } else {
                            btnProcess = (row.qty_ply > 0 && row.meja_id != '' && row.meja_id != null && row.app == 'Y') || row.status_form != 'idle' ? `<a class='btn btn-success btn-sm' href='{{ route('process-form-cut-input') }}/` + row.id + `' data-bs-toggle='tooltip' target='_blank'><i class='fa ` + (row.status_form == "finish" ? `fa-search-plus` : `fa-plus`) + `'></i></a>` : "";
                        }

                        return `<div class='d-flex gap-1 justify-content-center'>` + btnEdit + btnProcess + `</div>`;
                    }
                },
                {
                    targets: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13],
                    className: "text-nowrap"
                },
                {
                    targets: '_all',
                    render: (data, type, row, meta) => {
                        var color = 'black';

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
                        } else {
                            if (row.app != 'y') {
                                color = '#616161';
                            }
                        }

                        return '<span style="font-weight: 600; color:' + color + ';">' + (data ? data : "-") + '</span>';
                    }
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

        var datatableRatio = $("#datatable-ratio").DataTable({
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
            $('#datatable').DataTable().ajax.reload(null, false);
        }

        function dataTableRatioReload() {
            $('#datatable-ratio').DataTable().ajax.reload();
        }

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
