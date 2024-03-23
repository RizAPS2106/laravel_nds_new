@extends('layouts.index')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h5 class="fw-bold text-sb"><i class="fa-solid fa-file-circle-plus"></i> Tambah Data Form Spreading</h5>
        <a href="{{ route('spreading') }}" class="btn btn-primary btn-sm px-1 py-1"><i class="fas fa-reply"></i> Kembali ke Spreading</a>
    </div>
    <form action="{{ route('store-spreading') }}" method="post" id="store-spreading" name='form' onsubmit="submitForm(this, event)">
        @csrf
            <div class='row'>
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="fw-bold card-title">Filter Data :</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-6 col-md-6">
                                    <div class="form-group">
                                        <label>No. WS</label>
                                        <select class="form-control select2bs4" id="act_costing_id" name="act_costing_id" onchange='getMarkerOptions();' style="width: 100%;">
                                            <option selected="selected" value="">Pilih WS</option>
                                            @foreach ($orders as $order)
                                                <option value="{{ $order->act_costing_id }}">
                                                    {{ $order->act_costing_ws }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-6">
                                    <div class="form-group">
                                        <label>No. Marker</label>
                                        <select class='form-control select2bs4' style='width: 100%;' name='marker_input_kode' id='marker_input_kode' onchange='getMarkerInfo();'></select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>Tipe Form</label>
                                        <input type='text' class='form-control' id='tipe_form' name='tipe_form' autocomplete='off' readonly>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label>Qty Ply Cutting</label>
                                        <input type='number' class='form-control' id='qty_ply' name='qty_ply' oninput='calculation();' autocomplete='off'>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label>Total Form</label>
                                        <input type='number' class='form-control' id='jumlah_form' name='jumlah_form' oninput='customCalculation();' autocomplete='off'>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <textarea class='form-control' id='notes' name='notes' rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="fw-bold card-title">Hasil Data</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Qty Ply Marker</label>
                                        <input type='text' class='form-control' id='qty_ply_marker' name='qty_ply_marker' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Qty Ply Cutting</label>
                                        <input type='text' class='form-control' id='qty_ply_cutting' name='qty_ply_cutting' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Total Form</label>
                                        <input type='text' class='form-control' id='total_form' name='total_form' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Qty Ply Form</label>
                                        <input type='text' class='form-control' id='qty_ply_form' name='qty_ply_form' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Sisa Ply Marker</label>
                                        <input type='text' class='form-control' id='sisa' name='sisa' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tarik_sisa" id="tarik_sisa" value="tarik">
                                            <label>Tarik Sisa Ply</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group mt-3">
                                        <button type='submit' name='submit' class='btn btn-block btn-success fw-bold'>
                                            <i class="fa fa-save"></i>
                                            SIMPAN
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class='row'>
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="fw-bold card-title">Detail Data :</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Panel</label>
                                        <input type='text' class='form-control' id='panel' name='panel' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Color</label>
                                        <input type='text' class='form-control' id='color' name='color' readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Buyer</label>
                                        <input type='text' class='form-control' id='buyer' name='buyer' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Style</label>
                                        <input type='text' class='form-control' id='style' name='style' readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>P. Marker</label>
                                        <input type='text' class='form-control' id='p_marker' name='p_marker' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Unit</label>
                                        <input type='text' class='form-control' id='unit_p_marker' name='unit_p_marker' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Comma</label>
                                        <input type='text' class='form-control' id='comma_p_marker' name='comma_p_marker' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Unit</label>
                                        <input type='text' class='form-control' id='unit_comma_p_marker' name='unit_comma_p_marker' readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>PO</label>
                                        <input type='text' class='form-control' id='po_marker' name='po_marker' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>L. Marker</label>
                                        <input type='text' class='form-control' id='l_marker' name='l_marker' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Unit</label>
                                        <input type='text' class='form-control' id='unit_l_marker' name='unit_l_marker' readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Qty Gelar Marker</label>
                                        <input type='text' class='form-control' id='qty_gelar_marker' name='qty_gelar_marker' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>No. WS</label>
                                        <input type='text' class='form-control' id='act_costing_ws' name='act_costing_ws' readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Cons. WS</label>
                                        <input type='text' class='form-control' id='cons_ws' name='cons_ws' readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Cons. Marker</label>
                                        <input type='text' class='form-control' id='cons_marker' name='cons_marker' readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="fw-bold card-title">Ratio</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered table-striped table-sm w-100">
                                <thead>
                                    <tr>
                                        <th>Size</th>
                                        <th>Ratio</th>
                                        <th>Qty Cut Marker</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </form>
@endsection

@section('custom-script')
    <script>
        $(document).ready(() => {
            $('#act_costing_id').val("").trigger("change");
            $("#marker_input_kode").prop("disabled", true);
            $("#qty_ply").prop("readonly", true);

            document.getElementById("tarik_sisa").checked = false;
        });

        document.getElementById("tarik_sisa").addEventListener("change", () => {
            if (document.getElementById("tarik_sisa").checked && document.getElementById("sisa").value > 0) {
                document.getElementById("total_form").value -= 1;
            } else {
                calculation();
                customCalculation();
            }
        });

        let datatable = $("#datatable").DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            info: false,
            ajax: {
                url: '{{ route('get-marker-ratio') }}',
                data: function(d) {
                    d.marker_input_kode = $('#marker_input_kode').val();
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
                }
            ]
        });

        function getMarkerOptions() {
            clearForm();

            let options = $.ajax({
                type: "POST",
                url: '{{ route('get-marker-options') }}',
                data: {
                    act_costing_id: document.form.act_costing_id.value
                },
                async: false
            }).responseText;

            if (options != "") {
                $("#marker_input_kode").html(options);

                $("#marker_input_kode").prop("disabled", false);
                $("#qty_ply").prop("readonly", false);
            }
        };

        function getMarkerInfo() {
            clearForm();

            console.log($('#marker_input_kode').val());

            jQuery.ajax({
                url: '{{ route('get-marker-info') }}',
                method: 'get',
                data: {
                    marker_input_kode: $('#marker_input_kode').val()
                },
                dataType: 'json',
                success: function(response) {
                    document.getElementById('act_costing_ws').value = response.act_costing_ws;
                    document.getElementById('panel').value = response.panel;
                    document.getElementById('color').value = response.color;
                    document.getElementById('buyer').value = response.buyer;
                    document.getElementById('style').value = response.style;
                    document.getElementById('marker_input_kode').value = response.kode;
                    document.getElementById('p_marker').value = response.panjang_marker;
                    document.getElementById('unit_p_marker').value = response.unit_panjang_marker;
                    document.getElementById('comma_p_marker').value = response.comma_marker;
                    document.getElementById('unit_comma_p_marker').value = response.unit_comma_marker;
                    document.getElementById('po_marker').value = response.po_marker;
                    document.getElementById('l_marker').value = response.lebar_marker;
                    document.getElementById('unit_l_marker').value = response.unit_lebar_marker;
                    document.getElementById('cons_ws').value = response.cons_ws;
                    document.getElementById('cons_marker').value = response.cons_marker;
                    document.getElementById('qty_gelar_marker').value = response.gelar_qty_balance_marker ? response.gelar_qty_balance_marker : response.gelar_qty_marker;
                    document.getElementById('qty_ply_marker').value = response.gelar_qty_balance_marker ? response.gelar_qty_balance_marker : response.gelar_qty_marker;
                    document.getElementById('tipe_form').value = response.tipe_marker == "bulk" && response.status_marker == "active" ? "Pilot to Bulk" : capitalizeFirstLetter((response.tipe_marker).replace(' marker', ""));
                    document.getElementById('notes').value = response.notes ? response.notes : (response.tipe_marker == "bulk marker" && response.status_marker == "active" ? "Pilot to Bulk" : capitalizeFirstLetter((response.tipe_marker).replace(' marker', "")));
                },
                error: function(request, status, error) {
                    alert(request.responseText);
                },
            });

            datatable.ajax.reload();
        };

        function calculation() {
            let qtyPlyMarker = document.getElementById('qty_ply_marker').value;

            let qtyPly = document.getElementById('qty_ply').value;
            let jumlahForm = document.getElementById("jumlah_form").value;

            document.getElementById("qty_ply_cutting").value = +qtyPly;

            let result = Math.ceil(parseFloat(qtyPlyMarker) / parseFloat(qtyPly));
            let modulus = qtyPlyMarker > qtyPly ? Math.ceil(parseFloat(qtyPlyMarker) % parseFloat(qtyPly)) : 0;

            if (!isNaN(result)) {
                document.getElementById("total_form").value = result;
                document.getElementById("sisa").value = modulus;
                document.getElementById("jumlah_form").value = result;
                document.getElementById("qty_ply_form").value = (jumlahForm * qtyPly) > qtyPlyMarker ? qtyPlyMarker : (jumlahForm * qtyPly);
            }
        }

        function customCalculation() {
            let qtyPlyMarker = document.getElementById("qty_ply_marker").value;

            let qtyPly = document.getElementById("qty_ply").value;
            let jumlahForm = document.getElementById("jumlah_form").value;

            let qtyPlyForm = qtyPly * jumlahForm;
            let modulus = qtyPlyMarker > qtyPly ? Math.ceil(parseFloat(qtyPlyMarker) % parseFloat(qtyPly)) : 0;
            let maxForm = Math.floor(qtyPlyMarker/qtyPly) + (modulus > 0 ? 1 : 0);

            if (jumlahForm > maxForm) {
                calculation();
            } else {
                document.getElementById("total_form").value = jumlahForm;
                document.getElementById("sisa").value = modulus;
                document.getElementById("qty_ply_form").value = (qtyPlyForm > qtyPlyMarker ? qtyPlyMarker : qtyPlyForm);
            }
        }

        function clearForm() {
            document.getElementById('qty_ply').value = "";
            document.getElementById('jumlah_form').value = "";
            document.getElementById('qty_ply_marker').value = "";
            document.getElementById('qty_ply_cutting').value = "";
            document.getElementById('total_form').value = "";
            document.getElementById('qty_ply_form').value = "";
            document.getElementById('sisa').value = "";
            document.getElementById('notes').value = "";
        }

        function clearStep() {
            $('#act_costing_id').val("").trigger("change");
            $("#marker_input_kode").prop("disabled", true);
        }

        document.getElementById("store-spreading").onkeypress = function(e) {
            var key = e.charCode || e.keyCode || 0;
            if (key == 13) {
                e.preventDefault();
                console.log('enter key prevented');
            }
        }
    </script>
@endsection
