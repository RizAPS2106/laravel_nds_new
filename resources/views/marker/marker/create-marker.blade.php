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
    <div class="d-flex justify-content-between mb-3">
        <h5 class="fw-bold text-sb">Tambah Data Marker</h5>
        <a href="{{ route('marker') }}" class="btn btn-primary btn-sm px-1 py-1"><i class="fas fa-reply"></i> Kembali ke Marker</a>
    </div>
    <form action="{{ route('store-marker') }}" method="post" id="store-marker" onsubmit="submitMarkerForm(this, event)">
        @csrf
        <div class="card card-sb">
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
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mb-1">
                            <div class="form-group">
                                <label><small>No. WS</small></label>
                                <select class="form-control select2bs4" id="act_costing_id" name="act_costing_id" style="width: 100%;">
                                    <option selected="selected" value="">Pilih WS</option>
                                    @foreach ($orders as $order)
                                        <option value="{{ $order->id }}">
                                            {{ $order->kpno }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mb-1">
                            <div class="form-group">
                                <label><small>Color</small></label>
                                <select class="form-control select2bs4" id="color" name="color" style="width: 100%;">
                                    <option selected="selected" value="">Pilih Color</option>
                                    {{-- select 2 option --}}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mb-1">
                            <div class="form-group">
                                <label><small>Panel</small></label>
                                <select class="form-control select2bs4" id="panel" name="panel" style="width: 100%;" >
                                    <option selected="selected" value="">Pilih Panel</option>
                                    {{-- select 2 option --}}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="d-flex flex-column">
                            <input type="hidden" class="form-control" id="act_costing_ws" name="act_costing_ws" readonly>
                            <div class="mb-1">
                                <label class="form-label"><small>Buyer</small></label>
                                <input type="text" class="form-control" id="buyer" name="buyer" readonly>
                            </div>
                            <div class="mb-1">
                                <label class="form-label"><small>Style</small></label>
                                <input type="text" class="form-control" id="style" name="style" readonly>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label"><small>Cons WS</small></label>
                                        <input type="text" class="form-control" id="cons_ws" name="cons_ws" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label"><small>Qty Order</small></label>
                                        <input type="text" class="form-control" id="order_qty" name="order_qty" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label class="form-label"><small>P. Marker</small></label>
                                <div class="input-group mb-1">
                                    <input type="number" class="form-control" id="p_marker" name="p_marker" step=".001">
                                    <span class="input-group-text">METER</span>
                                </div>
                                <input type="hidden" class="form-control" id="p_unit" name="p_unit" value="METER">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label"><small>Comma</small></label>
                                <div class="input-group mb-1">
                                    <input type="number" class="form-control" id="comma_marker" name="comma_marker" step=".001">
                                    <span class="input-group-text">CM</span>
                                </div>
                                <input type="hidden" class="form-control" id="comma_unit" name="comma_unit" value="CM" readonly>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label"><small>L. Marker</small></label>
                                <div class="input-group mb-1">
                                    <input type="number" class="form-control" id="l_marker" name="l_marker" step=".001">
                                    <span class="input-group-text">CM</span>
                                </div>
                                <input type="hidden" class="form-control" id="l_unit" name="l_unit" value="CM" readonly>
                            </div>
                            <div class="col-6 col-md-6">
                                <div class="mb-1">
                                    <label class="form-label"><small>Cons Marker</small></label>
                                    <input type="number" class="form-control" id="cons_marker" name="cons_marker" step=".001">
                                </div>
                            </div>
                            <div class="col-4 col-md-4">
                                <div class="mb-1">
                                    <label class="form-label"><small>Cons Piping</small></label>
                                    <input type="number" class="form-control" id="cons_piping" name="cons_piping" step=".001">
                                </div>
                            </div>
                            <div class="col-4 col-md-4">
                                <div class="mb-1">
                                    <label class="form-label"><small>Gramasi</small></label>
                                    <input type="number" class="form-control" id="gramasi" name="gramasi" step=".001">
                                </div>
                            </div>
                            <div class="col-4 col-md-4">
                                <div class="mb-1">
                                    <label class="form-label"><small>Qty Gelar Marker</small></label>
                                    <input type="number" class="form-control" id="gelar_marker_qty" name="gelar_marker_qty" onchange="calculateAllRatio(this)" onkeyup="calculateAllRatio(this)">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1">
                            <label class="form-label"><small>No. Urut Marker</small></label>
                            <input type="text" class="form-control" id="urutan_marker" name="urutan_marker" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1">
                            <label class="form-label"><small>PO</small></label>
                            <input type="text" class="form-control" id="po" name="po">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1">
                            <label class="form-label"><small>Tipe Marker</small></label>
                            <select class="form-select select2bs4" id="tipe_marker" name="tipe_marker" style="width: 100%;">
                                <option selected="selected" value="regular marker">Regular Marker</option>
                                <option value="special marker">Special Marker</option>
                                <option value="pilot marker">Pilot Marker</option>
                                <option value="bulk marker">Bulk Marker</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1">
                            <label class="form-label"><small>Catatan</small></label>
                            <textarea class="form-control" id="notes" name="notes"></textarea>
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
                            <th id="total_cut_qty"></th>
                        </tr>
                    </tfoot>
                </table>
                <button class="btn btn-sb float-end mt-3">Simpan</button>
            </div>
        </div>
    </form>
@endsection

@section('custom-script')
    <!-- DataTables & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- Page specific script -->
    <script>
        // Global Variable
        var sumCutQty = null;
        var totalRatio = null;

        // Initial Window On Load Event
        $(document).ready(async function () {
            // Call Get Total Cut Qty ( set sum cut qty variable )
            getTotalCutQty($("#act_costing_id").val(), $("#color").val(), $("#panel").val());

            // Marker Type Default Value
            $("#tipe_marker").val("regular marker");

            //Reset Form
            if (document.getElementById('store-marker')) {
                document.getElementById('store-marker').reset();

                $("#act_costing_id").val(null).trigger("change");
            }

            // Select2 Prevent Step-Jump Input ( Step = WS -> Color -> Panel )
            $("#color").prop("disabled", true);
            $("#panel").prop("disabled", true);
        });

        // Select2 Autofocus
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });

        // Initialize Select2 Elements
        $('.select2').select2()

        // Initialize Select2BS4 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4',
        })

        // Get & Set Total Cut Qty Based on Order WS and Order Color ( to know remaining cut qty )
        async function getTotalCutQty(wsId, color, panel) {
            sumCutQty = await $.ajax({
                url: '{{ route("create-marker") }}',
                type: 'get',
                data: {
                    act_costing_id: wsId,
                    color: color,
                    panel: panel,
                },
                dataType: 'json',
            });
        }

        // Step One (WS) on change event
        $('#act_costing_id').on('change', function(e) {
            if (this.value) {
                updateColorList();
                updateOrderInfo();
            }
        });

        // Step Two (Color) on change event
        $('#color').on('change', function(e) {
            if (this.value) {
                updatePanelList();
                updateSizeList();
            }
        });

        // Step Three (Panel) on change event
        $('#panel').on('change', function(e) {
            if (this.value) {
                getMarkerCount();
                getNumber();
                updateSizeList();
            }
        });

        // Update Order Information Based on Order WS and Order Color
        function updateOrderInfo() {
            return $.ajax({
                url: '{{ route("get-general-order") }}',
                type: 'get',
                data: {
                    act_costing_id: $('#act_costing_id').val(),
                    color: $('#color').val(),
                },
                dataType: 'json',
                success: function (res) {
                    if (res) {
                        document.getElementById('ws').value = res.kpno;
                        document.getElementById('buyer').value = res.buyer;
                        document.getElementById('style').value = res.styleno;
                    }
                },
            });
        }

        // Update Color Select Option Based on Order WS
        function updateColorList() {
            document.getElementById('color').value = null;

            return $.ajax({
                url: '{{ route("get-general-colors") }}',
                type: 'get',
                data: {
                    act_costing_id: $('#act_costing_id').val(),
                },
                success: function (res) {
                    if (res) {
                        // Update this step
                        document.getElementById('color').innerHTML = res;

                        // Reset next step
                        document.getElementById('panel').innerHTML = null;
                        document.getElementById('panel').value = null;

                        // Open this step
                        $("#color").prop("disabled", false);

                        // Close next step
                        $("#panel").prop("disabled", true);

                        // Reset order information
                        document.getElementById('urutan_marker').value = null;
                        document.getElementById('cons_ws').value = null;
                        document.getElementById('order_qty').value = null;
                    }
                },
            });
        }

        // Update Panel Select Option Based on Order WS and Color WS
        function updatePanelList() {
            document.getElementById('panel').value = null;
            return $.ajax({
                url: '{{ route("get-general-panels") }}',
                type: 'get',
                data: {
                    act_costing_id: $('#act_costing_id').val(),
                    color: $('#color').val(),
                },
                success: function (res) {
                    if (res) {
                        // Update this step
                        document.getElementById('panel').innerHTML = res;

                        // Open this step
                        $("#panel").prop("disabled", false);

                        // Reset order information
                        document.getElementById('urutan_marker').value = null;
                        document.getElementById('cons_ws').value = null;
                        document.getElementById('order_qty').value = null;
                    }
                },
            });
        }

        // Calculate Remaining Cut Qty
        function remainingCutQty(orderQty, soDetId) {
            // Get Total Cut Qty Based on Order WS, Order Color and Order Panel ( to know remaining cut qty )
            let sumCutQtyData = sumCutQty.find(o => o.so_det_id == soDetId && o.panel == $("#panel").val());

            // Calculate Remaining Cut Qty
            let remain = orderQty - (sumCutQtyData ? sumCutQtyData.total_cut_qty : 0);

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
                    d.act_costing_id = $('#act_costing_id').val();
                    d.color = $('#color').val();
                },
            },
            columns: [
                {
                    data: 'no_ws'
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
                    data: null // remaining cut qty
                },
                {
                    data: null // percentage
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
                    // Remaining Cut Qty
                    targets: [5],
                    render: (data, type, row, meta) => {
                        // Calculate Remaining Cut Qty
                        let remain = remainingCutQty(row.order_qty, row.so_det_id);

                        return remain;
                    }
                },
                {
                    // Percentage
                    targets: [6],
                    render: (data, type, row, meta) => {
                        // Calculate Remaining Cut Qty
                        let remain = remainingCutQty(row.order_qty, row.so_det_id);

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
                        // Calculate Remaining Cut Qty
                        let remain = remainingCutQty(row.order_qty, row.so_det_id);

                        // Conditional Based on Remaining Cut Qty
                        // let readonly = remain < 1 ? "readonly" : "";
                        let readonly = remain < 1 ? "" : "";

                        // Hidden Ratio Input
                        return '<input type="number" id="ratio-' + meta.row + '" name="ratio[' + meta.row + ']" onchange="calculateRatio(' + meta.row + ');" onkeyup="calculateRatio(' + meta.row + ');" '+readonly+' />';
                    }
                },
                {
                    // Cut Qty Input
                    targets: [9],
                    render: (data, type, row, meta) => {
                        // Hidden Cut Qty Input
                        return '<input type="number" id="cut-qty-' + meta.row + '" name="cut_qty['+meta.row+']" readonly />'
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

                // Update footer
                $(api.column(1).footer()).html("Total");
                $(api.column(4).footer()).html(Number(orderQtyTotal).toLocaleString('id-ID'));
                $(api.column(5).footer()).html(Number(remainQtyTotal).toLocaleString('id-ID'));
                $(api.column(8).footer()).html(0); // Total ratio
                $(api.column(9).footer()).html(0); // Total cut qty
            },
        });

        // Update Order Qty Datatable
        async function updateSizeList() {
            await orderQtyDatatable.ajax.reload(() => {
                // Get Sizes Count ( for looping over sizes input )
                document.getElementById('jumlah_so_det').value = orderQtyDatatable.data().count();
            });
        }

        // Get & Set Marker Count Based on Order WS, Order Color and Order Panel
        function getMarkerCount() {
            document.getElementById('urutan_marker').value = "";
            return $.ajax({
                url: '{{ route("get-marker-count") }}',
                type: 'get',
                data: {
                    act_costing_id: $('#act_costing_id').val(),
                    color: $('#color').val(),
                    panel: $('#panel').val()
                },
                success: function (res) {
                    if (res) {
                        document.getElementById('urutan_marker').value = res;
                    }
                }
            });
        }

        // Get & Set Order WS Cons and Order Qty Based on Order WS, Order Color and Order Panel
        function getNumber() {
            document.getElementById('cons_ws').value = null;
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
                        document.getElementById('cons_ws').value = res.cons_ws;
                        document.getElementById('order_qty').value = res.order_qty;
                    }
                }
            });
        }

        // Calculate Cut Qty Based on Ratio and Spread Qty ( Ratio * Spread Qty )
        function calculateRatio(id) {
            let ratio = document.getElementById('ratio-'+id).value;
            let gelarQty = document.getElementById('gelar_marker_qty').value;

            // Cut Qty Formula
            document.getElementById('cut-qty-'+id).value = ratio * gelarQty;

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
                totalCutQty += Number(document.getElementById('cut-qty-'+i).value);
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
                document.getElementById('cut-qty-'+i).value = ratio * gelarQty;
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

        document.getElementById("tipe_marker").onchange = function(e) {
            if (this.value == "pilot marker") {
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
                        })

                        // Call Get Total Cut Qty ( update sum cut qty variable )
                        getTotalCutQty($("#act_costing_id").val(), $("#color").val(), $("#panel").val());

                        // Reset Step ( back to step one )
                        resetStep();
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

        // Reset Step
        async function resetStep() {
            await $("#act_costing_id").val(null).trigger("change");
            await $("#color").val(null).trigger("change");
            await $("#panel").val(null).trigger("change");
            await $("#color").prop("disabled", true);
            await $("#panel").prop("disabled", true);
        }
    </script>
@endsection
