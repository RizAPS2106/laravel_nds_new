@extends('layouts.index')

@section('content')
    <div class="row g-3">
        <div class="d-flex gap-3 align-items-center">
            <h5 class="mb-1">Form Cut - {{ strtoupper($formCutInputData->name) }}</h5>
            <button class="btn btn-sm btn-success" id="start-process" onclick="startProcess()">Mulai Pengerjaan</button>
        </div>
        {{-- Header Data --}}
        <div class="col-md-6">
            <div class="card card-sb" id="header-data-card">
                <div class="card-header">
                    <h3 class="card-title">Header Data</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    @php
                        $thisActCosting = $actCostingData->where('id', $formCutInputData->act_costing_id)->first();
                        $thisMarkerDetails = $markerDetailData->where('kode_marker', $formCutInputData->marker_input_kode);
                    @endphp
                    <div class="row align-items-end">
                        <input type="hidden" name="id" id="id" value="{{ $id }}" readonly>
                        <input type="hidden" name="meja_id" id="meja_id" value="{{ $formCutInputData->meja_id }}" readonly>
                        <input type="hidden" name="act_costing_id" id="act_costing_id" value="{{ $formCutInputData->act_costing_id }}" readonly>
                        <input type="hidden" name="status_form" id="status_form" value="{{ $formCutInputData->status_form }}" readonly>
                        <div class="col-6 col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Start</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="start" id="start-time" value="{{ $formCutInputData->waktu_mulai }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Finish</b></small></label>
                                <input type="text" class="form-control form-control-sm" name="finish" id="finish-time" value="{{ $formCutInputData->waktu_selesai }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Shell</b></small></label>
                                <select class="form-select form-select-sm" name="shell" id="shell">
                                    <option value="a" {{ $formCutInputData->shell == 'a' ? 'selected' : '' }}>A</option>
                                    <option value="b" {{ $formCutInputData->shell == 'b' ? 'selected' : '' }}>B</option>
                                    <option value="c" {{ $formCutInputData->shell == 'c' ? 'selected' : '' }}>C</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>No. Form</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" name="no_form" id="no_form" value="{{ $formCutInputData->no_form }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><small><b>Tanggal</b></small></label>
                                <input type="date" class="form-control form-control-sm" name="tanggal" id="tanggal" value="{{ date('Y-m-d') }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Kode Marker</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" value="{{ $formCutInputData->marker_input_kode }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>No. WS</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" name="no_ws" value="{{ $formCutInputData->act_costing_ws }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Buyer</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" name="buyer" value="{{ $thisActCosting->buyer }}" readonly>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Style</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" name="style" value="{{ $thisActCosting->style }}" readonly>
                            </div>
                        </div>
                        <div class="col-4 col-md-4">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Color</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" name="color" id="color" value="{{ $formCutInputData->color }}" readonly>
                            </div>
                        </div>
                        <div class="col-4 col-md-4">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Panel</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" name="panel" id="panel" value="{{ $formCutInputData->panel }}" readonly>
                            </div>
                        </div>
                        <div class="col-4 col-md-4">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Tipe Marker</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" name="tipe_marker" id="tipe_marker" value="{{ $formCutInputData->tipe_marker ? strtoupper($formCutInputData->tipe_marker) : '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-4 col-md-4">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>PO</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" name="po" value="{{ $formCutInputData->po_marker }}" readonly>
                            </div>
                        </div>
                        <div class="col-4 col-md-4">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>QTY Gelar Marker</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" name="gelar_qty" value="{{ $formCutInputData->gelar_qty_marker }}" readonly>
                            </div>
                        </div>
                        <div class="col-4 col-md-4">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>QTY Ply</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" id="qty_ply" name="qty_ply" value="{{ $formCutInputData->qty_ply }}" readonly>
                            </div>
                        </div>
                    </div>
                    <table id="ratio-datatable"
                        class="table table-striped table-bordered table-sm w-100 text-center mt-3">
                        <thead>
                            <tr>
                                <th class="label-fetch">Size</th>
                                <th class="label-fetch">Ratio</th>
                                <th class="label-fetch">Qty Cut Marker</th>
                                <th class="label-fetch">Qty Output</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalRatio = 0;
                                $totalQtyCutting = 0;
                                $totalQtyPly = 0;
                            @endphp
                            @foreach ($thisMarkerDetails as $item)
                                <tr>
                                    @php
                                        $totalRatio += $item->ratio;
                                        $totalQtyCutting += $item->qty_cutting;
                                        $qtyPly = $item->ratio * $formCutInputData->qty_ply;
                                        $totalQtyPly += $qtyPly;
                                    @endphp
                                    <td>{{ $item->size }}</td>
                                    <td>{{ $item->ratio }}</td>
                                    <td>{{ $item->qty_cutting }}</td>
                                    <td>{{ $qtyPly }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center">Total</th>
                                <th id="totalRatio">{{ $totalRatio }}</th>
                                <th id="totalQtyCutting">{{ $totalQtyCutting }}</th>
                                <th id="totalQtyPly">{{ $totalQtyPly }}</th>
                            </tr>
                        </tfoot>
                    </table>
                    <input type="hidden" name="total_ratio" id="total_ratio" value="{{ $totalRatio }}">
                    <input type="hidden" name="total_qty_cutting" id="total_qty_cutting" value="{{ $totalQtyCutting }}">
                    <input type="hidden" name="total_qty_ply" id="total_qty_ply" value="{{ $totalQtyPly }}">
                </div>
            </div>
        </div>
        {{-- Process One --}}
        <div class="col-md-6">
            <button class="btn btn-sb mb-3 d-none" id="next-process-1" onclick="nextProcessOne()">NEXT</button>
            <div class="card card-sb d-none" id="detail-data-card">
                <div class="card-header">
                    <h3 class="card-title">Detail Data</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    <div class="row">
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>P. Marker</b></small></label>
                                <input type="number" class="form-control form-control-sm border-fetch" value="{{ $formCutInputData->panjang_marker }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Unit</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" value="{{ strtoupper($formCutInputData->unit_panjang_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-input"><small><b>P. Act</b></small></label>
                                <input type="number" class="form-control form-control-sm border-input" name="p_act" id="p_act" value="{{ $formCutInputData->p_act }}"
                                    onkeyup="
                                        calculateConsAct();
                                        calculateConsAmpar();
                                        calculateEstAmpar();
                                        calculateTotalPemakaian();
                                        calculateShortRoll();
                                        calculateRemark();
                                        // calculateSisaKain();
                                    "
                                    onchange="
                                        calculateConsAct();
                                        calculateConsAmpar();
                                        calculateEstAmpar();
                                        calculateTotalPemakaian();
                                        calculateShortRoll();
                                        calculateRemark();
                                        // calculateSisaKain();
                                    "
                                >
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-input"><small><b>Unit Act</b></small></label>
                                <input type="text" class="form-control form-control-sm border-input" name="unit_p_act" id="unit_p_act" value="{{ strtoupper($formCutInputData->unit_panjang_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Comma</b></small></label>
                                <input type="number" class="form-control form-control-sm border-fetch" value="{{ $formCutInputData->comma_marker }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Unit</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" value="{{ strtoupper($formCutInputData->unit_comma_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-input"><small><b>Comma Act</b></small></label>
                                <input type="number" class="form-control form-control-sm border-input" name="comma_act" id="comma_act" value="{{ $formCutInputData->comma_p_act }}"
                                    onkeyup="
                                        calculateConsAct();
                                        calculateConsAmpar();
                                        calculateEstAmpar();
                                        calculateTotalPemakaian();
                                        calculateShortRoll();
                                        calculateRemark();
                                        // calculateSisaKain();
                                    "
                                    onchange="
                                        calculateConsAct();
                                        calculateConsAmpar();
                                        calculateEstAmpar();
                                        calculateTotalPemakaian();
                                        calculateShortRoll();
                                        calculateRemark();
                                        // calculateSisaKain();
                                    "
                                >
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-input"><small><b>Unit Act</b></small></label>
                                <input type="text" class="form-control form-control-sm border-input" name="unit_comma_act" id="unit_comma_act" value="{{ strtoupper($formCutInputData->unit_comma_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>L. Marker</b></small></label>
                                <input type="number" class="form-control form-control-sm border-fetch" value="{{ strtoupper($formCutInputData->lebar_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Unit</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" value="{{ strtoupper($formCutInputData->unit_lebar_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-input"><small><b>L. Act</b></small></label>
                                <input type="number" class="form-control form-control-sm border-input" name="l_act"
                                    id="l_act" value="{{ $formCutInputData->l_act }}"
                                    onkeyup="
                                        calculateConsAmpar();
                                        calculateEstAmpar();
                                        calculateTotalPemakaian();
                                        calculateShortRoll();
                                        calculateRemark();
                                        // calculateSisaKain();
                                    "
                                    onchange="
                                        calculateConsAmpar();
                                        calculateEstAmpar();
                                        calculateTotalPemakaian();
                                        calculateShortRoll();
                                        calculateRemark();
                                        // calculateSisaKain();
                                    "
                                >
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-input"><small><b>Unit Act</b></small></label>
                                <input type="text" class="form-control form-control-sm border-input" name="unit_l_act" id="unit_l_act" value="{{ strtoupper($formCutInputData->unit_lebar_marker) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Gramasi</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" name="gramasi_marker" id="gramasi_marker"
                                    value="{{ $formCutInputData->gramasi_marker }}"
                                    onkeyup="calculateEstAmpar(undefined, undefined, undefined, this.value);"
                                    onchange="calculateEstAmpar(undefined, undefined, undefined, this.value);"
                                    readonly
                                >
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Cons WS</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" name="cons_ws" id="cons_ws" readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Cons Marker</b></small></label>
                                <input type="text" class="form-control form-control-sm border-fetch" name="cons_marker" id="cons_marker"
                                    value="{{ $formCutInputData->cons_marker }}"
                                    onkeyup="calculateEstKain(this.value)"
                                    onchange="calculateEstKain(this.value)"
                                    readonly
                                >
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-3">
                                <label class="form-label label-calc"><small><b>Cons Act</b></small></label>
                                <input type="number" class="form-control form-control-sm border-calc" name="cons_act" id="cons_act" step=".01"
                                    value="{{ round($formCutInputData->cons_act, 2) > 0 ? $formCutInputData->cons_act : ($totalQtyPly > 0 ? round($formCutInputData->p_act / $totalQtyPly, 2) : '0') }}"
                                    readonly
                                >
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="mb-3">
                                <label class="form-label label-fetch"><small><b>Cons Piping</b></small></label>
                                <input type="number" class="form-control form-control-sm border-fetch" step=".01" name="cons_piping" id="cons_piping"
                                    value="{{ $formCutInputData->cons_piping ? $formCutInputData->cons_piping : '0' }}" readonly
                                    onkeyup="calculateEstPiping(this.value)"
                                    onchange="calculateEstPiping(this.value)"
                                >
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="mb-3">
                                <label class="form-label label-calc"><small><b>Cons 1 Ampar</b></small></label>
                                <div class="row">
                                    <div class="col-8">
                                        <input type="number" class="form-control form-control-sm border-calc" step=".01" name="cons_ampar" id="cons_ampar" value="{{ $formCutInputData->cons_ampar }}" readonly>
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control form-control-sm border-calc" name="unit_cons_ampar" id="unit_cons_ampar" value="KGM" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label label-calc"><small><b>Est. Kebutuhan KainPiping</b></small></label>
                                <div class="row g-1">
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm border-calc" step=".01" name="est_piping" id="est_piping"
                                            value="{{ $formCutInputData->est_piping ? $formCutInputData->est_piping : '0.00' }}"
                                            readonly
                                        >
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control form-control-sm border-calc" name="unit_est_piping" id="unit_est_piping"
                                            value="{{ strtoupper($formCutInputData->unit_panjang_marker) }}"
                                            readonly
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label label-calc"><small><b>Est. Kebutuhan Kain</b></small></label>
                                <div class="row g-1">
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm border-calc" step=".01" name="est_kain" id="est_kain"
                                            value="{{ $formCutInputData->cons_marker * $totalQtyPly }}"
                                            readonly
                                        >
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control form-control-sm border-calc" name="unit_est_kain" id="unit_est_kain"
                                            value="{{ strtoupper($formCutInputData->unit_panjang_marker) }}"
                                            readonly
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Process Two --}}
        <div class="col-md-12">
            <button class="btn btn-sb mb-3 float-end d-none" id="next-process-2" onclick="nextProcessTwo()">NEXT</button>
            <div class="card card-sb d-none" id="scan-qr-card">
                <div class="card-header">
                    <h3 class="card-title">Scan QR</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    <div class="row justify-content-center align-items-end">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div id="reader"></div>
                            </div>
                        </div>
                        {{-- Method Switch --}}
                        <div class="col-md-12">
                            <div class="d-flex justify-content-center mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="switch-method" onchange="switchMethod(this)">
                                    <label class="form-check-label" id="to-scan">Scan Roll</label>
                                    <label class="form-check-label d-none" id="to-item">Pilih Barang</label>
                                </div>
                            </div>
                        </div>
                        {{-- Scan Method --}}
                        <div class="col-md-12" id="scan-method">
                            <div class="row align-items-end">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label label-input"><small><b>ID Roll</b></small></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm border-input" name="roll_id" id="roll_id">
                                            <button class="btn btn-sm btn-success" type="button" id="get-button" onclick="fetchScan()">Get</button>
                                            <button class="btn btn-sm btn-primary" type="button" id="scan-button" onclick="refreshScan()">Scan</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label label-scan"><small><b>ID Item</b></small></label>
                                        <input type="text" class="form-control form-control-sm border-scan" name="item_id" id="item_id" readonly>
                                    </div>
                                </div>
                                <div class="col-6 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label label-scan"><small><b>Detail Item</b></small></label>
                                        <input type="text" class="form-control form-control-sm border-scan" name="item_detail" id="item_detail" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label label-input"><small><b>Color Act</b></small></label>
                                        <input type="text" class="form-control form-control-sm border-input" name="item_color" id="item_color">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mt-auto mb-3">
                                        <button class="btn btn-sb btn-sm btn-block d-none" id="next-process-3" onclick="nextProcessThree()">START</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Item Method --}}
                        <div class="col-md-12 d-none" id="item-method">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label label-input"><small><b>Pilih Barang</b></small></label>
                                        <select class="form-select form-control-sm select2bs4" name="select_item" id="select_item" onchange="setSelectedItem(this)">
                                            <option value="">Pilih Barang</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mt-auto mb-3">
                                        <button class="btn btn-sb btn-sm btn-block" id="next-process-3-item" onclick="nextProcessThree()">START</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Spreading Process --}}
        <div class="col-md-12">
            <div class="card card-sb d-none" id="spreading-form-card">
                <div class="card-header">
                    <h3 class="card-title">Spreading</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    <form action="#" method="post" id="spreading-form">
                        <input type="hidden" id="method" name="method" readonly>
                        <input type="hidden" id="current_roll_id" name="current_roll_id" readonly>
                        <input type="hidden" id="sambungan_id" name="sambungan_id" readonly>
                        <input type="hidden" id="sambungan_status" name="sambungan_status" readonly>
                        <div class="row">
                            <div class="col-3">
                                <div class="mb-3">
                                    <label class="form-label label-input"><small><b>Group</b></small></label>
                                    <input type="text" class="form-control form-control-sm border-input" id="current_group" name="current_group">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label class="form-label label-scan" id="current_item_id_label"><small><b>ID Item</b></small></label>
                                    <input type="text" class="form-control form-control-sm border-scan" id="current_item_id" name="current_item_id" readonly>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label class="form-label label-scan" id="current_lot_label"><small><b>Lot</b></small></label>
                                    <input type="text" class="form-control form-control-sm border-scan" id="current_lot" name="current_lot" readonly>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label class="form-label label-scan" id="current_roll_label"><small><b>Roll</b></small></label>
                                    <input type="text" class="form-control form-control-sm border-scan" id="current_roll" name="current_roll" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label label-scan" id="current_qty_roll_label"><small><b>Qty</b></small></label>
                                <div class="d-flex mb-3">
                                    <div style="width: 60%">
                                        <input type="number" class="form-control form-control-sm border-scan" id="current_qty_roll" name="current_qty_roll"
                                            onchange="setRollQtyConversion(this.value); calculateEstAmpar();"
                                            onkeyup="setRollQtyConversion(this.value); calculateEstAmpar();"
                                            readonly
                                        >
                                    </div>
                                    <div style="width: 40%">
                                        <input type="text" class="form-control form-control-sm border-scan" id="current_unit_roll" name="current_unit_roll" readonly>

                                        <select class="form-select form-select-sm d-none rounded-0" name="current_unit_roll_custom" id="current_unit_roll_custom" onchange="setCustomUnit(this.value); setRollQtyConversion(); calculateEstAmpar()">
                                            <option value="METER">METER</option>
                                            <option value="KGM">KGM</option>
                                            <option value="YARD">YARD</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label label-calc" id="current_qty_label"><small><b>Qty Konversi</b></small></label>
                                    <div class="d-flex mb-3">
                                        <div style="width: 60%">
                                            <input type="number" class="form-control form-control-sm border-calc" id="current_qty" name="current_qty" readonly>
                                        </div>
                                        <div style="width: 40%">
                                            <input type="text" class="form-control form-control-sm border-calc" id="current_unit" name="current_unit" value="METER" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label label-input"><small><b>Sisa Gelaran</b></small></label>
                                    <div class="d-flex mb-3">
                                        <div style="width: 60%;">
                                            <input type="number" class="form-control form-control-sm border-input" id="current_sisa_gelaran" name="current_sisa_gelaran" step=".01"
                                                onkeyup="
                                                    // restrictRemainPly();
                                                    calculateShortRoll();
                                                    calculateRemark();
                                                "
                                                onchange="
                                                    // restrictRemainPly();
                                                    calculateShortRoll();
                                                    calculateRemark();
                                                "
                                            >
                                        </div>
                                        <div style="width: 40%;">
                                            <input type="text" class="form-control form-control-sm border-input" id="current_sisa_gelaran_unit" name="current_sisa_gelaran_unit" step=".01" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label label-input"><small><b>Sambungan</b></small></label>
                                    <div class="d-flex">
                                        <div style="width: 60%">
                                            <input type="number" class="form-control form-control-sm border-input" id="current_sambungan" name="current_sambungan" step=".01"
                                                onkeyup="
                                                    calculateTotalPemakaian();
                                                    calculateShortRoll();
                                                    // calculateSisaKain();
                                                "
                                                onchange="
                                                    calculateTotalPemakaian();
                                                    calculateShortRoll();
                                                    // calculateSisaKain();
                                                ">
                                        </div>
                                        <div style="width: 40%">
                                            <input type="text" class="form-control form-control-sm border-input" id="current_sambungan_unit" name="current_sambungan_unit" step=".01" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="form-label label-calc"><small><b>Estimasi Amparan</b></small></label>
                                    <input type="number" class="form-control form-control-sm border-calc" id="current_est_amparan" name="current_est_amparan" step=".01" readonly>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="form-label label-sb"><small><b>Lembar Gelaran</b></small></label>
                                    <input type="number" class="form-control form-control-sm border-sb" id="current_lembar_gelaran" name="current_lembar_gelaran" readonly
                                        onkeyup="
                                            calculateTotalPemakaian();
                                            calculateShortRoll();
                                            calculateRemark();
                                            // calculateSisaKain();
                                        "
                                        onchange="
                                            calculateTotalPemakaian();
                                            calculateShortRoll();
                                            calculateRemark();
                                            // calculateSisaKain();
                                        "
                                    >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="form-label label-sb"><small><b>Average Time</b></small></label>
                                    <input type="text" class="form-control form-control-sm border-sb" id="current_waktu_average" name="current_waktu_average" readonly>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label label-sb"><small><b>Ply Progress</b></small></label>
                                    <div class="progress border border-sb" style="height: 31px">
                                        <p class="position-absolute" style="top: 59%;left: 50%;transform: translate(-50%, -50%);" id="current_ply_progress_txt"></p>
                                        <div class="progress-bar" style="background-color: #75baeb;" role="progressbar" id="current_ply_progress"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label class="form-label label-input"><small><b>Kepala Kain</b></small></label>
                                    <div class="input-group input-group-sm mb-3">
                                        <input type="number" class="form-control border-input" id="current_kepala_kain" name="current_kepala_kain" step=".01"
                                            onkeyup="
                                                calculateTotalPemakaian();
                                                calculateShortRoll();
                                                // calculateSisaKain();
                                            "
                                            onchange="
                                                calculateTotalPemakaian();
                                                calculateShortRoll();
                                                // calculateSisaKain();
                                            "
                                        >
                                        <span class="input-group-text input-group-unit"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label class="form-label label-input"><small><b>Sisa Tidak Bisa</b></small></label>
                                    <div class="input-group input-group-sm mb-3">
                                        <input type="number" class="form-control border-input" id="current_sisa_tidak_bisa" name="current_sisa_tidak_bisa" step=".01"
                                            onkeyup="
                                                calculateTotalPemakaian();
                                                calculateShortRoll();
                                                calculateRemark();
                                                // calculateSisaKain();
                                            "
                                            onchange="
                                                calculateTotalPemakaian();
                                                calculateShortRoll();
                                                calculateRemark();
                                                // calculateSisaKain();
                                            "
                                        >
                                        <span class="input-group-text input-group-unit"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label class="form-label label-input"><small><b>Reject</b></small></label>
                                    <div class="input-group input-group-sm mb-3">
                                        <input type="number" class="form-control form-control-sm border-input" id="current_reject" name="current_reject" step=".01"
                                            onkeyup="
                                                calculateTotalPemakaian();
                                                calculateShortRoll();
                                                calculateRemark();
                                                // calculateSisaKain();
                                            "
                                            onchange="
                                                calculateTotalPemakaian();
                                                calculateShortRoll();
                                                calculateRemark();
                                                // calculateSisaKain();
                                            "
                                        >
                                        <span class="input-group-text input-group-unit"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label class="form-label label-input"><small><b>Piping</b></small></label>
                                    <div class="input-group input-group-sm mb-3">
                                        <input type="number" class="form-control form-control-sm border-input" id="current_piping" name="current_piping" step=".01"
                                            onkeyup="
                                                calculateShortRoll();
                                                calculateRemark();
                                                // calculateSisaKain();
                                            "
                                            onchange="
                                                calculateShortRoll();
                                                calculateRemark();
                                                // calculateSisaKain();
                                            "
                                        >
                                        <span class="input-group-text input-group-unit"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label class="form-label label-calc"><small><b>Tot. Pakai /Roll</b></small></label>
                                    <div class="input-group input-group-sm mb-3">
                                        <input type="number" class="form-control form-control-sm border-calc" id="current_total_pemakaian_roll" name="current_total_pemakaian_roll" step=".01" readonly>
                                        <span class="input-group-text input-group-unit"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label class="form-label label-calc"><small><b>Short Roll +/-</b></small></label>
                                    <div class="input-group input-group-sm mb-3">
                                        <input type="number" class="form-control form-control-sm border-calc" id="current_short_roll" name="current_short_roll" step=".01" readonly>
                                        <span class="input-group-text input-group-unit"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label class="form-label label-input"><small><b>Sisa Kain</b></small></label>
                                    <div class="input-group input-group-sm mb-3">
                                        <input type="number" class="form-control form-control-sm border-input" id="current_sisa_kain" name="current_sisa_kain" step=".01"
                                            onkeyup="
                                                calculateShortRoll();
                                                calculateRemark();
                                            "
                                            onchange="
                                                calculateShortRoll();
                                                calculateRemark();
                                            "
                                        >
                                        <span class="input-group-text input-group-unit"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label class="form-label label-input"><small><b>Remark</b></small></label>
                                    <div class="input-group input-group-sm mb-3">
                                        <input type="number" class="form-control form-control-sm border-input" id="current_remark" name="current_remark" step=".01">
                                        <span class="input-group-text input-group-unit"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-6 my-3">
                                <button type="button" class="fs-5 fw-bold btn btn-success btn-sm w-100 h-100" style="min-height: 90px !important;" id="startLapButton"onclick="startTimeRecord()">START</button>
                                <button type="button" class="fs-5 fw-bold btn btn-primary btn-sm d-none w-100 h-100" style="min-height: 90px !important;" id="nextLapButton"onclick="addNewTimeRecord()">NEXT LAP</button>
                            </div>
                            <div class="col-6 col-md-6 my-3">
                                <div class="row">
                                    <div class="col-5">
                                        <input type="text" class="form-control form-control-sm" id="minutes" value="00" readonly class="mx-1">
                                    </div>
                                    <div class="col-2">
                                        <center>:</center>
                                    </div>
                                    <div class="col-5">
                                        <input type="text" class="form-control form-control-sm" id="seconds" value="00" readonly class="mx-1">
                                    </div>
                                </div>
                                <div class="w-100 table-responsive mt-3" style="max-height: 150px; overflow-y: auto;">
                                    <table class="table table-bordered table-sm" id="timeRecordTable">
                                        <thead>
                                            <tr>
                                                <th>Lap</th>
                                                <th>Waktu</th>
                                                <th class="d-none"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-sb btn-sm btn-block my-3" id="stopLapButton" onclick="stopTimeRecord()">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Loss Time --}}
        <div class="col-md-12">
            <div class="card card-sb collapsed-card d-none" id="loss-time-card">
                <div class="card-header">
                    <h3 class="card-title">Loss Time</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    <div class="row">
                        <div class="col-6 col-md-6 my-3">
                            <button type="button" class="fs-5 fw-bold btn btn-danger btn-sm w-100 h-100" style="min-height: 90px !important;" id="startLossButton" onclick="startLossTime()">START</button>
                            <button type="button" class="fs-5 fw-bold btn btn-warning btn-sm d-none w-100 h-100" style="min-height: 90px !important;" id="nextLossButton" onclick="addNewLossTime()">STOP</button>
                        </div>
                        <div class="col-6 col-md-6 my-3">
                            <div class="row">
                                <div class="col-5">
                                    <input type="text" class="form-control form-control-sm" id="lostMinutes" value="00" readonly class="mx-1">
                                </div>
                                <div class="col-2">
                                    <center>:</center>
                                </div>
                                <div class="col-5">
                                    <input type="text" class="form-control form-control-sm" id="lostSeconds" value="00" readonly class="mx-1">
                                </div>
                            </div>
                            <div class="w-100 h-100 table-responsive mt-3" style="max-height: 150px; overflow-y: auto;">
                                <form action="#" method="post" id="loss-time-form">
                                    <input type="hidden" id="current_loss_time" name="current_loss_time">
                                    <table class="table table-bordered table-sm" id="lossTimeTable">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Waktu</th>
                                                <th class="d-none"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rolls --}}
        <div class="col-md-12">
            <div class="card card-sb d-none" id="summary-card">
                <div class="card-header">
                    <h3 class="card-title">Summary</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="w-100 table-responsive my-3">
                                <table class="table table-bordered table-sm" id="scannedItemTable">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Group</th>
                                            <th>Group Number</th>
                                            <th class="label-scan">ID Roll</th>
                                            <th class="label-scan">ID Item</th>
                                            <th class="label-scan">Lot</th>
                                            <th class="label-scan">Roll</th>
                                            <th class="label-scan">Qty</th>
                                            <th class="label-scan">Unit</th>
                                            <th>Sisa Gelaran</th>
                                            <th>Sambungan</th>
                                            <th class="label-calc">Estimasi Amparan</th>
                                            <th>Lembar Gelaran</th>
                                            <th>Average Time</th>
                                            <th>Kepala Kain</th>
                                            <th>Sisa Tidak Bisa</th>
                                            <th>Reject</th>
                                            <th>Sisa Kain</th>
                                            <th class="label-calc">Total Pemakaian Per Roll</th>
                                            <th class="label-calc">Short Roll +/-</th>
                                            <th>Piping</th>
                                            <th>Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="7" class="text-center">Total</th>
                                            <th id="total-qty"></th>
                                            <th id="total-unit"></th>
                                            <th id="total-sisa-gelaran"></th>
                                            <th id="total-sambungan"></th>
                                            <th id="total-est-amparan"></th>
                                            <th id="total-lembar"></th>
                                            <th id="total-average-time"></th>
                                            <th id="total-kepala-kain"></th>
                                            <th id="total-sisa-tidak-bisa"></th>
                                            <th id="total-reject"></th>
                                            <th id="total-sisa-kain"></th>
                                            <th id="total-total-pemakaian"></th>
                                            <th id="total-short-roll"></th>
                                            <th id="total-piping"></th>
                                            <th id="total-remark"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-labe label-calc"><small><b>Cons. Actual 1 Gelaran</b></small></label>
                                        <input type="text" class="form-control form-control-sm border-calc" name="cons_actual_gelaran" id="cons_actual_gelaran" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label label-calc"><small><b>Unit</b></small></label>
                                        <select class="form-select form-select-sm border-calc" name="unit_cons_actual_gelaran" id="unit_cons_actual_gelaran" disabled>
                                            <option value="meter">METER</option>
                                            <option value="yard">YARD</option>
                                            <option value="kgm">KGM</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-labe label-calc"><small><b>Kenaikan Cons. WS</b></small></label>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="text" class="form-control border-calc" name="cons_ws_uprate" id="cons_ws_uprate" readonly>
                                            <span class="input-group-text border-calc">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-labe label-calc"><small><b>Kenaikan Cons. Marker</b></small></label>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="text" class="form-control border-calc" name="cons_marker_uprate" id="cons_marker_uprate" readonly>
                                            <span class="input-group-text border-calc">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-labe label-calc"><small><b>Cons. Actual 1 Gelaran Tanpa Short Roll</b></small></label>
                                        <input type="text" class="form-control form-control-sm border-calc" name="cons_actual_gelaran_short_rolless" id="cons_actual_gelaran_short_rolless" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label label-calc"><small><b>Unit</b></small></label>
                                        <select class="form-select form-select-sm border-calc" name="unit_cons_actual_gelaran_short_rolless" id="unit_cons_actual_gelaran_short_rolless" disabled>
                                            <option value="meter">METER</option>
                                            <option value="yard">YARD</option>
                                            <option value="kgm">KGM</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-labe label-calc"><small><b>Kenaikan Cons. WS</b></small></label>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="text" class="form-control border-calc" name="cons_ws_uprate_nosr" id="cons_ws_uprate_nosr" readonly>
                                            <span class="input-group-text border-calc">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-labe label-calc"><small><b>Kenaikan Cons. Marker</b></small></label>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="text" class="form-control border-calc" name="cons_marker_uprate_nosr" id="cons_marker_uprate_nosr" readonly>
                                            <span class="input-group-text border-calc">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label label-input"><small><b>Operator</b></small></label>
                                        <input type="text" class="form-control form-control-sm border-input" name="operator" id="operator" value="{{ $formCutInputData->operator }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Finish --}}
        <div class="col-md-12">
            <button class="btn btn-block btn-sb d-none" id="finish-process" onclick="finishProcess()">SELESAI PENGERJAAN</button>
        </div>
    </div>
@endsection

@section('custom-script')
    <script>
        // Variable List :
            // -Form Cut Input Header Data-
            var id = document.getElementById("id").value;
            var noForm = document.getElementById("no_form").value;
            var statusForm = document.getElementById("status_form").value;
            var startTime = document.getElementById("start-time");
            var finishTime = document.getElementById("finish-time");

            // -Process Button Elements-
            var startProcessButton = document.getElementById("start-process");
            var nextProcessOneButton = document.getElementById("next-process-1");
            var nextProcessTwoButton = document.getElementById("next-process-2");
            var nextProcessThreeButton = document.getElementById("next-process-3");
            var finishProcessButton = document.getElementById("finish-process");

            // -Current Scanned Item-
            var currentScannedItem = null;
            var currentSisaGelaran = null;

            // -Summary Data-
            var summaryData = null;

            // -Ratio & Qty Cuy-
            var totalRatio = document.getElementById('total_ratio').value;
            var totalQtyCut = document.getElementById('total_qty_ply').value;

            // -Method-
            var method = "scan";

        // Function List :
            // -On Load-
            $(document).ready(async () => {
                document.getElementById("loading").classList.remove("d-none");

                await getNumberData();
                await clearGeneralForm();
                await clearScanItemForm();
                await clearSpreadingForm();
                await checkStatus();

                document.getElementById("loading").classList.add("d-none");

                // -Global Key Event-
                $(document).keyup(function(e) {
                    if (e.key === "Escape") {
                        e.preventDefault();

                        // stopTimeRecord()
                    }
                });

                // -Kode Barang Manual Input Event-
                $('#roll_id').keyup(function(e) {
                    if (e.key === "Enter") {
                        e.preventDefault();

                        getScannedItem(this.value);
                    }
                });

                // -Trigger Next Lap Button on Key Up 'Enter'-
                nextLapButton.addEventListener("keyup", function(evt) {
                    if (evt.key === "Enter") {
                        // Cancel the default action, if needed
                        event.preventDefault();
                        // Trigger the button element with a click
                        nextLapButton.click();
                    }
                });

                // // -On Scan Card Collapse-
                // $('#scan-qr-card').on('collapsed.lte.cardwidget', function(e) {
                //     clearQrCodeScanner();
                // });

                // -On Scan Card Expand-
                $('#scan-qr-card').on('expanded.lte.cardwidget', function(e) {
                    refreshScan();
                });

                // -On Custom Unit Change-
                $('#current_unit_roll_custom').on('change', function(e) {
                    console.log("testing");
                    $('#current_unit').val(this.value);
                });

                // -Default Method-
                await $('#switch-method').prop('checked', true);
            });

        // Process :
            // -Start Process-
            function startProcess() {
                Swal.fire({
                    icon: 'info',
                    title: 'Approve Pengerjaan Form Cut?',
                    text: 'Yakin akan approve proses pengerjaan?',
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonText: 'Approve',
                    confirmButtonColor: "#6531a0",
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        let now = new Date();
                        startTime.value = now.getFullYear().toString() + "-" + pad(now.getMonth() + 1) + "-" + pad(now.getDate()) + " " + pad(now.getHours()) + ":" + pad(now.getMinutes()) + ":" + pad(now.getSeconds());

                        updateToStartProcess();

                        startProcessButton.classList.add("d-none");
                        nextProcessOneButton.classList.remove("d-none");
                        document.getElementById("loss-time-card").classList.remove("d-none");
                    }
                });
            }

            // -Start Process Transaction-
            function updateToStartProcess() {
                return $.ajax({
                    url: '{{ route('start-process-form-cut-input') }}/' + id,
                    type: 'put',
                    dataType: 'json',
                    data: {
                        startTime: startTime.value,
                    },
                    success: function(res) {
                        if (res) {
                            console.log(res);

                            statusForm = "form";
                        }
                    }
                });
            }

            // -Process One-
            function nextProcessOne() {
                updateToNextProcessOne();

                $('#header-data-card').CardWidget('collapse');
                $('#detail-data-card').removeClass('d-none');

                nextProcessOneButton.classList.add("d-none");
                nextProcessTwoButton.classList.remove("d-none");
            }

            // -Process One Transaction-
            function updateToNextProcessOne() {
                return $.ajax({
                    url: '{{ route('next-process-one-form-cut-input') }}/' + id,
                    type: 'put',
                    dataType: 'json',
                    data: {
                        shell: $("#shell").val()
                    },
                    success: function(res) {
                        if (res) {
                            statusForm = "form detail";
                        }
                    }
                });
            }

            // -Process Two-
            function nextProcessTwo() {
                updateToNextProcessTwo();
            }

            // -Process Two Transaction-
            function updateToNextProcessTwo() {
                var pActual = document.getElementById('p_act').value;
                var pUnitActual = document.getElementById('unit_p_act').value;
                var commaActual = document.getElementById('comma_act').value;
                var commaUnitActual = document.getElementById('unit_comma_act').value;
                var lActual = document.getElementById('l_act').value;
                var lUnitActual = document.getElementById('unit_l_act').value;
                var consActual = document.getElementById('cons_act').value;
                var consPipping = document.getElementById('cons_piping').value;
                var consAmpar = document.getElementById('cons_ampar').value;
                var unitConsAmpar = document.getElementById('unit_cons_ampar').value;
                var estPiping = document.getElementById('est_piping').value;
                var unitEstPiping = document.getElementById('unit_est_piping').value;
                var estKain = document.getElementById('est_kain').value;
                var unitEstKain = document.getElementById('unit_est_kain').value;

                clearModified();

                return $.ajax({
                    url: '{{ route('next-process-two-form-cut-input') }}/' + id,
                    type: 'put',
                    dataType: 'json',
                    data: {
                        p_act: pActual,
                        unit_p_act: pUnitActual,
                        comma_act: commaActual,
                        unit_comma_act: commaUnitActual,
                        l_act: lActual,
                        unit_l_act: lUnitActual,
                        cons_act: consActual,
                        cons_piping: consPipping,
                        cons_ampar: consAmpar,
                        unit_cons_ampar: unitConsAmpar,
                        est_piping: estPiping,
                        unit_est_piping: unitEstPiping,
                        est_kain: estKain,
                        unit_est_kain: unitEstKain,
                    },
                    success: function(res) {
                        if (res) {
                            if (res.status == 200) {
                                $('#header-data-card').CardWidget('collapse');
                                $('#detail-data-card').CardWidget('collapse');
                                $('#scan-qr-card').removeClass('d-none');

                                nextProcessTwoButton.classList.add("d-none");
                                nextProcessThreeButton.classList.remove("d-none");

                                initScan();
                                getItemList();

                                statusForm = "form spreading";
                            }
                        }
                    },
                    error: function(jqXHR) {
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

            // -Process Three-
            function nextProcessThree() {
                updateToNextProcessThree();
            }

            // -Process Three Transaction-
            async function updateToNextProcessThree() {
                let validation = false;

                switch (method) {
                    case "scan" :
                        validation = isNotNull(document.getElementById("item_id").value) && isNotNull(document.getElementById("item_detail").value) && isNotNull(document.getElementById("item_color").value);
                        break;
                    case "item" :
                        validation = isNotNull(document.getElementById("select_item").value);
                        break;
                    default :
                        validation = isNotNull(document.getElementById("item_id").value) && isNotNull(document.getElementById("item_detail").value) && isNotNull(document.getElementById("item_color").value);
                        break;
                }

                if (validation && currentScannedItem) {
                    nextProcessThreeButton.classList.add("d-none");

                    $('#scan-qr-card').CardWidget('collapse');

                    setSpreadingForm(currentScannedItem, sisaGelaran, unitSisaGelaran);
                    getSummary();

                    $('#spreading-form-card').removeClass('d-none');
                    $('#spreading-form-card').CardWidget('expand');
                    $('#summary-card').removeClass('d-none');

                    location.href = "#spreading-form-card";
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Pastikan item yang di scan tersedia dan color actual sudah diisi',
                        showCancelButton: false,
                        showConfirmButton: true,
                        confirmButtonText: 'Oke',
                    })
                }
            }

            // -Back to Process Three-
            async function backToProcessThree() {
                storeTimeRecord();
            }

            // -Store Time Record Transaction-
            function storeTimeRecord() {
                document.getElementById("loading").classList.remove("d-none");

                clearModified();

                let spreadingForm = new FormData(document.getElementById("spreading-form"));

                let dataObj = {
                    "p_act": $("#p_act").val(),
                    "unit_p_act": $("#unit_p_act").val(),
                    "comma_act": $("#comma_act").val(),
                    "no_form": $("#no_form").val(),
                    "meja_id": $("#meja_id").val(),
                    "item_color": $("#item_color").val(),
                    "item_detail": $("#item_detail").val(),
                    "metode": method,
                }

                spreadingForm.forEach((value, key) => dataObj[key] = value);

                if ($("#sambungan_status").val() != "extension") {
                    // Not an Extension :
                    return $.ajax({
                        url: '{{ route('store-time-form-cut-input') }}',
                        type: 'post',
                        dataType: 'json',
                        data: dataObj,
                        success: function(res) {
                            document.getElementById("loading").classList.add("d-none");

                            if (res) {
                                timeRecordTableTbody.innerHTML = "";

                                clearScanItemForm();
                                openScanItemForm();
                                clearSpreadingForm();
                                firstTimeRecordCondition();

                                nextProcessThreeButton.classList.remove('d-none');

                                if (res.additional.length > 0) {
                                    $('#summary-card').removeClass('d-none');

                                    appendScannedItem(res.additional[0]);

                                    if (res.additional.length > 1) {
                                        if (res.additional[1]) {
                                            sisaGelaran = res.additional[0].sisa_gelaran;
                                            unitSisaGelaran = res.additional[0].unit;
                                            setSpreadingForm(res.additional[1], sisaGelaran, unitSisaGelaran);
                                        } else {
                                            checkStatus();
                                        }
                                    }
                                }

                                resetTimeRecord();
                            }
                        },
                        error: function(jqXHR) {
                            document.getElementById("loading").classList.add("d-none");

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
                } else {
                    // An Extension :

                    return $.ajax({
                        url: '{{ route('store-time-ext-form-cut-input') }}',
                        type: 'post',
                        dataType: 'json',
                        data: dataObj,
                        success: function(res) {
                            document.getElementById("loading").classList.add("d-none");

                            if (res) {
                                timeRecordTableTbody.innerHTML = "";

                                clearScanItemForm();
                                openScanItemForm();
                                clearSpreadingForm();
                                firstTimeRecordCondition();

                                nextProcessThreeButton.classList.remove('d-none');

                                if (res.additional.length > 0) {
                                    $('#summary-card').removeClass('d-none');

                                    appendScannedItem(res.additional[0]);

                                    if (res.additional.length > 1) {
                                        if (res.additional[1]) {
                                            sisaGelaran = res.additional[0].sisa_gelaran;
                                            unitSisaGelaran = res.additional[0].unit;
                                            setSpreadingForm(res.additional[1], sisaGelaran, unitSisaGelaran);
                                        } else {
                                            checkStatus();
                                        }
                                    }
                                }

                                resetTimeRecord();
                            }
                        },
                        error: function(jqXHR) {
                            document.getElementById("loading").classList.add("d-none");

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
            }

            // -Store This Time Record Transaction-
            function storeThisTimeRecord() {
                document.getElementById("loading").classList.remove("d-none");

                let spreadingForm = new FormData(document.getElementById("spreading-form"));

                let dataObj = {
                    "no_form": $("#no_form").val(),
                    "item_color": $("#item_color").val(),
                    "item_detail": $("#item_detail").val(),
                    "meja_id": $("#meja_id").val(),
                    "metode": method,
                    "lap": lap
                }

                spreadingForm.forEach((value, key) => dataObj[key] = value);

                return $.ajax({
                    url: '{{ route('store-this-time-form-cut-input') }}',
                    type: 'post',
                    dataType: 'json',
                    data: dataObj,
                    success: function(res) {
                        document.getElementById("loading").classList.add("d-none");

                        if (res) {
                            console.log(res);
                        }
                    }, error: function(jqXHR) {
                        document.getElementById("loading").classList.add("d-none");

                        console.log(jqXHR);

                        Swal.fire({
                            icon: 'error',
                            title: 'Koneksi Hilang',
                            text: 'Terjadi Kesalahan',
                            showConfirmButton: true,
                            confirmButtonText: 'Oke',
                            confirmButtonColor: "#6531a0",
                        });
                    }
                });
            }

            // -Finish Process-
            function finishProcess() {
                let now = new Date();
                finishTime.value = now.getFullYear().toString() + "-" + pad(now.getMonth() + 1) + "-" + pad(now.getDate()) +" " + pad(now.getHours()) + ":" + pad(now.getMinutes()) + ":" + pad(now.getSeconds());

                if ($("#operator").val() == "" || $("#cons_actual_gelaran").val() == "") {
                    return Swal.fire({
                        icon: 'error',
                        title: 'Tidak Dapat Menyelesaikan Proses',
                        text: 'Harap pastikan data "Operator" dan "Cons. Actual 1 Gelaran" telah terisi',
                        showConfirmButton: true,
                        confirmButtonText: 'Oke',
                        confirmButtonColor: "#6531a0",
                    });
                }

                updateToFinishProcess();
            }

            // -Finish Process Transaction-
            function updateToFinishProcess() {
                Swal.fire({
                    icon: 'info',
                    title: 'Selesaikan Pengerjaan?',
                    text: 'Yakin akan menyelesaikan proses pengerjaan?',
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonText: 'Selesaikan',
                    confirmButtonColor: "#6531a0",
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        document.getElementById("loading").classList.remove("d-none");

                        await updateToNextProcessOne();
                        await updateToNextProcessTwo();

                        $.ajax({
                            url: '{{ route('finish-process-form-cut-input') }}/' + id,
                            type: 'put',
                            dataType: 'json',
                            data: {
                                finishTime: finishTime.value,
                                operator: $('#operator').val(),
                                consAct: $('#cons_actual_gelaran').val(),
                                unitConsAct: $('#unit_cons_actual_gelaran').val(),
                                consActNoSr: $('#cons_actual_gelaran_short_rolless').val(),
                                unitConsActNoSr: $('#unit_cons_actual_gelaran_short_rolless').val(),
                                consWsUprate: $('#cons_ws_uprate').val(),
                                consMarkerUprate: $('#cons_marker_uprate').val(),
                                consWsUprateNoSr: $('#cons_ws_uprate_nosr').val(),
                                consMarkerUprateNoSr: $('#cons_marker_uprate_nosr').val(),
                                totalPly: totalPly
                            },
                            success: function(res) {
                                document.getElementById("loading").classList.add("d-none");

                                if (res) {
                                    lockFormCutInput();

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'Proses telah berhasil diselesaikan (Laman akan ditutup)',
                                        showCancelButton: false,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Oke',
                                        timer: 3000,
                                        timerProgressBar: true
                                    }).then((result) => {
                                        window.close();
                                    })

                                    statusForm = "finish";
                                }
                            },
                            error: function(jqXHR) {
                                document.getElementById("loading").classList.add("d-none");

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan',
                                    showCancelButton: false,
                                    showConfirmButton: true,
                                    confirmButtonText: 'Oke',
                                });
                            }
                        });
                    }
                });
            }

            // -Lock Process-
            function lockProcessCondition() {
                startProcessButton.disabled = true;
                nextProcessOneButton.disabled = true;
                nextProcessTwoButton.disabled = true;
                nextProcessThreeButton.disabled = true;
                finishProcessButton.disabled = true;
            }

            // -Calculate P. Actual + Comma Actual-
            function pActualCommaActual(pActualVar, unitPActualVar, commaActualVar) {
                let pActualFinal = 0;

                if (unitPActualVar == "YARD" || unitPActualVar == "YRD") {
                    let commaMeter = commaActualVar / 36;

                    pActualFinal = (pActualVar + commaMeter);
                } else if (unitPActualVar == "METER") {
                    let commaMeter = commaActualVar / 100;

                    pActualFinal = (pActualVar + commaMeter);
                }

                return pActualFinal;
            }

            // -Convert P. Actual-
            function pActualConversion(pActualVar, unitPActualVar, commaActualVar, lActualVar, gramasiVar, unitQtyVar) {
                let pActualConverted = 0;

                if (unitQtyVar == unitPActualVar) {
                    if (unitPActualVar == "YARD" || unitPActualVar == "YRD") {
                        pActualConverted = pActualVar + (commaActualVar / 36);
                    } else if (unitPActualVar == "METER") {
                        pActualConverted = pActualVar + (commaActualVar / 100);
                    }
                } else {
                    // YARD
                    if (unitPActualVar == "YARD" || unitPActualVar == "YRD") {
                        let pActualInch = ((pActualVar * 36 / 1) + commaActualVar)

                        if (unitQtyVar == "METER") {
                            pActualConverted = pActualInch * 0.0254;
                        } else if (unitQtyVar == "KGM") {
                            let gramasiInch = gramasiVar / 1550;

                            pActualConverted = ((gramasiInch * (pActualInch * lActualVar)) / 1000);
                        } else {
                            pActualConverted = pActualVar + (commaActualVar / 36);
                        }

                        // METER
                    } else if (unitPActualVar == "METER") {
                        let pActualInch = ((pActualVar * 39.3701) + (commaActualVar / 2.54));
                        let lActualInch = lActualVar / 2.54;

                        if (unitQtyVar == "YARD" || unitQtyVar == "YRD") {
                            pActualConverted = pActualInch / 36;
                        } else if (unitQtyVar == "KGM") {
                            let gramasiInch = gramasiVar / 1550;

                            pActualConverted = ((gramasiInch * (pActualInch * lActualInch)) / 1000);
                        } else {
                            pActualConverted = pActualVar + (commaActualVar / 100);
                        }
                    }
                }

                return pActualConverted;
            }

            // -Convert Roll Qty Actual-
            function rollQtyConversion(rollQtyVar, unitQtyVar, gramasi = 0, pActual = 0, lActual = 0, commaActual = 0) {
                let rollQtyConverted = 0;
                let gramasiVar = gramasi > 0 ? Number(gramasi) : Number(document.getElementById("gramasi_marker").value);
                let pActualVar = pActual > 0 ? Number(pActual) : Number(document.getElementById("p_act").value);
                let lActualVar = lActual > 0 ? Number(lActual) : Number(document.getElementById("l_act").value);
                let commaActualVar = commaActual > 0 ? Number(commaActual) : Number(document.getElementById("comma_act").value);

                if (rollQtyVar && unitQtyVar) {
                    if (unitQtyVar == "YARD" || unitQtyVar == "YRD") {
                        // YARD
                        rollQtyConverted = rollQtyVar * 0.9144;

                    } else if (unitQtyVar == "KGM") {
                        // KGM
                        let gramasiConverted = gramasiVar / 1000;
                        let lActualConverted = lActualVar / 100;

                        rollQtyConverted = rollQtyVar / (gramasiConverted * lActualConverted);

                    } else {
                        // METER

                        rollQtyConverted = rollQtyVar;
                    }

                    return Number(rollQtyConverted).round(2);
                }

                return null;
            }

            function setRollQtyConversion(rollQty = 0, unitQty) {
                let rollQtyVar = rollQty > 0 ? Number(rollQty) : Number(document.getElementById("current_qty_roll").value);
                let unitQtyVar = unitQty ? unitQty : document.getElementById("current_unit_roll").value;

                document.getElementById("current_qty").value = rollQtyConversion(rollQtyVar, unitQtyVar);
            }

            function conversion(qty, unit, unitBefore) {
                console.log("convert", qty, unitBefore, unit);

                let gramasiVar = Number(document.getElementById("gramasi_marker").value);
                let pActualVar = Number(document.getElementById("p_act").value);
                let lActualVar = Number(document.getElementById("l_act").value);
                let commaActualVar = Number(document.getElementById("comma_act").value);

                let qtyConverted = 0;

                if (qty && unit && unitBefore) {
                    if (unit == unitBefore) {
                        qtyConverted = qty;
                    } else {
                        if (unitBefore == "KGM" && unit == "METER") {
                            // KGM
                            let gramasiConverted = gramasiVar / 1000;
                            let lActualConverted = lActualVar / 100;

                            qtyConverted = qty / (gramasiConverted * lActualConverted);
                        } else if (unitBefore == "METER" && unit == "KGM") {
                            let gramasiInch = gramasiVar / 1550;
                            let qtyInch = qty * 39.3701;
                            let lActualInch = lActualVar / 2.54;

                            qtyConverted = (gramasiInch * (qtyInch * lActualInch)) / 1000;
                        }
                    }

                    return Number(qtyConverted).round(2);
                }

                return null;
            }

            // -Restrict Sisa Gelaran-
            function restrictRemainPly() {
                let estSambungan = calculateSambungan();

                if (estSambungan <= 0) {
                    // document.getElementById('current_sisa_gelaran').value = 0;

                    iziToast.warning({
                        title: 'Warning',
                        message: 'Sisa gelaran telah melebihi Panjang actual',
                        position: 'topCenter'
                    });
                }
            }

            // -Calculate Cons Ampar-
            function calculateConsAmpar() {
                let pActualVar = Number(document.getElementById("p_act").value);
                let unitPActualVar = document.getElementById("unit_p_act").value;
                let commaActualVar = Number(document.getElementById("comma_act").value);
                let lActualVar = Number(document.getElementById("l_act").value);
                let gramasiVar = Number(document.getElementById("gramasi_marker").value);
                let lActualMeter = lActualVar / 100;

                let pActualFinal = pActualCommaActual(pActualVar, unitPActualVar, commaActualVar);

                consAmpar = totalRatio > 0 ? (gramasiVar * pActualFinal * lActualMeter) / 1000 : 0;

                document.getElementById('cons_ampar').value = consAmpar.round(2);
            }

            // -Calculate Cons Act-
            function calculateConsAct() {
                let pActualVar = Number(document.getElementById("p_act").value);
                let unitPActualVar = document.getElementById("unit_p_act").value;
                let commaActualVar = Number(document.getElementById("comma_act").value);

                let consActual = 0;

                let pActualFinal = pActualCommaActual(pActualVar, unitPActualVar, commaActualVar);

                // consActual = totalQtyCut > 0 ? pActualFinal / totalQtyCut : 0;
                consActual = totalQtyCut > 0 ? pActualFinal / totalRatio : 0;

                document.getElementById('cons_act').value = consActual.round(2);
            }

            // -Calculate Est. Piping-
            function calculateEstPiping(consPipping = 0) {
                let consPippingVar = consPipping;

                let estPiping = consPippingVar * totalQtyCut;

                document.getElementById('est_piping').value = estPiping.round(2);
            }

            // -Calculate Est. Kain-
            function calculateEstKain(consMarker = 0) {
                let consMarkerVar = consMarker;

                let estKain = consMarkerVar * totalQtyCut

                document.getElementById('est_kain').value = estKain.round(2);
            }

            // -Calculate Est. Ampar-
            function calculateEstAmpar() {
                let qtyVar = Number(document.getElementById("current_qty").value);
                let pActualVar = Number(document.getElementById("p_act").value);
                let lActualVar = Number(document.getElementById("l_act").value);
                let commaActualVar = Number(document.getElementById("comma_act").value);
                let unitQtyVar = document.getElementById("current_unit_roll").value;
                let unitPActualVar = document.getElementById("unit_p_act").value;
                let unitCommaActualVar = document.getElementById("unit_comma_act").value;
                let gramasiVar = Number(document.getElementById("gramasi_marker").value);

                let pActualConverted = 0;

                if (unitQtyVar != "KGM") {
                    pActualConverted = pActualCommaActual(pActualVar, unitPActualVar, commaActualVar);
                } else {
                    qtyVar = Number(document.getElementById("current_qty_roll").value);

                    pActualConverted = pActualConversion(pActualVar, unitPActualVar, commaActualVar, lActualVar, gramasiVar, unitQtyVar);
                }

                let estAmpar = pActualConverted > 0 ? qtyVar / pActualConverted : 0;

                document.getElementById("current_est_amparan").value = estAmpar.round(2);
            }

            // -Calculate Total Pemakaian Roll-
            function calculateTotalPemakaian() {
                let lembarGelaranVar = Number(document.getElementById("current_lembar_gelaran").value);
                let pActualVar = Number(document.getElementById("p_act").value);
                let kepalaKainVar = Number(document.getElementById("current_kepala_kain").value);
                let sisaTidakBisaVar = Number(document.getElementById("current_sisa_tidak_bisa").value);
                let rejectVar = Number(document.getElementById("current_reject").value);
                let lActualVar = Number(document.getElementById("l_act").value);
                let gramasiVar = Number(document.getElementById("gramasi_marker").value);
                let unitQtyVar = document.getElementById("current_unit_roll").value;
                let unitPActualVar = document.getElementById("unit_p_act").value;
                let commaActualVar = Number(document.getElementById("comma_act").value);

                let pActualConverted = 0;

                if (document.getElementById("sambungan_status").value == "extension") {
                    pActualConverted = document.getElementById("current_sambungan").value;
                } else {
                    if (unitQtyVar != "KGM") {
                        pActualConverted = pActualCommaActual(pActualVar, unitPActualVar, commaActualVar);
                    } else {
                        qtyVar = Number(document.getElementById("current_qty_roll").value);

                        pActualConverted = pActualConversion(pActualVar, unitPActualVar, commaActualVar, lActualVar, gramasiVar, unitQtyVar);
                    }
                }

                // let totalPemakaian = lembarGelaranVar * pActualConverted + kepalaKainVar + sisaTidakBisaVar + rejectVar;
                let totalPemakaian = lembarGelaranVar * pActualConverted;

                document.getElementById("current_total_pemakaian_roll").value = totalPemakaian.round(2);
            }

            // -Calculate Short Roll-
            function calculateShortRoll() {
                let lembarGelaranVar = Number(document.getElementById("current_lembar_gelaran").value);
                let pActualVar = Number(document.getElementById("p_act").value);
                let kepalaKainVar = Number(document.getElementById("current_kepala_kain").value);
                let pipingVar = Number(document.getElementById("current_piping").value);
                let sisaKainVar = Number(document.getElementById("current_sisa_kain").value);
                let rejectVar = Number(document.getElementById("current_reject").value);
                let sambunganVar = Number(document.getElementById("current_sambungan").value);
                let qtyVar = Number(document.getElementById("current_qty").value);
                let unitQtyVar = document.getElementById("current_unit_roll").value;
                let gramasiVar = Number(document.getElementById("gramasi_marker").value);
                let unitPActualVar = document.getElementById("unit_p_act").value;
                let lActualVar = Number(document.getElementById("l_act").value);
                let commaActualVar = Number(document.getElementById("comma_act").value);
                let sisaGelaranVar = Number(document.getElementById("current_sisa_gelaran").value);
                let sisaTidakBisaVar = Number(document.getElementById("current_sisa_tidak_bisa").value);

                let pActualConverted = 0;

                if (document.getElementById("sambungan_status").value == "extension") {
                    pActualConverted = document.getElementById("current_sambungan").value;
                } else {
                    if (unitQtyVar != "KGM") {
                        pActualConverted = pActualCommaActual(pActualVar, unitPActualVar, commaActualVar);
                    } else {
                        qtyVar = Number(document.getElementById("current_qty_roll").value);

                        pActualConverted = pActualConversion(pActualVar, unitPActualVar, commaActualVar, lActualVar, gramasiVar, unitQtyVar);
                    }
                }

                // let shortRoll = pActualConverted * lembarGelaranVar + kepalaKainVar + pipingVar + sisaKainVar + rejectVar + sambunganVar - qtyVar;
                let shortRoll = ((pActualConverted * lembarGelaranVar) + sisaGelaranVar + sambunganVar + kepalaKainVar + sisaTidakBisaVar + rejectVar + sisaKainVar + pipingVar) - qtyVar;

                if (sambunganVar != 0) {
                    shortRoll = 0;
                }

                document.getElementById("current_short_roll").value = isNaN(shortRoll.round(2)) ? 0 : shortRoll.round(2);
            }

            // -Calculate Remark-
            function calculateRemark() {
                let lembarGelaranVar = Number(document.getElementById("current_lembar_gelaran").value);
                let pActualVar = Number(document.getElementById("p_act").value);
                let kepalaKainVar = Number(document.getElementById("current_kepala_kain").value);
                let pipingVar = Number(document.getElementById("current_piping").value);
                let sisaKainVar = Number(document.getElementById("current_sisa_kain").value);
                let rejectVar = Number(document.getElementById("current_reject").value);
                let sambunganVar = Number(document.getElementById("current_sambungan").value);
                let qtyVar = Number(document.getElementById("current_qty").value);
                let unitQtyVar = document.getElementById("current_unit_roll").value;
                let gramasiVar = Number(document.getElementById("gramasi_marker").value);
                let unitPActualVar = document.getElementById("unit_p_act").value;
                let lActualVar = Number(document.getElementById("l_act").value);
                let commaActualVar = Number(document.getElementById("comma_act").value);
                let sisaGelaranVar = Number(document.getElementById("current_sisa_gelaran").value);
                let sisaTidakBisaVar = Number(document.getElementById("current_sisa_tidak_bisa").value);

                let pActualConverted = 0;

                if (document.getElementById("sambungan_status").value == "extension") {
                    pActualConverted = document.getElementById("current_sambungan").value;
                } else {
                    if (unitQtyVar != "KGM") {
                        pActualConverted = pActualCommaActual(pActualVar, unitPActualVar, commaActualVar);
                    } else {
                        qtyVar = Number(document.getElementById("current_qty_roll").value);

                        pActualConverted = pActualConversion(pActualVar, unitPActualVar, commaActualVar, lActualVar, gramasiVar, unitQtyVar);
                    }
                }

                let remark = ((pActualConverted * lembarGelaranVar) + sisaGelaranVar + sambunganVar + kepalaKainVar + sisaTidakBisaVar + rejectVar + sisaKainVar + pipingVar);

                document.getElementById("current_remark").value = remark.round(2);
            }

            // -Calculate Sisa Kain-
            function calculateSisaKain() {
                let lembarGelaranVar = Number(document.getElementById("current_lembar_gelaran").value);
                let kepalaKainVar = Number(document.getElementById("current_kepala_kain").value);
                let rejectVar = Number(document.getElementById("current_reject").value);
                let pipingVar = Number(document.getElementById("current_piping").value);
                let sisaTidakBisaVar = Number(document.getElementById("current_sisa_tidak_bisa").value);

                let pActualVar = Number(document.getElementById("p_act").value);
                let lActualVar = Number(document.getElementById("l_act").value);
                let unitPActualVar = document.getElementById("unit_p_act").value;
                let commaActualVar = Number(document.getElementById("comma_act").value);
                let gramasiVar = Number(document.getElementById("gramasi_marker").value);

                let qtyVar = Number(document.getElementById("current_qty").value);
                let unitQtyVar = document.getElementById("current_unit_roll").value;

                let pActualConverted = 0;

                if (document.getElementById("sambungan_status").value == "extension") {
                    pActualConverted = document.getElementById("current_sambungan").value;
                } else {
                    if (unitQtyVar != "KGM") {
                        pActualConverted = pActualCommaActual(pActualVar, unitPActualVar, commaActualVar);
                    } else {
                        qtyVar = Number(document.getElementById("current_qty_roll").value);

                        pActualConverted = pActualConversion(pActualVar, unitPActualVar, commaActualVar, lActualVar, gramasiVar, unitQtyVar);
                    }
                }

                let sisaKain = qtyVar - ((pActualConverted * lembarGelaranVar) + kepalaKainVar + sisaTidakBisaVar + rejectVar + rejectVar + pipingVar);

                document.getElementById("current_sisa_kain").value = sisaKain.round(2);
            }

            // -Calculate Sambungan-
            function calculateSambungan(sisaGelaran, unitSisaGelaran) {
                let sisaGelaranVar = sisaGelaran > 0 ? Number(sisaGelaran) : Number(document.getElementById("current_sisa_gelaran").value);
                let unitSisaGelaranVar = unitSisaGelaran ? unitSisaGelaran : document.getElementById("current_sisa_gelaran_unit").value;
                let qtyVar = Number(document.getElementById("current_qty").value);
                let unitQtyVar = document.getElementById("current_unit_roll").value;
                let pActualVar = Number(document.getElementById('p_act').value);
                let unitPActualVar = document.getElementById('unit_p_act').value;
                let commaActualVar = Number(document.getElementById('comma_act').value);
                let lActualVar = Number(document.getElementById('l_act').value);
                let gramasiVar = Number(document.getElementById('gramasi_marker').value);

                let pActualConverted = 0;
                let sisaGelaranConverted = 0;

                // Convert P Actual
                if (unitQtyVar != "KGM") {
                    pActualConverted = pActualCommaActual(pActualVar, unitPActualVar, commaActualVar);
                } else {
                    pActualConverted = pActualConversion(pActualVar, unitPActualVar, commaActualVar, lActualVar, gramasiVar, unitQtyVar);
                }

                // Convert Sisa Gelaran
                if (unitSisaGelaranVar == unitQtyVar) {
                    sisaGelaranConverted = sisaGelaranVar;
                } else {
                    if (unitQtyVar == "YARD" || unitQtyVar == "YRD") {
                        unitQtyVar = "METER";
                    }

                    sisaGelaranConverted = conversion(sisaGelaranVar, unitQtyVar, unitSisaGelaranVar);
                }

                let estSambungan = pActualConverted - sisaGelaranConverted;

                return estSambungan.round(2);
            }

            // -Calculate Cons. Actual 1 Gelaran-
            // function calculateConsActualGelaran(unit = 0, piping = 0, lembar = 0, totalQtyFabric = 0, totalQtyCut = 0, totalPemakaian) {
            //     let unitVar = unit;
            //     let pipingVar = Number(piping);
            //     let lembarVar = Number(lembar);
            //     let totalQtyFabricVar = Number(totalQtyFabric);
            //     let totalQtyCutVar = Number(totalQtyCut);
            //     let totalPemakaianVar = Number(totalPemakaian);
            //     let pActualVar = Number(document.getElementById('p_act').value);
            //     let unitPActualVar = document.getElementById("unit_p_act").value;
            //     let lActualVar = Number(document.getElementById("l_act").value);
            //     let commaActualVar = Number(document.getElementById("comma_act").value);
            //     let gramasiVar = Number(document.getElementById("gramasi_marker").value);

            //     if (isNotNull(unitVar) && isNotNull(pipingVar) && isNotNull(lembarVar) && isNotNull(pActualVar) && isNotNull(totalQtyCutVar)) {
            //         let consActualGelaran = 0;

            //         let commaMeter = commaActualVar / 100;

            //         let pActualConverted = 0;

            //         if (document.getElementById("sambungan_status").value == "extension") {
            //             pActualConverted = document.getElementById("current_sambungan").value;
            //         } else {
            //             if (unitVar != "KGM") {
            //                 pActualConverted = pActualCommaActual(pActualVar, unitPActualVar,
            //                     commaActualVar);
            //             } else {
            //                 pActualConverted = pActualConversion(pActualVar, unitPActualVar, commaActualVar,
            //                     lActualVar,
            //                     gramasiVar, unitVar);
            //             }
            //         }

            //         // consActualGelaran = totalQtyCutVar > 0 ? (lembarVar * pActualConverted) / totalQtyCutVar : 0;
            //         consActualGelaran = totalPemakaianVar / lembarVar;

            //         document.getElementById("cons_actual_gelaran").value = consActualGelaran.round(2);
            //         document.getElementById("unit_cons_actual_gelaran").value = unitVar.toLowerCase();
            //         // document.getElementById("unit_cons_ampar").value = unitVar.toUpperCase();

            //         calculateConsAmpar();
            //     }
            // }

            function calculateConsActualGelaran(unit, totalQtyFabric, totalKepalaKain, totalSisaTidakBisa, totalReject, totalSisaKain, totalPiping, totalShortRoll) {
                let unitVar = unit;
                let totalQtyFabricVar = totalQtyFabric ? Number(totalQtyFabric) : 0;
                let totalKepalaKainVar = totalKepalaKain ? Number(totalKepalaKain) : 0;
                let totalSisaTidakBisaVar = totalSisaTidakBisa ? Number(totalSisaTidakBisa) : 0;
                let totalRejectVar = totalReject ? Number(totalReject) : 0;
                let totalSisaKainVar = totalSisaKain ? Number(totalSisaKain) : 0;
                let totalPipingVar = totalPiping ? Number(totalPiping) : 0;
                let totalShortRollVar = totalShortRoll ? Number(totalShortRoll) : 0;

                let consActualGelaran = (totalQtyFabricVar - totalKepalaKainVar - totalSisaTidakBisaVar - totalRejectVar - totalSisaKainVar - totalPipingVar)/totalQtyCut;
                let consActualGelaranShortRolless = (totalQtyFabricVar - totalKepalaKainVar - totalSisaTidakBisaVar - totalRejectVar - totalSisaKainVar - totalPipingVar + totalShortRollVar)/totalQtyCut;

                document.getElementById('cons_actual_gelaran').value = Number(consActualGelaran).round(3);
                document.getElementById('cons_actual_gelaran_short_rolless').value = Number(consActualGelaranShortRolless).round(3);

                document.getElementById("unit_cons_actual_gelaran").value = unitVar.toLowerCase();
                document.getElementById("unit_cons_actual_gelaran_short_rolless").value = unitVar.toLowerCase();

                calculateConsAmpar();

                consUpRate();
            }

            function consUpRate() {
                let consActualGelaran = document.getElementById('cons_actual_gelaran').value;
                let unitConsActualGelaran = document.getElementById('unit_cons_actual_gelaran').value;

                let consActualGelaranShortRolless = document.getElementById('cons_actual_gelaran_short_rolless').value;
                let unitConsActualGelaranShortRolless = document.getElementById('unit_cons_actual_gelaran_short_rolless').value;

                let consWs = document.getElementById('cons_ws').value;
                let consMarker = document.getElementById('cons_marker').value;

                let consWsUpRate = 0;
                let consMarkerUpRate = 0;
                let consWsUpRateNoSr = 0;
                let consMarkerUpRateNoSr = 0;

                if (unitConsActualGelaran != "METER" && unitConsActualGelaranShortRolless != "METER") {
                    let consActualGelaranConverted = conversion(consActualGelaran, "METER", unitConsActualGelaran.toUpperCase());
                    let consActualGelaranShortRollessConverted = conversion(consActualGelaranShortRolless, "METER", unitConsActualGelaranShortRolless.toUpperCase());

                    consWsUpRate = (consActualGelaranConverted - consWs)/consWs * 100;
                    consMarkerUpRate = ((consActualGelaranConverted - consMarker)/consMarker) * 100;

                    consWsUpRateNoSr = ((consActualGelaranShortRollessConverted - consWs)/consWs) * 100;
                    consMarkerUpRateNoSr = ((consActualGelaranShortRollessConverted - consMarker)/consMarker) * 100;
                } else {
                    consWsUpRate = ((consActualGelaran - consWs)/consWs) * 100;
                    consMarkerUpRate = ((consActualGelaran - consMarker)/consMarker) * 100;

                    consWsUpRateNoSr = ((consActualGelaranShortRolless - consWs)/consWs) * 100;
                    consMarkerUpRateNoSr = ((consActualGelaranShortRolless - consMarker)/consMarker) * 100;
                }

                document.getElementById('cons_ws_uprate').value = Number(consWsUpRate).round(2);
                document.getElementById('cons_marker_uprate').value = Number(consMarkerUpRate).round(2);
                document.getElementById('cons_ws_uprate_nosr').value = Number(consWsUpRateNoSr).round(2);
                document.getElementById('cons_marker_uprate_nosr').value = Number(consMarkerUpRateNoSr).round(2);
            }

            // -Get Cons. WS Data-
            function getNumberData() {
                return $.ajax({
                    url: '{{ route('get-general-number') }}',
                    type: 'get',
                    data: {
                        act_costing_id: $("#act_costing_id").val(),
                        color: $("#color").val(),
                        panel: $("#panel").val(),
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res) {
                            let consWs = res.cons_ws;

                            document.getElementById("cons_ws").value = consWs;
                        }
                    }
                });
            }

            // -Check Form Cut Input Status-
            async function checkStatus() {
                $('#loss-time-card').CardWidget('collapse');

                checkLostTime(noForm);

                if (statusForm == "form") {
                    startProcessButton.classList.add("d-none");
                    nextProcessOneButton.classList.remove("d-none");

                    document.getElementById("loss-time-card").classList.remove("d-none");
                }

                if (statusForm == "form detail") {
                    document.getElementById("loss-time-card").classList.remove("d-none");

                    startProcessButton.classList.add("d-none");
                    nextProcessOneButton.classList.add("d-none");

                    $('#header-data-card').CardWidget('collapse');
                    $('#detail-data-card').removeClass('d-none');
                    nextProcessTwoButton.classList.remove("d-none");
                }

                if (statusForm == "form spreading") {
                    document.getElementById("loss-time-card").classList.remove("d-none");

                    if ($("sambungan_status").val() != "extension") {
                        document.getElementById("current_sambungan").setAttribute('readonly', true);
                        document.getElementById("current_sisa_gelaran").removeAttribute('readonly');
                    }

                    startProcessButton.classList.add("d-none");
                    nextProcessOneButton.classList.add("d-none");
                    nextProcessTwoButton.classList.add("d-none");
                    nextProcessThreeButton.classList.remove("d-none");

                    $('#header-data-card').CardWidget('collapse');
                    $('#detail-data-card').removeClass('d-none');
                    $('#detail-data-card').CardWidget('collapse');
                    $('#scan-qr-card').removeClass('d-none');

                    initScan();
                    getItemList();

                    checkSpreadingForm();

                    await getSummary()
                    if (summaryData != null && summaryData.length > 0) {
                        $('#spreading-form-card').removeClass("d-none");
                        $('#summary-card').removeClass("d-none");

                        finishProcessButton.classList.remove("d-none");
                    }
                }

                if (statusForm == "finish") {
                    document.getElementById("loss-time-card").classList.remove("d-none");

                    startProcessButton.classList.add("d-none");
                    nextProcessOneButton.classList.add("d-none");
                    nextProcessTwoButton.classList.add("d-none");
                    nextProcessThreeButton.classList.add("d-none");

                    $('#header-data-card').CardWidget('collapse');
                    $('#detail-data-card').removeClass('d-none');
                    $('#detail-data-card').CardWidget('collapse');
                    $('#scan-qr-card').removeClass('d-none');
                    $('#scan-qr-card').CardWidget('collapse');

                    nextProcessThreeButton.setAttribute("disabled", true);

                    await getSummary();
                    if (summaryData != null && summaryData.length > 0) {
                        $('#spreading-form-card').CardWidget("collapse");
                        $('#spreading-form-card').removeClass("d-none");
                        $('#summary-card').removeClass("d-none");

                        finishProcessButton.classList.remove("d-none");
                    }

                    lockFormCutInput();
                }
            }

            // -Clear General Form Value-
            function clearGeneralForm() {
                if (startTime.value == "" || startTime.value == null) {
                    startTime.value = "";
                }

                if (finishTime.value == "" || finishTime.value == null) {
                    finishTime.value = "";
                }
            }

            // -Lock General Form-
            function lockGeneralForm() {
                document.getElementById('shell').setAttribute('disabled', true);
                document.getElementById('p_act').setAttribute('readonly', true);
                document.getElementById('comma_act').setAttribute('readonly', true);
                document.getElementById('l_act').setAttribute('readonly', true);
                document.getElementById('cons_ws').setAttribute('readonly', true);
                document.getElementById('cons_act').setAttribute('readonly', true);
                document.getElementById('cons_piping').setAttribute('readonly', true);
                document.getElementById('cons_ampar').setAttribute('readonly', true);
                document.getElementById('est_piping').setAttribute('readonly', true);
                document.getElementById('est_kain').setAttribute('readonly', true);
                document.getElementById('operator').setAttribute('readonly', true);
                document.getElementById('unit_cons_actual_gelaran').setAttribute('readonly', true);
            }

            // -Switch Method-
            function switchMethod(element) {
                if (element.checked) {
                    toScanMethod();
                } else {
                    toItemMethod();
                }
            }

            function toItemMethod() {
                method = "item";

                document.getElementById("scan-method").classList.add('d-none');
                document.getElementById("to-scan").classList.add('d-none');

                document.getElementById("item-method").classList.remove('d-none');
                document.getElementById("to-item").classList.remove('d-none');
                $("#select_item").val("").trigger("change");

                clearQrCodeScanner();

                removeColorSpreading();

                location.href = "#scan-qr-card";
            }

            function toScanMethod() {
                method = "scan";

                document.getElementById("item-method").classList.add('d-none');
                document.getElementById("to-item").classList.add('d-none');

                document.getElementById("scan-method").classList.remove('d-none');
                document.getElementById("to-scan").classList.remove('d-none');
                $("#select_item").val("").trigger("change");

                initScan();

                addColorSpreading();

                location.href = "#scan-qr-card";
            }

            function addColorSpreading() {
                document.getElementById("current_item_id_label").classList.add("label-scan");
                document.getElementById("current_item_id").classList.add("border-scan");
                document.getElementById("current_lot_label").classList.add("label-scan");
                document.getElementById("current_lot").classList.add("border-scan");
                document.getElementById("current_roll_label").classList.add("label-scan");
                document.getElementById("current_roll").classList.add("border-scan");
                document.getElementById("current_qty_roll_label").classList.add("label-scan");
                document.getElementById("current_qty_roll").classList.add("border-scan");
                document.getElementById("current_unit_roll").classList.add("border-scan");
                document.getElementById("current_qty_label").classList.add("label-calc");
                document.getElementById("current_qty").classList.add("border-calc");
                document.getElementById("current_unit").classList.add("border-calc");
            }

            function removeColorSpreading() {
                document.getElementById("current_item_id_label").classList.remove("label-scan");
                document.getElementById("current_item_id").classList.remove("border-scan");
                document.getElementById("current_lot_label").classList.remove("label-scan");
                document.getElementById("current_lot").classList.remove("border-scan");
                document.getElementById("current_roll_label").classList.remove("label-scan");
                document.getElementById("current_roll").classList.remove("border-scan");
                document.getElementById("current_qty_roll_label").classList.remove("label-scan");
                document.getElementById("current_qty_roll").classList.remove("border-scan");
                document.getElementById("current_unit_roll").classList.remove("border-scan");
                document.getElementById("current_qty_label").classList.remove("label-calc");
                document.getElementById("current_qty").classList.remove("border-calc");
                document.getElementById("current_unit").classList.remove("border-calc");
            }

            // Get Item List Module :
            async function getItemList() {
                $("#select_item").prop("disabled", true);

                await $.ajax({
                    url: '{{ route('get-item-form-cut-input') }}',
                    type: 'get',
                    data: {
                        act_costing_id: $("#act_costing_id").val(),
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res) {
                            res.forEach((item) => {
                                let option = document.createElement("option");
                                option.text = item.itemdesc;
                                option.value = item.item_id;

                                document.getElementById("select_item").appendChild(option);
                            })
                        }
                    },
                });

                $("#select_item").prop("disabled", false);
            }

            function setSelectedItem(element) {
                currentScannedItem = null;

                if (element.value && element.value != "") {
                    document.getElementById("roll_id").value = "";
                    document.getElementById("current_roll_id").value = "";

                    document.getElementById("item_id").value = element.value;
                    document.getElementById("item_detail").value = $("#select_item option:selected").text();

                    currentScannedItem = {"item_id": element.value, "item_detail": $("#select_item option:selected").text(), "unit": "METER"};
                }
            }

            function setCustomUnit(unit) {
                document.getElementById("current_unit_roll").value = unit;

                let inputGroupUnit = document.getElementsByClassName("input-group-unit");
                let unitSimplified = unit != "KGM" ? "M" : "KG";

                if (unit == "KGM") {
                    document.getElementById("current_sisa_gelaran_unit").value = unit;
                    document.getElementById("current_sambungan_unit").value = unit;

                    for (var i = 0; i < inputGroupUnit.length; i++) {
                        inputGroupUnit[i].innerText = unitSimplified;
                    }
                } else {
                    document.getElementById("current_sisa_gelaran_unit").value = "METER";
                    document.getElementById("current_sambungan_unit").value = "METER";

                    for (var i = 0; i < inputGroupUnit.length; i++) {
                        inputGroupUnit[i].innerText = unitSimplified;
                    }
                }

                if (sisaGelaran > 0) {
                    let sambungan = calculateSambungan(sisaGelaran, unitSisaGelaran);

                    document.getElementById("current_sambungan").value = sambungan;
                    document.getElementById("current_total_pemakaian_roll").value = sambungan;

                    console.log(sisaGelaran, unitSisaGelaran);
                }
            }

        // Spreading Form Module :
            // Variable :
            var spreadingFormData = null;
            var sisaGelaran = null;
            var unitSisaGelaran = null;

        // Function :
            // -Check Spreading Form-
            function checkSpreadingForm() {
                let noForm = document.getElementById("no_form").value;
                let mejaId = document.getElementById("meja_id").value;

                console.log(noForm, mejaId);

                $.ajax({
                    url: '{{ route('check-spreading-form-cut-input') }}/' + noForm + '/' + mejaId,
                    type: 'get',
                    dataType: 'json',
                    success: function(res) {
                        if (res) {
                            nextProcessThreeButton.classList.remove('d-none');

                            firstTimeRecordCondition();

                            if (res.count > 0) {
                                spreadingFormData = res.data;
                                sisaGelaran = res.sisaGelaran;
                                unitSisaGelaran = res.unitSisaGelaran;
                                method = res.data.metode ? res.data.metode : "scan";

                                console.log(spreadingFormData)

                                setSpreadingForm(spreadingFormData, sisaGelaran, unitSisaGelaran);

                                checkTimeRecordLap(res.data.id);

                                document.getElementById("roll_id").value = res.data.roll_id;
                                document.getElementById("item_id").value = res.data.item_id;
                                document.getElementById("item_color").value = res.data.item_color;
                                document.getElementById("item_detail").value = res.data.item_detail;

                                $('#spreading-form-card').CardWidget('expand');
                                $('#spreading-form-card').removeClass("d-none");
                            } else {
                                $('#spreading-form-card').CardWidget('collapse');
                            }
                        }
                    }
                });
            }

            // -Set Spreading Form-
            function setSpreadingForm(data, sisaGelaran, unitSisaGelaran) {
                lockItemSpreading();

                // if not an extension
                if (!(sisaGelaran)) {
                    clearSpreadingForm();
                }

                // if the roll method is item
                if (method == "item") {
                    openItemSpreading();

                    document.getElementById("current_unit_roll").value = "METER";
                    document.getElementById("current_unit_roll_custom").value = "METER";
                }

                // spreading form data set
                let convertedQty = rollQtyConversion(data.qty, data.unit);

                data.roll_id ? document.getElementById("roll_id").value = data.roll_id : '';
                data.item_id ? document.getElementById("item_id").value = data.item_id : '';
                data.item_detail ? document.getElementById("item_detail").value = data.item_detail : '';
                data.item_color ? document.getElementById("item_color").value = data.item_color : '';
                data.roll_id ? document.getElementById("current_roll_id").value = data.roll_id : '';
                data.item_id ? document.getElementById("current_item_id").value = data.item_id : '';
                data.roll ? document.getElementById("current_roll").value = data.roll : '';
                data.group ? document.getElementById("current_group").value = data.group : '';
                data.lot ? document.getElementById("current_lot").value = data.lot : '';
                data.qty ? document.getElementById("current_qty").value = convertedQty : '';
                data.qty ? document.getElementById("current_qty_roll").value = data.qty : '';
                data.unit ? document.getElementById("current_unit_roll").value = data.unit : '';
                data.unit ? document.getElementById("current_unit_roll_custom").value = data.unit : '';
                data.unit ? document.getElementById("current_sisa_gelaran_unit").value = (data.unit != "KGM" ? "METER" : "KGM") : '';
                data.unit ? document.getElementById("current_sambungan_unit").value = (data.unit != "KGM" ? "METER" : "KGM") : '';
                data.sisa_gelaran ? document.getElementById("current_sisa_gelaran").value = data.sisa_gelaran : '';
                data.sambungan ? document.getElementById("current_sambungan").value = data.sambungan : '';
                data.est_amparan ? document.getElementById("current_est_amparan").value = data.est_amparan : '';
                data.lembar_gelaran ? document.getElementById("current_lembar_gelaran").value = data.lembar_gelaran : '';
                data.waktu_average ? document.getElementById("current_waktu_average").value = data.waktu_average : '';
                data.kepala_kain ? document.getElementById("current_kepala_kain").value = data.kepala_kain : '';
                data.sisa_tidak_bisa ? document.getElementById("current_sisa_tidak_bisa").value = data.sisa_tidak_bisa : '';
                data.reject ? document.getElementById("current_reject").value = data.reject : '';
                data.sisa_kain ? document.getElementById("current_sisa_kain").value = data.sisa_kain : '';
                data.total_pemakaian_roll ? document.getElementById("current_total_pemakaian_roll").value = data.total_pemakaian_roll : '';
                data.short_roll ? document.getElementById("current_short_roll").value = data.short_roll : '';
                data.piping ? document.getElementById("current_piping").value = data.piping : '';
                data.remark ? document.getElementById("current_remark").value = data.remark : '';

                // simplified unit name
                let unitSimplified = data.unit != "KGM" ? "M" : "KG";

                let inputGroupUnit = document.getElementsByClassName("input-group-unit");

                for (var i = 0; i < inputGroupUnit.length; i++) {
                    inputGroupUnit[i].innerText = unitSimplified;
                }

                // updating est ampar & ply progress bar
                calculateEstAmpar();

                updatePlyProgress();

                // if is an extension
                if (sisaGelaran > 0) {
                    // extension things
                    let estSambungan = calculateSambungan(sisaGelaran, unitSisaGelaran);

                    data.sambungan_id ? document.getElementById("sambungan_id").value = data.sambungan_id : '';
                    document.getElementById("sambungan_status").value = "extension";
                    document.getElementById("current_sambungan").value = estSambungan;

                    openExtension();

                    // set total pemakaian
                    document.getElementById("current_total_pemakaian_roll").value = document.getElementById("current_sambungan").value;

                // if not an extension
                } else {
                    nextProcessThreeButton.classList.add("d-none");

                    if ($("sambungan_status").val() != "extension") {
                        lockExtension();
                    }
                }

                // if item is not yet scanned/selected
                if (!(data.item_id)) {
                    openScanItemForm();

                    // scan qr
                    $('#scan-qr-card').CardWidget('expand');
                    $('#spreading-form-card').CardWidget('collapse');
                    document.getElementById('roll_id').focus();

                    // time record
                    firstTimeRecordCondition();

                // if item scanned/selected
                } else {
                    lockScanItemForm();

                    // scan qr
                    $('#scan-qr-card').CardWidget('collapse');
                    $('#spreading-form-card').CardWidget('expand');

                    // time record
                    openTimeRecordCondition();
                }
            }

            // -Clear Spreading Form-
            function clearSpreadingForm() {
                $('#spreading-form-card').CardWidget('collapse');

                lockItemSpreading();

                document.getElementById("current_item_id").setAttribute("readonly", true);
                document.getElementById("current_lot").setAttribute("readonly", true);
                document.getElementById("current_roll").setAttribute("readonly", true);
                document.getElementById("current_qty").setAttribute("readonly", true);
                document.getElementById("current_qty_roll").setAttribute("readonly", true);

                document.getElementById("sambungan_id").value = "";
                document.getElementById("sambungan_status").value = "";
                document.getElementById("current_group").value = "";
                document.getElementById("current_item_id").value = "";
                document.getElementById("current_lot").value = "";
                document.getElementById("current_roll").value = "";
                document.getElementById("current_qty").value = "";
                document.getElementById("current_qty_roll").value = "";
                document.getElementById("current_unit_roll").value = "";
                document.getElementById("current_sisa_gelaran").value = 0;
                document.getElementById("current_sisa_gelaran_unit").value = "";
                document.getElementById("current_sambungan").value = 0;
                document.getElementById("current_sambungan_unit").value = "";
                document.getElementById("current_est_amparan").value = 0;
                document.getElementById("current_lembar_gelaran").value = 0;
                document.getElementById("current_waktu_average").value = "00:00";
                document.getElementById("current_kepala_kain").value = 0;
                document.getElementById("current_sisa_tidak_bisa").value = 0;
                document.getElementById("current_reject").value = 0;
                document.getElementById("current_sisa_kain").value = "";
                document.getElementById("current_total_pemakaian_roll").value = 0;
                document.getElementById("current_short_roll").value = 0;
                document.getElementById("current_piping").value = 0;
                document.getElementById("current_remark").value = 0;

                let inputGroupUnit = document.getElementsByClassName("input-group-unit");

                for (var i = 0; i < inputGroupUnit.length; i++) {
                    inputGroupUnit[i].innerText = "";
                }
            }

            // -Lock Extension input on Spreading Form-
            function lockExtension() {
                document.getElementById("current_sambungan").setAttribute('readonly', true);
                document.getElementById("current_sisa_gelaran").removeAttribute('readonly');

                document.getElementById("current_lembar_gelaran").removeAttribute('readonly');
                document.getElementById("current_lembar_gelaran").setAttribute('onkeyup', "calculateTotalPemakaian();calculateShortRoll();calculateRemark();openStopTimeRecord();");
                document.getElementById("current_lembar_gelaran").setAttribute('onchange', "calculateTotalPemakaian();calculateShortRoll();calculateRemark();openStopTimeRecord();");
            }

            // -Open Extension input on Spreading Form-
            function openExtension() {
                document.getElementById("current_sambungan").removeAttribute('readonly');
                document.getElementById("current_sisa_gelaran").setAttribute('readonly', true);

                document.getElementById("current_lembar_gelaran").setAttribute('readonly', true);
                document.getElementById("current_lembar_gelaran").setAttribute('onkeyup', "calculateTotalPemakaian();calculateShortRoll();calculateRemark();");
                document.getElementById("current_lembar_gelaran").setAttribute('onchange', "calculateTotalPemakaian();calculateShortRoll();calculateRemark();");
            }

            // -Lock Item input on Spreading Form-
            function lockItemSpreading() {
                document.getElementById("current_item_id").setAttribute("readonly", true);
                document.getElementById("current_lot").setAttribute("readonly", true);
                document.getElementById("current_roll").setAttribute("readonly", true);
                document.getElementById("current_qty").setAttribute("readonly", true);
                document.getElementById("current_qty_roll").setAttribute("readonly", true);

                document.getElementById("current_unit_roll").classList.remove("d-none");
                document.getElementById("current_unit_roll_custom").classList.add("d-none");
            }

            // -Open Item input on Spreading Form-
            function openItemSpreading() {
                document.getElementById("current_item_id").removeAttribute("readonly");
                document.getElementById("current_lot").removeAttribute("readonly");
                document.getElementById("current_roll").removeAttribute("readonly");
                document.getElementById("current_qty").removeAttribute("readonly");
                document.getElementById("current_qty_roll").removeAttribute("readonly");

                document.getElementById("current_unit_roll").classList.add("d-none");
                document.getElementById("current_unit_roll_custom").classList.remove("d-none");
            }

            // -Lock Spreading Form-
            function lockSpreadingForm() {
                document.getElementById("current_group").setAttribute("readonly", true);
                document.getElementById("current_item_id").setAttribute("readonly", true);
                document.getElementById("current_lot").setAttribute("readonly", true);
                document.getElementById("current_roll").setAttribute("readonly", true);
                document.getElementById("current_qty").setAttribute("readonly", true);
                document.getElementById("current_unit_roll").setAttribute("readonly", true);
                document.getElementById("current_sisa_gelaran").setAttribute("readonly", true);
                document.getElementById("current_sambungan").setAttribute("readonly", true);
                document.getElementById("current_est_amparan").setAttribute("readonly", true);
                document.getElementById("current_lembar_gelaran").setAttribute("readonly", true);
                document.getElementById("current_waktu_average").setAttribute("readonly", true);
                document.getElementById("current_kepala_kain").setAttribute("readonly", true);
                document.getElementById("current_sisa_tidak_bisa").setAttribute("readonly", true);
                document.getElementById("current_reject").setAttribute("readonly", true);
                document.getElementById("current_sisa_kain").setAttribute("readonly", true);
                document.getElementById("current_total_pemakaian_roll").setAttribute("readonly", true);
                document.getElementById("current_short_roll").setAttribute("readonly", true);
                document.getElementById("current_piping").setAttribute("readonly", true);
                document.getElementById("current_remark").setAttribute("readonly", true);
            }

            // -Get Summary Data-
            function getSummary() {
                if (summaryData == null) {
                    let noForm = document.getElementById("no_form").value;

                    return $.ajax({
                        url: '{{ route('get-time-form-cut-input') }}/' + noForm,
                        type: 'get',
                        dataType: 'json',
                        success: function(res) {
                            if (res) {
                                summaryData = res;
                                setSummary(summaryData);
                            }
                        }
                    });
                }
            }

            // -Set Summary Data-
            function setSummary(data) {
                if (totalScannedItem < 1) {
                    summaryData.forEach((data) => {
                        appendScannedItem(data)
                    });

                    updatePlyProgress();
                }
            }

            // -Update Ply Progress-
            function updatePlyProgress() {
                let currentLembar = Number($("#current_lembar_gelaran").val());
                let qtyPly = Number($("#qty_ply").val());

                document.getElementById("current_ply_progress_txt").innerText = (totalPly + currentLembar) + "/" + qtyPly;
                document.getElementById("current_ply_progress").style.width = Number(qtyPly) > 0 ? (Number( totalPly + currentLembar) / Number(qtyPly) * 100) + "%" : "0%";
            }

            // -Lock Form Cut Input-
            function lockFormCutInput() {
                lockProcessCondition();

                lockGeneralForm();

                lockScanItemForm();

                lockSpreadingForm();

                firstTimeRecordCondition();

                lockTimeRecord();

                firstLostTimeCondition();
            }

        // Scan QR Module :
            // Variable List :
            var html5QrcodeScanner = new Html5Qrcode("reader");
            var scannerInitialized = false;

        // Function List :
            // -Initialize Scanner-
            async function initScan() {
                if (document.getElementById("reader")) {
                    console.log(html5QrcodeScanner, html5QrcodeScanner.getState());
                    if (html5QrcodeScanner == null || (html5QrcodeScanner && (html5QrcodeScanner.isScanning == false))) {
                        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                            // handle the scanned code as you like, for example:
                            console.log(`Code matched = ${decodedText}`, decodedResult);

                            // store to input text
                            let breakDecodedText = decodedText.split('-');

                            document.getElementById('roll_id').value = breakDecodedText[0];

                            getScannedItem(breakDecodedText[0]);

                            clearQrCodeScanner();
                        };
                        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                        // If you want to prefer front camera
                        await html5QrcodeScanner.start({ facingMode: "environment" }, config, qrCodeSuccessCallback);

                        // function onScanSuccess(decodedText, decodedResult) {
                        //     // handle the scanned code as you like, for example:
                        //     console.log(`Code matched = ${decodedText}`, decodedResult);

                        //     // store to input text
                        //     let breakDecodedText = decodedText.split('-');

                        //     document.getElementById('roll_id').value = breakDecodedText[0];

                        //     getScannedItem(breakDecodedText[0]);

                        //     clearQrCodeScanner();
                        // }

                        // function onScanFailure(error) {
                        //     // handle scan failure, usually better to ignore and keep scanning.
                        //     // for example:
                        //     console.warn(`Code scan error = ${error}`);
                        // }

                        // html5QrcodeScanner = new Html5QrcodeScanner(
                        //     "reader",
                        //     {
                        //         fps: 10,
                        //         qrbox: {
                        //             width: 250,
                        //             height: 250
                        //         }
                        //     }
                        // );

                        // html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                        // html5QrCode.start({ facingMode: { exact: "environment"}}, config, onScanSuccess, onScanFailure);
                    }
                }
            }

            async function clearQrCodeScanner() {
                console.log(html5QrcodeScanner, html5QrcodeScanner.getState());
                if (html5QrcodeScanner && (html5QrcodeScanner.isScanning)) {
                    await html5QrcodeScanner.stop();
                    await html5QrcodeScanner.clear();
                }
            }

            async function refreshScan() {
                await clearQrCodeScanner();
                await initScan();
            }

            // --Clear Scan Item Form--
            function clearScanItemForm() {
                $("#roll_id").val("");
                $("#item_id").val("");
                $("#item_detail").val("");
                $("#item_color").val("");
            }

            // --Lock Scan Item Form then Clear Scanner--
            function lockScanItemForm() {
                document.getElementById("roll_id").setAttribute("readonly", true);
                document.getElementById("item_color").setAttribute("disabled", true);
                document.getElementById("get-button").setAttribute("disabled", true);
                document.getElementById("scan-button").setAttribute("disabled", true);
                document.getElementById("switch-method").setAttribute("disabled", true);
                document.getElementById("reader").classList.add("d-none");

                clearQrCodeScanner();
            }

            // --Open Scan Item Form then Open Scanner--
            function openScanItemForm() {
                if (statusForm != "finish") {

                    document.getElementById("roll_id").removeAttribute("readonly");
                    document.getElementById("item_color").removeAttribute("disabled");
                    document.getElementById("get-button").removeAttribute("disabled");
                    document.getElementById("scan-button").removeAttribute("disabled");
                    document.getElementById("switch-method").removeAttribute("disabled");
                    document.getElementById("reader").classList.remove("d-none");

                    initScan();
                }
            }

        // Scanned Item Module :
            // Variable List :
            var scannedItemTable = document.getElementById("scannedItemTable");
            var scannedItemTableTbody = scannedItemTable.getElementsByTagName("tbody")[0];
            var totalRow = 0;
            var totalScannedItem = 0;
            var totalSisaGelaran = 0;
            var totalSambungan = 0;
            var totalEstAmparan = 0;
            var totalAverageTime = 0;
            var totalKepalaKain = 0;
            var totalSisaTidakBisa = 0;
            var totalReject = 0;
            var totalSisaKain = 0;
            var totalTotalPemakaian = 0;
            var totalShortRoll = 0;
            var totalRemark = 0;
            var totalPly = 0;
            var totalPiping = 0;
            var totalQtyFabric = 0;
            var latestStatus = "";
            var latestUnit = "";

        // Function List :
            // -Fetch Scanned Item Data-
            function fetchScan() {
                let kodeBarang = document.getElementById('roll_id').value;

                getScannedItem(kodeBarang);
            }

            // -Get Scanned Item Data-
            function getScannedItem(id) {
                document.getElementById("item_id").value = "";
                document.getElementById("item_detail").value = "";
                document.getElementById("item_color").value = "";

                if (isNotNull(id)) {
                    return $.ajax({
                        url: '{{ route('get-scanned-form-cut-input') }}/' + id,
                        type: 'get',
                        dataType: 'json',
                        success: function(res) {
                            if (res) {
                                if (totalScannedItem > 0) {
                                    // if (res.unit.toLowerCase() != ($("#unit_cons_actual_gelaran").val()).toLowerCase()) {
                                    //     Swal.fire({
                                    //         icon: 'error',
                                    //         title: 'Gagal',
                                    //         text: 'Unit tidak sesuai',
                                    //         showCancelButton: false,
                                    //         showConfirmButton: true,
                                    //         confirmButtonText: 'Oke',
                                    //     });
                                    // } else {
                                    //     currentScannedItem = res;

                                    //     document.getElementById("item_id").value = res.item_id;
                                    //     document.getElementById("item_detail").value = res.item_detail;
                                    // }

                                    currentScannedItem = res;

                                    document.getElementById("item_id").value = res.item_id;
                                    document.getElementById("item_detail").value = res.item_detail;
                                } else {
                                    currentScannedItem = res;

                                    document.getElementById("item_id").value = res.item_id;
                                    document.getElementById("item_detail").value = res.item_detail;
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Roll tidak tersedia atau sudah habis.',
                                    showCancelButton: false,
                                    showConfirmButton: true,
                                    confirmButtonText: 'Oke',
                                });
                            }
                        }
                    });
                }

                return Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Item tidak ditemukan',
                    showCancelButton: false,
                    showConfirmButton: true,
                    confirmButtonText: 'Oke',
                });
            }

            // -Append Scanned Item to Summary Table-
            function appendScannedItem(data) {
                totalPly += Number(data.lembar_gelaran);
                totalPiping += Number(data.piping);
                latestStatus != 'extension complete' ? totalQtyFabric += Number(data.qty) : '';
                latestUnit = data.unit;

                let tr = document.createElement('tr');
                let td1 = document.createElement('td');
                let td2 = document.createElement('td');
                let td3 = document.createElement('td');
                let td4 = document.createElement('td');
                let td5 = document.createElement('td');
                let td6 = document.createElement('td');
                let td7 = document.createElement('td');
                let td8 = document.createElement('td');
                let td9 = document.createElement('td');
                let td10 = document.createElement('td');
                let td11 = document.createElement('td');
                let td12 = document.createElement('td');
                let td13 = document.createElement('td');
                let td14 = document.createElement('td');
                let td15 = document.createElement('td');
                let td16 = document.createElement('td');
                let td17 = document.createElement('td');
                let td18 = document.createElement('td');
                let td19 = document.createElement('td');
                let td20 = document.createElement('td');
                let td21 = document.createElement('td');
                let td22 = document.createElement('td');
                td1.innerHTML = (latestStatus != 'extension complete' ? totalScannedItem + 1 : "");
                td2.innerHTML = data.group ? data.group : '-';
                td3.innerHTML = data.stocker_group ? data.stocker_group : '-';
                td4.innerHTML = data.roll_id ? data.roll_id : '-';
                td5.innerHTML = data.item_id ? data.item_id : '-';
                td6.innerHTML = data.lot ? data.lot : '-';
                td7.innerHTML = data.roll ? data.roll : '-';
                td8.innerHTML = data.qty ? data.qty : '-';
                td9.innerHTML = data.unit ? data.unit : '-';
                td10.innerHTML = data.sisa_gelaran ? data.sisa_gelaran : '-';
                td11.innerHTML = data.sambungan ? data.sambungan : '-';
                td12.innerHTML = data.est_amparan ? data.est_amparan : '-';
                td13.innerHTML = data.lembar_gelaran ? data.lembar_gelaran : '';
                td14.innerHTML = data.waktu_average ? data.waktu_average : '-';
                td15.innerHTML = data.kepala_kain ? data.kepala_kain : '-';
                td16.innerHTML = data.sisa_tidak_bisa ? data.sisa_tidak_bisa : '-';
                td17.innerHTML = data.reject ? data.reject : '-';
                td18.innerHTML = data.sisa_kain ? data.sisa_kain : '-';
                td19.innerHTML = data.total_pemakaian_roll ? data.total_pemakaian_roll : '-';
                td20.innerHTML = data.short_roll ? data.short_roll : '-';
                td21.innerHTML = data.piping ? data.piping : '-';
                td22.innerHTML = data.remark ? data.remark : '-';
                tr.appendChild(td1);
                tr.appendChild(td2);
                tr.appendChild(td3);
                tr.appendChild(td4);
                tr.appendChild(td5);
                tr.appendChild(td6);
                tr.appendChild(td7);
                tr.appendChild(td8);
                tr.appendChild(td9);
                tr.appendChild(td10);
                tr.appendChild(td11);
                tr.appendChild(td12);
                tr.appendChild(td13);
                tr.appendChild(td14);
                tr.appendChild(td15);
                tr.appendChild(td16);
                tr.appendChild(td17);
                tr.appendChild(td18);
                tr.appendChild(td19);
                tr.appendChild(td20);
                tr.appendChild(td21);
                tr.appendChild(td22);

                scannedItemTableTbody.appendChild(tr);

                totalRow++;
                latestStatus != 'extension complete' ? totalScannedItem++ : '';

                totalSisaGelaran += Number(data.sisa_gelaran);
                totalSambungan += Number(data.sambungan);
                totalEstAmparan += Number(data.est_amparan);
                totalAverageTime += (Number(data.waktu_average.slice(0, 2)) * 60) + Number(data.waktu_average.slice(3, 5));
                totalKepalaKain += Number(data.kepala_kain);
                totalSisaTidakBisa += Number(data.sisa_tidak_bisa);
                totalReject += Number(data.reject);
                totalSisaKain += Number(data.sisa_kain);
                totalTotalPemakaian += Number(data.total_pemakaian_roll);
                Number(data.short_roll) < 0 ? totalShortRoll += Number(data.short_roll) : "";
                totalRemark += Number(data.remark);

                let averageTotalAverageTime = totalAverageTime / totalRow;
                let averageTotalAverageTimeMinute = averageTotalAverageTime.round(0) >= 60 ? pad((averageTotalAverageTime.round(0) / 60).round(0)) : pad(0);
                let averageTotalAverageTimeSecond = averageTotalAverageTime.round(0) >= 60 ? pad((averageTotalAverageTime.round(0) % 60).round(0)) : pad(averageTotalAverageTime.round(0));

                document.getElementById("total-qty").innerText = Number(totalQtyFabric).round(2);
                document.getElementById("total-unit").innerText = latestUnit;
                document.getElementById("total-sisa-gelaran").innerText = Number(totalSisaGelaran).round(2);
                document.getElementById("total-sambungan").innerText = Number(totalSambungan).round(2);
                document.getElementById("total-est-amparan").innerText = Number(totalEstAmparan).round(2);
                document.getElementById("total-lembar").innerText = Number(totalPly).round(2);
                document.getElementById("total-average-time").innerText = (averageTotalAverageTimeMinute + ":" +averageTotalAverageTimeSecond);
                document.getElementById("total-kepala-kain").innerText = Number(totalKepalaKain).round(2);
                document.getElementById("total-sisa-tidak-bisa").innerText = Number(totalSisaTidakBisa).round(2);
                document.getElementById("total-reject").innerText = Number(totalReject).round(2);
                document.getElementById("total-sisa-kain").innerText = Number(totalSisaKain).round(2);
                document.getElementById("total-total-pemakaian").innerText = Number(totalTotalPemakaian).round(2);
                document.getElementById("total-short-roll").innerText = Number(totalShortRoll).round(2);
                document.getElementById("total-piping").innerText = Number(totalPiping).round(2);
                document.getElementById("total-remark").innerText = Number(totalRemark).round(2);

                calculateConsActualGelaran(latestUnit, totalQtyFabric, totalKepalaKain, totalSisaTidakBisa, totalReject, totalSisaKain, totalPiping, totalShortRoll);

                latestStatus = data.status;
            }

        // Time Record Module :
            // Variable List :
                // Button Elements
                var startLapButton = document.getElementById("startLapButton");
                var nextLapButton = document.getElementById("nextLapButton");
                var stopLapButton = document.getElementById("stopLapButton");

                // Time Elements
                var minutes = document.getElementById("minutes");
                var seconds = document.getElementById("seconds");

                // Table Elements
                var timeRecordTable = document.getElementById('timeRecordTable');
                var timeRecordTableTbody = timeRecordTable.getElementsByTagName("tbody")[0];

                // Calculate Things
                var lap = 0;
                var totalSeconds = 0;
                var summarySeconds = 0;
                var averageSeconds = 0;
                var timeRecordInterval = 0;

                // Initialize Time Elements
                seconds.value = pad(totalSeconds % 60);
                minutes.value = pad(parseInt(totalSeconds / 60));

        // Function List :
            // -Time Record-
            function checkTimeRecordLap(detailId) {
                $.ajax({
                    url: '{{ route('check-time-record-form-cut-input') }}/' + detailId,
                    type: 'get',
                    dataType: 'json',
                    success: function(res) {
                        if (res.count > 0) {
                            setTimeRecordLap(res.data);
                        }
                    }
                });
            }

            function setTimeRecordLap(data) {
                data.forEach((element, index, array) => {
                    let time = element.waktu.split(":");
                    let minutesData = Number(time[0]) * 60;
                    let secondsData = Number(time[1]);

                    summarySeconds += (minutesData + secondsData);
                    lap++;

                    if (index == (array.length - 1)) {
                        averageSeconds = (parseFloat(summarySeconds) / parseFloat(lap)).round(0);

                        $("#current_lembar_gelaran").val(lap).trigger('change');
                        $("#current_waktu_average").val((pad(parseInt(averageSeconds / 60))) + ':' + (pad(averageSeconds % 60)))
                    }

                    let tr = document.createElement('tr');
                    let td1 = document.createElement('td');
                    let td2 = document.createElement('td');
                    let td3 = document.createElement('td');
                    td1.innerHTML = lap;
                    td2.innerHTML = element.waktu;
                    td3.classList.add('d-none');
                    td3.innerHTML = `<input type='hidden' name="time_record[` + lap + `]" value="` + element.waktu + `" />`;
                    tr.appendChild(td1);
                    tr.appendChild(td2);
                    tr.appendChild(td3);

                    timeRecordTableTbody.prepend(tr);
                });

                if (data.length > 0) {
                    stopLapButton.disabled = false;
                }
            }

            // -Set Time-
            function setTime() {
                ++totalSeconds;
                seconds.value = pad(totalSeconds % 60);
                minutes.value = pad(parseInt(totalSeconds / 60));
            }

            // -Start Time Record-
            function startTimeRecord() {
                if (lostInterval) {
                    addNewLossTime();
                }

                timeRecordInterval = setInterval(setTime, 999);

                startLapButton.classList.add("d-none")
                nextLapButton.classList.remove('d-none');

                openLapTimeRecordCondition();

                if ($("#sambungan_status").val() != "extension") {
                    storeThisTimeRecord();
                }
            }

            // -Next Lap Time Record-
            async function addNewTimeRecord(data = null) {
                if ($("#sambungan_status").val() == "extension") {
                    pauseTimeRecordButtons();

                    summarySeconds += totalSeconds;
                    totalSeconds = 0;
                    lap++;

                    averageSeconds = (parseFloat(summarySeconds) / parseFloat(lap)).round(0);

                    $("#current_lembar_gelaran").val(lap).trigger('change');
                    $("#current_waktu_average").val((pad(parseInt(averageSeconds / 60))) + ':' + (pad(averageSeconds % 60)))

                    let tr = document.createElement('tr');
                    let td1 = document.createElement('td');
                    let td2 = document.createElement('td');
                    let td3 = document.createElement('td');
                    td1.innerHTML = lap;
                    td2.innerHTML = minutes.value + ':' + seconds.value;
                    td3.classList.add('d-none');
                    td3.innerHTML = `<input type='hidden' name="time_record[` + lap + `]" value="` + minutes.value + ':' +seconds.value + `" />`;
                    tr.appendChild(td1);
                    tr.appendChild(td2);
                    tr.appendChild(td3);

                    timeRecordTableTbody.prepend(tr);

                    stopLapButton.disabled = false;

                    if (!(await stopTimeRecord())) {
                        resetTimeRecord();
                    }
                } else {
                    pauseTimeRecordButtons();

                    summarySeconds += totalSeconds;
                    totalSeconds = 0;
                    lap++;

                    averageSeconds = (parseFloat(summarySeconds) / parseFloat(lap)).round(0);

                    $("#current_lembar_gelaran").val(lap).trigger('change');
                    $("#current_waktu_average").val((pad(parseInt(averageSeconds / 60))) + ':' + (pad(averageSeconds % 60)))

                    let tr = document.createElement('tr');
                    let td1 = document.createElement('td');
                    let td2 = document.createElement('td');
                    let td3 = document.createElement('td');
                    td1.innerHTML = lap;
                    td2.innerHTML = minutes.value + ':' + seconds.value;
                    td3.classList.add('d-none');
                    td3.innerHTML = `<input type='hidden' name="time_record[` + lap + `]" value="` + minutes.value + ':' +seconds.value + `" />`;
                    tr.appendChild(td1);
                    tr.appendChild(td2);
                    tr.appendChild(td3);

                    timeRecordTableTbody.prepend(tr);

                    await storeThisTimeRecord();

                    stopLapButton.disabled = false;
                }

                updatePlyProgress();
            }

            function openStopTimeRecord() {
                let lembarGelaran = document.getElementById('current_lembar_gelaran').value;

                if (lembarGelaran > 0) {
                    stopLapButton.disabled = false;
                } else {
                    stopLapButton.disabled = true;
                }
            }

            // -Stop Time Record-
            async function stopTimeRecord() {
                backToProcessThree();
            }

            // -Reset Time Record-
            function resetTimeRecord() {
                clearTimeout(timeRecordInterval);

                summarySeconds = 0;
                totalSeconds = 0;
                timeRecordInterval = 0;

                seconds.value = 00;
                minutes.value = 00;
                lap = 0;

                startLapButton.classList.remove('d-none');
                nextLapButton.classList.add('d-none');

                timeRecordTableTbody.innerHTML = "";

                $("#switch-method").prop("checked", true).trigger("change");
            }

            function failTimeRecord() {
                clearTimeout(timeRecordInterval);

                summarySeconds = 0;
                totalSeconds = 0;
                timeRecordInterval = 0;

                seconds.value = 00;
                minutes.value = 00;
                lap = 0;

                timeRecordTableTbody.innerHTML = "";

                $("#switch-method").prop("checked", true).trigger("change");
            }

            // Pause Buttons
            function pauseTimeRecordButtons() {
                startLapButton.disabled = true;
                nextLapButton.disabled = true;
                setTimeout(function() {
                    startLapButton.disabled = false;
                    nextLapButton.disabled = false;

                    nextLapButton.focus();
                }, 1500);
            }

            // Conditions :
            function firstTimeRecordCondition() {
                startLapButton.disabled = true;
                nextLapButton.disabled = true;
                stopLapButton.disabled = true;
                finishProcessButton.disabled = false;

                finishProcessButton.classList.remove("d-none");

                openScanItemForm();
            }

            function openTimeRecordCondition() {
                startLapButton.disabled = false;
                nextLapButton.disabled = true;
                stopLapButton.disabled = true;

                lockScanItemForm();
            }

            function openLapTimeRecordCondition() {
                startLapButton.disabled = true;
                nextLapButton.disabled = false;

                pauseTimeRecordButtons();
            }

            function nextTimeRecordCondition() {
                startLapButton.disabled = true;
                nextLapButton.disabled = true;
                stopLapButton.disabled = true;
                finishProcessButton.disabled = false;
            }

            function lockTimeRecord() {
                finishProcessButton.disabled = true;
                finishProcessButton.innerHTML = "PENGERJAAN TELAH DISELESAIKAN";
            }

            // -Disable Time Record-
            function disableTimeRecord() {
                startLapButton.disabled = true;
                nextLapButton.disabled = true;

                clearTimeout(timeRecordInterval);
            }

            // -Enable Time Record-
            function enableTimeRecord() {
                startLapButton.disabled = false;
                nextLapButton.disabled = false;

                timeRecordInterval = setInterval(setTime, 999);
            }

        // Lost Time Module :
            // Variable List :
                // Button Elements
                var startLossButton = document.getElementById("startLossButton");
                var nextLossButton = document.getElementById("nextLossButton");

                // Time Elements
                var lostMinutes = document.getElementById("lostMinutes");
                var lostSeconds = document.getElementById("lostSeconds");

                // Table Elements
                var lossTimeTable = document.getElementById('lossTimeTable');
                var lossTimeTableTbody = lossTimeTable.getElementsByTagName("tbody")[0];

                // Calculate Things
                var lostTime = 0;
                var totalLostSeconds = 0;
                var summaryLostSeconds = 0;
                var averageLostSeconds = 0;
                var lostInterval = 0;

                // Status
                var pausedTimeRecord = false;

                // Initialize Time Elements
                lostSeconds.value = pad(totalLostSeconds % 60);
                lostMinutes.value = pad(parseInt(totalLostSeconds / 60));

            // Function List :
                // -Time Record-
                function checkLostTime(noForm) {
                    $.ajax({
                        url: '{{ route('check-lost-form-cut-input') }}/' + noForm,
                        type: 'get',
                        dataType: 'json',
                        success: function(res) {
                            openLostTimeCondition();

                            if (res.count > 0) {
                                setLostTime(res.data);
                            }
                        }
                    });
                }

                async function setLostTime(data) {
                    await data.forEach((element, index, array) => {
                        let time = element.waktu.split(":");
                        let minutesData = Number(time[0]) * 60;
                        let secondsData = Number(time[1]);

                        lostTime++;

                        let tr = document.createElement('tr');
                        let td1 = document.createElement('td');
                        let td2 = document.createElement('td');
                        let td3 = document.createElement('td');
                        td1.innerHTML = lostTime;
                        td2.innerHTML = element.waktu;
                        td3.classList.add('d-none');
                        td3.innerHTML = `<input type='hidden' name="lost_time[` + lostTime + `]" value="` + element.waktu + `" />`;
                        tr.appendChild(td1);
                        tr.appendChild(td2);
                        tr.appendChild(td3);

                        lossTimeTableTbody.prepend(tr);
                    });

                    document.getElementById("current_loss_time").value = lostTime;
                }

                // -Set Time-
                function setTimeLost() {
                    ++totalLostSeconds;
                    lostSeconds.value = pad(totalLostSeconds % 60);
                    lostMinutes.value = pad(parseInt(totalLostSeconds / 60));
                }

                // -Start Time Record-
                function startLossTime() {
                    pausedTimeRecord = false;

                    if (timeRecordInterval) {
                        disableTimeRecord();

                        pausedTimeRecord = true;
                    }

                    pauseLostTimeButtons();

                    lostInterval = setInterval(setTimeLost, 999);

                    startLossButton.classList.add("d-none")
                    nextLossButton.classList.remove('d-none');
                    nextLossButton.focus();

                    openLostTimeCondition();
                }

                // -Next Lap Time Record-
                async function addNewLossTime() {
                    pauseLostTimeButtons();

                    totalLostSeconds = 0;
                    lostTime++;

                    document.getElementById("current_loss_time").value = lostTime;

                    let tr = document.createElement('tr');
                    let td1 = document.createElement('td');
                    let td2 = document.createElement('td');
                    let td3 = document.createElement('td');
                    td1.innerHTML = lostTime;
                    td2.innerHTML = lostMinutes.value + ':' + lostSeconds.value;
                    td3.classList.add('d-none');
                    td3.innerHTML = `<input type='hidden' name="lost_time[` + lostTime + `]" value="` +
                        lostMinutes.value +
                        ':' + lostSeconds.value + `" />`;
                    tr.appendChild(td1);
                    tr.appendChild(td2);
                    tr.appendChild(td3);

                    lossTimeTableTbody.prepend(tr);

                    console.log(lostTime);

                    stopLostTime();

                    $('#loss-time-card').CardWidget('collapse');
                }

                // -Stop Time Record-
                async function stopLostTime() {
                    if (pausedTimeRecord) {
                        enableTimeRecord();
                    }

                    pauseLostTimeButtons();

                    clearTimeout(lostInterval);

                    totalLostSeconds = 0;
                    lostInterval = 0;

                    lostSeconds.value = "00";
                    lostMinutes.value = "00";

                    startLossButton.classList.remove('d-none');
                    nextLossButton.classList.add('d-none');

                    storeLostTime(id);
                }

                // Pause Buttons
                function pauseLostTimeButtons(disables = []) {
                    startLossButton.disabled = true;
                    nextLossButton.disabled = true;
                    setTimeout(function() {
                        startLossButton.disabled = false;
                        nextLossButton.disabled = false;
                    }, 1500);
                }

                // Store Lost Time
                function storeLostTime(id) {
                    let lostTimeForm = new FormData(document.getElementById('loss-time-form'));

                    $.ajax({
                        url: '{{ route('store-lost-form-cut-input') }}/' + id,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        data: lostTimeForm,
                        success: function(res) {
                            if (res) {
                                console.log(res);
                            }
                        }
                    });
                }

                // Conditions :
                function firstLostTimeCondition() {
                    startLossButton.disabled = true;
                    nextLossButton.disabled = true;
                }

                function openLostTimeCondition() {
                    startLossButton.disabled = false;
                    nextLossButton.disabled = true;
                }

                function openLapLostTimeCondition() {
                    startLossButton.disabled = true;
                    nextLossButton.disabled = false;
                }

                function nextLostTimeCondition() {
                    startLossButton.disabled = true;
                    nextLossButton.disabled = true;
                }
    </script>
@endsection
