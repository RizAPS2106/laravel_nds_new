@extends('layouts.index')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h5 class="fw-bold text-sb"><i class="fa fa-edit fa-sm"></i> Edit Data Marker - {{ $marker->kode }}</h5>
        <a href="{{ route('marker') }}" class="btn btn-primary btn-sm px-1 py-1"><i class="fas fa-reply"></i> Kembali ke Marker</a>
    </div>
    @php
        $totalForm = $marker->formCutInputs->count();
    @endphp
    <form action="{{ route('update-marker')."/".$marker->id }}" method="post" id="store-marker" onsubmit="submitMarkerForm(this, event)">
        @method('PUT')
        <div class="card card-sb">
            <input type="hidden" id="id" name="id" value="{{ $marker->id }}">
            <div class="card-header">
                <h5 class="card-title fw-bold">
                    List Data
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 col-md-3">
                        <div class="mb-1">
                            <label class="form-label"><small>Tanggal</small></label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ $marker->tanggal }}" readonly>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mb-1">
                            <div class="form-group">
                                <label><small>No. WS</small></label>
                                <input type="text" class="form-control" id="act_costing_ws" name="act_costing_ws" value="{{ $marker->act_costing_ws }}" readonly>
                                <input type="hidden" class="form-control" id="act_costing_id" name="act_costing_id" value="{{ $marker->act_costing_id }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mb-1">
                            <div class="form-group">
                                <label><small>Color</small></label>
                                <input type="text" class="form-control" id="color" name="color" value="{{ $marker->color }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mb-1">
                            <div class="form-group">
                                <label><small>Panel</small></label>
                                <input type="text" class="form-control" id="panel" name="panel" value="{{ $marker->panel }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="d-flex flex-column">
                            <div class="mb-1">
                                <label class="form-label"><small>Buyer</small></label>
                                <input type="text" class="form-control" id="buyer" name="buyer" value="{{ $marker->buyer }}" readonly>
                            </div>
                            <div class="mb-1">
                                <label class="form-label"><small>Style</small></label>
                                <input type="text" class="form-control" id="style" name="style" value="{{ $marker->style }}" readonly>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label"><small>Cons WS</small></label>
                                        <input type="text" class="form-control" id="cons_ws" name="cons_ws" value="{{ $marker->cons_ws }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label"><small>Qty Order</small></label>
                                        <input type="text" class="form-control" id="order_qty" name="order_qty" value="" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="row">
                            <div class="col-12 d-none">
                                <label class="form-label"><small>Kode Marker</small></label>
                                <input type="text" class="form-control" id="kode_marker" name="kode_marker" value="{{ $marker->kode }}" readonly>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label"><small>P. Marker</small></label>
                                <div class="input-group mb-1">
                                    <input type="number" class="form-control" id="p_marker" name="p_marker" step=".001" value="{{ $marker->panjang_marker }}" {{ $totalForm > 0 ? "readonly" : "" }}>
                                    <span class="input-group-text">METER</span>
                                </div>
                                <input type="hidden" class="form-control" id="p_unit" name="p_unit" value="METER" readonly>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label"><small>Comma</small></label>
                                <div class="input-group mb-1">
                                    <input type="number" class="form-control" id="comma_marker" name="comma_marker" step=".001" value="{{ $marker->comma_marker }}" {{ $totalForm > 0 ? "readonly" : "" }}>
                                    <span class="input-group-text">CM</span>
                                </div>
                                <input type="hidden" class="form-control" id="comma_unit" name="comma_unit" value="CM" readonly>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label"><small>L. Marker</small></label>
                                <div class="input-group mb-1">
                                    <input type="number" class="form-control" id="l_marker" name="l_marker" step=".001" value="{{ $marker->lebar_marker }}" {{ $totalForm > 0 ? "readonly" : "" }}>
                                    <span class="input-group-text">CM</span>
                                </div>
                                <input type="hidden" class="form-control" id="l_unit" name="l_unit" value="CM" readonly>
                            </div>
                            <div class="col-6 col-md-6">
                                <div class="mb-1">
                                    <label class="form-label"><small>Cons Marker</small></label>
                                    <input type="number" class="form-control" id="cons_marker" name="cons_marker" step=".001" value="{{ $marker->cons_marker }}" {{ $totalForm > 0 ? "readonly" : "" }}>
                                </div>
                            </div>
                            <div class="col-4 col-md-4">
                                <div class="mb-1">
                                    <label class="form-label"><small>Cons Piping</small></label>
                                    <input type="number" class="form-control" id="cons_piping_marker" name="cons_piping_marker" step=".001" value="{{ $marker->cons_piping_marker }}" {{ $totalForm > 0 ? "readonly" : "" }}>
                                </div>
                            </div>
                            <div class="col-4 col-md-4">
                                <div class="mb-1">
                                    <label class="form-label"><small>Gramasi</small></label>
                                    <input type="number" class="form-control" id="gramasi_marker" name="gramasi_marker" step=".001" value="{{ $marker->gramasi_marker }}" {{ $totalForm > 0 ? "readonly" : "" }}>
                                </div>
                            </div>
                            <div class="col-4 col-md-4">
                                <div class="mb-1">
                                    <label class="form-label"><small>Qty Gelar Marker</small></label>
                                    <input type="number" class="form-control" id="gelar_qty_marker" name="gelar_qty_marker" onchange="calculateAllRatio(this)" onkeyup="calculateAllRatio(this)" value="{{ $marker->gelar_qty_marker }}" {{ $totalForm > 0 ? "readonly" : "" }}>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1">
                            <label class="form-label"><small>No. Urut Marker</small></label>
                            <input type="text" class="form-control" id="urutan_marker" name="urutan_marker" value="{{ $marker->urutan_marker }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1">
                            <label class="form-label"><small>PO</small></label>
                            <input type="text" class="form-control" id="po_marker" name="po_marker" value="{{ $marker->po_marker }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1">
                            <label class="form-label"><small>Tipe Marker</small></label>
                            <select class="form-select select2bs4" id="tipe_marker" name="tipe_marker" {{ $totalForm > 0 ? "disabled" : "" }}>
                                <option value="regular" {{ $marker->tipe_marker == "regular" ? "selected" : "" }}>Regular</option>
                                <option value="special" {{ $marker->tipe_marker == "special" ? "selected" : "" }}>Special</option>
                                <option value="pilot" {{ $marker->tipe_marker == "pilot" ? "selected" : "" }}>Pilot</option>
                                <option value="bulk" {{ $marker->tipe_marker == "bulk" ? "selected" : "" }}>Bulk</option>
                            </select>
                        </div>
                        @if ($totalForm > 0)
                            <input type="hidden" id="tipe_marker" name="tipe_marker" value="{{ $marker->tipe_marker }}">
                        @endif
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1">
                            <label class="form-label"><small>Catatan</small></label>
                            <textarea class="form-control" id="notes" name="notes">{{ $marker->notes }}</textarea>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" id="jumlah_so_det" name="jumlah_so_det" readonly>
                </div>
            </div>
        </div>
        <div class="card card-sb">
            <div class="card-header">
                <h5 class="card-title fw-bold">
                    Data Ratio
                </h5>
            </div>
            <div class="card-body table-responsive">
                <table id="orderQtyDatatable" class="table table-bordered table-striped table-sm w-100">
                    <thead>
                        <tr>
                            <th>WS</th>
                            <th>Color</th>
                            <th>Size</th>
                            <th>Size Input</th>
                            <th>QTY Order</th>
                            <th>Sisa</th>
                            <th>Persentase</th>
                            <th>So Det Id</th>
                            <th>Ratio</th>
                            <th>Cut Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th id="total_ratio"></th>
                            <th id="total_qty_cutting"></th>
                        </tr>
                    </tfoot>
                </table>
                <button class="btn btn-sb float-end mt-3"><i class="fa fa-save fa-sm"></i> Simpan</button>
            </div>
        </div>
    </form>
@endsection

@section('custom-script')
    <script>
        // Global Variable
        var sumCutQty = null;
        var totalRatio = null;

        // Initial Window On Load Event
        $(document).ready(async function () {
            // Call Get Total Cut Qty ( set sum cut qty variable )
            await getTotalCutQty($("#act_costing_id").val(), $("#color").val(), $("#panel").val());

            getNumber();
            updateSizeList();
        });

        // Get & Set Total Cut Qty Based on Order WS and Order Color ( to know remaining cut qty )
        async function getTotalCutQty(actCostingWsId, color, panel) {
            sumCutQty = await $.ajax({
                url: '{{ route("create-marker") }}',
                type: 'get',
                data: {
                    act_costing_id: actCostingWsId,
                    color: color,
                    panel: panel,
                },
                dataType: 'json',
            });
        }

        // Calculate Remaining Cut Qty
        function remainingQtyCutting(orderQty, soDetId) {
            // Get Total Cut Qty Based on Order WS, Order Color and Order Panel ( to know remaining cut qty )
            let sumCutQtyData = sumCutQty ? sumCutQty.find(o => o.so_det_id == soDetId && o.panel == $("#panel").val()) : 0;

            // Calculate Remaining Cut Qty
            let remain = orderQty - (sumCutQtyData ? sumCutQtyData.total_qty_cutting : 0);

            return remain;
        }

        // Order Qty Datatable (Size|Ratio|Cut Qty)
        let orderQtyDatatable = $("#orderQtyDatatable").DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("get-general-sizes") }}',
                data: function (d) {
                    d.marker_input_kode = $('#kode_marker').val();
                    d.act_costing_id = $('#act_costing_id').val();
                    d.color = $('#color').val();
                },
            },
            columns: [
                {
                    data: 'act_costing_ws'
                },
                {
                    data: 'color'
                },
                {
                    data: 'size'
                },
                {
                    data: 'size' // size input
                },
                {
                    data: 'order_qty'
                },
                {
                    data: 'ratio' // remaining cut qty
                },
                {
                    data: 'qty_cutting' // percentage
                },
                {
                    data: 'so_det_id' // detail so input
                },
                {
                    data: 'so_det_id' // ratio input
                },
                {
                    data: 'so_det_id' // cut qty input
                }
            ],
            columnDefs: [
                {
                    // Size Input
                    targets: [3],
                    className: "d-none",
                    render: (data, type, row, meta) => {
                        // Hidden Size Input
                        return '<input type="hidden" class="form-control" id="size-' + meta.row + '" name="size['+meta.row+']" value="' + data + '" readonly />'
                    }
                },
                {
                    // Remaining Qty Cutting
                    targets: [5],
                    render: (data, type, row, meta) => {
                        // Calculate Remaining Qty Cutting
                        let remain = remainingQtyCutting(row.order_qty, row.so_det_id);

                        return remain;
                    }
                },
                {
                    // Percentage
                    targets: [6],
                    render: (data, type, row, meta) => {
                        // Calculate Remaining Qty Cutting
                        let remain = remainingQtyCutting(row.order_qty, row.so_det_id);

                        // Calculate Percentage
                        let percentage = Number(row.order_qty) > 0 ? ((Number(row.order_qty)-Number(remain))/Number(row.order_qty)*100) : 0;

                        return `
                            <div class="position-relative">
                                <div class="progress border border-sb" style="height: 27px">
                                    <p class="position-absolute" style="top: 55%;left: 50%;transform: translate(-50%, -50%);" id="current_ply_progress_txt">`+ percentage.round(2) +`%</p>
                                    <div class="progress-bar" style="background-color: #75baeb; width: `+ percentage.round(2) +`%" role="progressbar" id="current_ply_progress"></div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    // SO Detail Input
                    targets: [7],
                    className: "d-none",
                    render: (data, type, row, meta) => {
                        // Hidden Detail SO Input
                        return '<input type="hidden" id="so-det-id-' + meta.row + '" name="so_det_id['+meta.row+']" value="' + data + '" readonly />'
                    }
                },
                {
                    // Ratio Input
                    targets: [8],
                    render: (data, type, row, meta) => {
                        // Calculate Remaining Qty Cutting
                        let remain = remainingQtyCutting(row.order_qty, row.so_det_id);

                        // Conditional Based on Remaining Qty Cutting
                        // let readonly = remain < 1 ? "readonly" : "";
                        let readonly = remain < 1 ? "" : "";

                        // Hidden Ratio Input
                        return '<input type="number" id="ratio-' + meta.row + '" name="ratio[' + meta.row + ']" onchange="calculateRatio(' + meta.row + ');" onkeyup="calculateRatio(' + meta.row + ');" value="' + row.ratio + '" '+readonly+' />';
                    }
                },
                {
                    // Qty Cutting Input
                    targets: [9],
                    render: (data, type, row, meta) => {
                        // Hidden Qty Cutting Input
                        return '<input type="number" id="qty-cutting-' + meta.row + '" name="qty_cutting['+meta.row+']" value="' + row.qty_cutting + '" readonly />'
                    }
                }
            ],
            footerCallback: function(row, data, start, end, display) {
                // This datatable api
                let api = this.api();

                // Remove the formatting to get integer data for summation
                let intVal = function(i) {
                    return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                };

                // Total order qty
                let orderQtyTotal = api
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        let result = intVal(a) + intVal(b);
                        return result;
                    }, 0);

                // Total remain qty
                let remainQtyTotal = orderQtyDatatable
                    .cells( null, 5 )
                    .render( 'display' )
                    .reduce(function(a, b) {
                        let result = intVal(a) + intVal(b);
                        return result;
                    }, 0);

                let ratioTotal = orderQtyDatatable
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        let result = intVal(a) + intVal(b);
                        return result;
                    }, 0);

                let cutQtyTotal = orderQtyDatatable
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        let result = intVal(a) + intVal(b);
                        return result;
                    }, 0);

                // Update footer
                $(api.column(1).footer()).html("Total");
                $(api.column(4).footer()).html(Number(orderQtyTotal).toLocaleString('id-ID'));
                $(api.column(5).footer()).html(Number(remainQtyTotal).toLocaleString('id-ID'));
                $(api.column(8).footer()).html(Number(ratioTotal).toLocaleString('id-ID')); // Total ratio
                $(api.column(9).footer()).html(Number(cutQtyTotal).toLocaleString('id-ID')); // Total cut qty
            },
        });

        // Update Order Qty Datatable
        async function updateSizeList() {
            await orderQtyDatatable.ajax.reload(() => {
                // Get Sizes Count ( for looping over sizes input )
                document.getElementById('jumlah_so_det').value = orderQtyDatatable.data().count();
            });
        }

        // Get & Set Order WS Cons and Order Qty Based on Order WS, Order Color and Order Panel
        function getNumber() {
            document.getElementById('order_qty').value = null;
            return $.ajax({
                url: ' {{ route("get-general-number") }}',
                type: 'get',
                dataType: 'json',
                data: {
                    act_costing_id: $('#act_costing_id').val(),
                    color: $('#color').val(),
                    panel: $('#panel').val()
                },
                success: function (res) {
                    if (res) {
                        document.getElementById('order_qty').value = res.order_qty;
                    }
                }
            });
        }

        // Calculate Cut Qty Based on Ratio and Spread Qty ( Ratio * Spread Qty )
        function calculateRatio(id) {
            let ratio = document.getElementById('ratio-'+id).value;
            let gelarQty = document.getElementById('gelar_qty_marker').value;

            // Cut Qty Formula
            document.getElementById('qty-cutting-'+id).value = ratio * gelarQty;

            // Call Calculate Total Ratio Function ( for order qty datatable summary )
            calculateTotalRatio();
        }

        // Calculate Total Ratio
        function calculateTotalRatio() {
            // Get Sizes Count
            let totalSize = document.getElementById('jumlah_so_det').value;

            let totalRatio = 0;
            let totalCutQty = 0;

            // Looping Over Sizes Input
            for (let i = 0; i < totalSize; i++) {
                // Sum Ratio and Cut Qty
                totalRatio += Number(document.getElementById('ratio-'+i).value);
                totalCutQty += Number(document.getElementById('qty-cutting-'+i).value);
            }

            // Set Ratio and Cut Qty ( order qty datatable summary )
            document.querySelector("table#orderQtyDatatable tfoot tr th:nth-child(7)").innerText = totalRatio;
            document.querySelector("table#orderQtyDatatable tfoot tr th:nth-child(8)").innerText = totalCutQty;
        }

        // Calculate All Cut Qty at Once Based on Spread Qty
        function calculateAllRatio(element) {
            // Get Sizes Count
            let totalSize = document.getElementById('jumlah_so_det').value;

            let gelarQty = element.value;

            // Looping Over Sizes Input
            for (let i = 0; i < totalSize; i++) {
                // Calculate Cut Qty
                let ratio = document.getElementById('ratio-'+i).value;

                // Cut Qty Formula
                document.getElementById('qty-cutting-'+i).value = ratio * gelarQty;
            }

            // Call Calculate Total Ratio Function ( for order qty datatable summary )
            calculateTotalRatio();
        }

        // Prevent Form Submit When Pressing Enter
        document.getElementById("store-marker").onkeypress = function(e) {
            var key = e.charCode || e.keyCode || 0;
            if (key == 13) {
                e.preventDefault();
            }
        }

        // Add Note When Pilot
        document.getElementById("tipe_marker").onchange = function(e) {
            if (this.value == "pilot") {
                document.getElementById("notes").value = "PILOT MARKER";
            } else {
                document.getElementById("notes").value = "";
            }
        }

        // Submit Marker Form
        function submitMarkerForm(e, evt) {
            document.getElementById("loading").classList.remove("d-none");
            $("input[type=submit][clicked=true]").attr('disabled', true);

            evt.preventDefault();

            clearModified();

            $.ajax({
                url: e.getAttribute('action'),
                type: e.getAttribute('method'),
                data: new FormData(e),
                processData: false,
                contentType: false,
                success: async function(res) {
                    document.getElementById("loading").classList.add("d-none");
                    $("input[type=submit][clicked=true]").removeAttr('disabled');

                    // Success Response
                    if (res.status == 200) {
                        // When Actually Success :

                        // Reset This Form
                        e.reset();

                        // Success Alert
                        Swal.fire({
                            icon: 'success',
                            title: 'Data Marker berhasil disimpan',
                            text: res.message,
                            showCancelButton: false,
                            showConfirmButton: true,
                            confirmButtonText: 'Oke',
                            timer: 5000,
                            timerProgressBar: true
                        }).then(() => {
                            if (res.redirect != '') {
                                if (res.redirect != 'reload') {
                                    location.href = res.redirect;
                                } else {
                                    location.reload();
                                }
                            }
                        });
                    } else {
                        // When Actually Error :

                        // Error Alert
                        iziToast.error({
                            title: 'Error',
                            message: res.message,
                            position: 'topCenter'
                        });
                    }

                    // Reload Order Qty Datatable
                    orderQtyDatatable.ajax.reload();

                    // If There Are Some Additional Error
                    if (Object.keys(res.additional).length > 0 ) {
                        for (let key in res.additional) {
                            if (document.getElementById(key)) {
                                document.getElementById(key).classList.add('is-invalid');

                                if (res.additional[key].hasOwnProperty('message')) {
                                    document.getElementById(key+'_error').classList.remove('d-none');
                                    document.getElementById(key+'_error').innerHTML = res.additional[key]['message'];
                                }

                                if (res.additional[key].hasOwnProperty('value')) {
                                    document.getElementById(key).value = res.additional[key]['value'];
                                }

                                modified.push(
                                    [key, '.classList', '.remove(', "'is-invalid')"],
                                    [key+'_error', '.classList', '.add(', "'d-none')"],
                                    [key+'_error', '.innerHTML = ', "''"],
                                )
                            }
                        }
                    }
                }, error: function (jqXHR) {
                    document.getElementById("loading").classList.add("d-none");
                    $("input[type=submit][clicked=true]").removeAttr('disabled');

                    // Error Response
                    let res = jqXHR.responseJSON;
                    let message = '';
                    let i = 0;

                    for (let key in res.errors) {
                        message = res.errors[key];
                        document.getElementById(key).classList.add('is-invalid');
                        modified.push(
                            [key, '.classList', '.remove(', "'is-invalid')"],
                        )

                        if (i == 0) {
                            document.getElementById(key).focus();
                            i++;
                        }
                    };
                }
            });
        }
    </script>
@endsection
