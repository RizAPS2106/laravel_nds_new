@extends('layouts.index')

@section('content')
    <form action="{{ route('store-part') }}" method="post" id="store-part" onsubmit="submitForm(this, event)">
        @csrf
        <div class="card card-sb">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold">
                        <i class="fas fa-th fa-sm"></i> Tambah Data Part
                    </h5>
                    <a href="{{ route('part') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-reply"></i> Kembali ke Part
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 col-md-3">
                        <div class="mb-1">
                            <div class="form-group">
                                <label><small>No. WS</small></label>
                                <select class="form-control select2bs4" id="act_costing_id" name="act_costing_id">
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
                            <label class="form-label"><small>Buyer</small></label>
                            <input type="text" class="form-control" id="buyer" name="buyer" readonly>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mb-1">
                            <label class="form-label"><small>Style</small></label>
                            <input type="text" class="form-control" id="style" name="style" readonly>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mb-1">
                            <div class="form-group">
                                <label class="form-label"><small>Panel</small></label>
                                <select class="form-control select2bs4" id="panel" name="panel" style="width: 100%;" >
                                    <option selected="selected" value="">Pilih Panel</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <input type="hidden" class="form-control" id="act_costing_ws" name="act_costing_ws" readonly>
                    <div class="col-12 col-md-12">
                        <div class="mb-1">
                            <div class="form-group">
                                <label class="form-label"><small>Color</small></label>
                                <input type="text" class="form-control" name="color" id="color" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12" id="parts-section">
                        <div class="row">
                            <div class="col-3">
                                <label class="form-label"><small>Part</small></label>
                                <select class="form-control select2bs4" name="part[0]" id="part_0">
                                    <option value="">Pilih Part</option>
                                    @foreach ($masterParts as $part)
                                        <option value="{{ $part->kode }}" data-index="0">{{ $part->nama_part }} - {{ $part->bagian }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <label class="form-label"><small>Cons</small></label>
                                <div class="d-flex mb-3">
                                    <div style="width: 50%;">
                                        <input type="number" class="form-control" style="border-radius: 3px 0 0 3px;" name="cons[0]" id="cons_0" step="0.001">
                                    </div>
                                    <div style="width: 50%;">
                                        <select class="form-select" style="border-radius: 0 3px 3px 0;" name="unit_cons[0]" id="unit_cons_0">
                                            <option value="meter">METER</option>
                                            <option value="yard">YARD</option>
                                            <option value="kgm">KGM</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <label class="form-label"><small>Tujuan</small></label>
                                <select class="form-control select2bs4" style="border-radius: 0 3px 3px 0;" name="tujuan[0]" id="tujuan_0">
                                    <option value="">Pilih Tujuan</option>
                                    @foreach ($masterTujuan as $tujuan)
                                        <option value="{{ $tujuan->kode }}">{{ $tujuan->tujuan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <label class="form-label"><small>Proses</small></label>
                                <select class="form-control select2bs4" style="border-radius: 0 3px 3px 0;" name="proses[0]" id="proses_0" data-index="0" onchange="changeTujuan(this)">
                                    <option value="">Pilih Proses</option>
                                    @foreach ($masterSecondary as $secondary)
                                        <option value="{{ $secondary->kode }}" data-tujuan="{{ $secondary->tujuan_kode }}">{{ $secondary->proses }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-sm btn-sb-secondary my-3" onclick="addNewPart()">
                            <i class="far fa-plus-square"></i> Tambah Part
                        </button>
                    </div>
                    <input type="hidden" class="form-control" id="jumlah_part_detail" name="jumlah_part_detail" value="1" readonly>
                </div>
                <button type="submit" class="btn btn-success btn-block fw-bold mt-3" id="submit-button"><i class="fa fa-save"></i> SIMPAN</button>
            </div>
        </div>
    </form>
@endsection

@section('custom-script')
    <script>
        // Global Variable
        var sumCutQty = null;
        var totalRatio = null;

        var partSection = null;
        var partOptions = null;
        var tujuanOptions = null;
        var prosesOptions = null;
        var selectedPartArray = [];

        var jumlahPartDetail = null;

        // Loading
        document.getElementById('loading').classList.remove("d-none");

        // Initial Window On Load Event
        $(document).ready(async function () {
            //Reset Form
            if (document.getElementById('store-part')) {
                document.getElementById('store-part').reset();

                $(".select2").val('').trigger('change');
                $(".select2bs4").val('').trigger('change');

                $("#act_costing_id").val(null).trigger("change");
                $('#part').val(null).trigger('change');

                await getMasterParts();
                await getTujuan();
                await getProses();

                partSection = document.getElementById('parts-section');

                jumlahPartDetail = document.getElementById('jumlah_part_detail');
                jumlahPartDetail.value = 1;
            }

            // Select2 Prevent Step-Jump Input ( Step = WS -> Panel )
            $("#panel").prop("disabled", true);

            // Loading End
            document.getElementById('loading').classList.add("d-none");
        });

        // Step One (WS) on change event
        $('#act_costing_id').on('change', function(e) {
            if (this.value) {
                updateOrderInfo();
                updatePanelList();
            }
        });

        // Update Order Information Based on Order WS and Order Color
        function updateOrderInfo() {
            return $.ajax({
                url: '{{ route("get-general-order") }}',
                type: 'get',
                data: {
                    act_costing_id: $('#act_costing_id').val(),
                },
                dataType: 'json',
                success: function (res) {
                    if (res) {
                        document.getElementById('act_costing_ws').value = res.kpno;
                        document.getElementById('buyer').value = res.buyer;
                        document.getElementById('style').value = res.styleno;
                        document.getElementById('color').value = res.colors;
                    }
                },
            });
        }

        // Update Panel Select Option Based on Order WS and Color WS
        function updatePanelList() {
            document.getElementById('panel').value = null;
            return $.ajax({
                url: '{{ route("get-part-panels") }}',
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
                    }
                },
            });
        }

        function getMasterParts() {
            return $.ajax({
                url: '{{ route("get-master-parts") }}',
                type: 'get',
                success: function (res) {
                    if (res) {
                        partOptions = res;
                    }
                },
                error: function (jqXHR) {
                    console.log(jqXHR);
                }
            });
        }

        function getTujuan() {
            return $.ajax({
                url: '{{ route("get-master-tujuan") }}',
                type: 'get',
                success: function (res) {
                    if (res) {
                        tujuanOptions = res;
                    }
                },
                error: function (jqXHR) {
                    console.log(jqXHR);
                }
            });
        }

        function getProses() {
            return $.ajax({
                url: '{{ route("get-master-secondaries") }}',
                type: 'get',
                success: function (res) {
                    if (res) {
                        prosesOptions = res;
                    }
                },
                error: function (jqXHR) {
                    console.log(jqXHR);
                }
            });
        }

        function changeTujuan(element) {
            let thisIndex = element.getAttribute('data-index');
            let thisSelected = element.options[element.selectedIndex];
            let thisTujuan = document.getElementById('tujuan_'+thisIndex);

            if (thisTujuan.value != thisSelected.getAttribute('data-tujuan')) {
                $('#tujuan_'+thisIndex).val(thisSelected.getAttribute('data-tujuan')).trigger("change");
            }
        }

        function addNewPart() {
            // row
            let divRow = document.createElement('div');
            divRow.setAttribute('class', 'row');

            // 1
            let divCol1 = document.createElement('div');
            divCol1.setAttribute('class', 'col-3');

            let label1 = document.createElement('label');
            label1.setAttribute('class', 'form-label');
            label1.innerHTML = '<small>Part</small>';

            let partDetail = document.createElement("select");
            partDetail.setAttribute('class', 'form-select select2bs4custom');
            partDetail.setAttribute('name', 'part['+jumlahPartDetail.value+']');
            partDetail.setAttribute('id', 'part_'+jumlahPartDetail.value);
            partDetail.innerHTML = partOptions;

            divCol1.appendChild(label1);
            divCol1.appendChild(partDetail);

            // 2
            let divCol2 = document.createElement('div');
            divCol2.setAttribute('class', 'col-3');

            divCol2.innerHTML= `
                <label class="form-label"><small>Cons</small></label>
                <div class="d-flex mb-3">
                    <div style="width: 50%;">
                        <input type="number" class="form-control" style="border-radius: 3px 0 0 3px;" name="cons[`+jumlahPartDetail.value+`]" id="cons_`+jumlahPartDetail.value+`" step="0.001">
                    </div>
                    <div style="width: 50%;">
                        <select class="form-select" style="border-radius: 0 3px 3px 0;" name="unit_cons[`+jumlahPartDetail.value+`]" id="unit_cons_`+jumlahPartDetail.value+`">
                            <option value="meter">METER</option>
                            <option value="yard">YARD</option>
                            <option value="kgm">KGM</option>
                        </select>
                    </div>
                </div>
            `;

            // 3
            let divCol3 = document.createElement('div');
            divCol3.setAttribute('class', 'col-3');

            let label3 = document.createElement('label');
            label3.setAttribute('class', 'form-label');
            label3.innerHTML = '<small>Tujuan</small>';

            let tujuan = document.createElement("select");
            tujuan.setAttribute('class', 'form-select select2bs4custom');
            tujuan.setAttribute('name', 'tujuan['+jumlahPartDetail.value+']');
            tujuan.setAttribute('id', 'tujuan_'+jumlahPartDetail.value);
            tujuan.innerHTML = tujuanOptions;

            divCol3.appendChild(label3);
            divCol3.appendChild(tujuan);

            // 4
            let divCol4 = document.createElement('div');
            divCol4.setAttribute('class', 'col-3');

            let label4 = document.createElement('label');
            label4.setAttribute('class', 'form-label');
            label4.innerHTML = '<small>Proses</small>';

            let proses = document.createElement("select");
            proses.setAttribute('class', 'form-select select2bs4custom');
            proses.setAttribute('name', 'proses['+jumlahPartDetail.value+']');
            proses.setAttribute('id', 'proses_'+jumlahPartDetail.value);
            proses.setAttribute('data-index', jumlahPartDetail.value);
            proses.setAttribute('onchange', 'changeTujuan(this)');
            proses.innerHTML = prosesOptions;

            divCol4.appendChild(label4);
            divCol4.appendChild(proses);

            // row
            divRow.appendChild(divCol1);
            divRow.appendChild(divCol2);
            divRow.appendChild(divCol3);
            divRow.appendChild(divCol4);

            partSection.appendChild(divRow);

            $('#part_'+jumlahPartDetail.value).select2({
                theme: 'bootstrap4',
            });
            $('#tujuan_'+jumlahPartDetail.value).select2({
                theme: 'bootstrap4',
            });
            $('#proses_'+jumlahPartDetail.value).select2({
                theme: 'bootstrap4',
            });

            jumlahPartDetail.value++;
        }

        // Prevent Form Submit When Pressing Enter
        document.getElementById("store-part").onkeypress = function(e) {
            var key = e.charCode || e.keyCode || 0;
            if (key == 13) {
                e.preventDefault();
            }
        }

        // Reset Step
        async function resetStep() {
            $('#part').val(null).trigger('change');
            await $("#act_costing_id").val(null).trigger("change");
            await $("#panel").val(null).trigger("change");
            await $("#panel").prop("disabled", true);
        }
    </script>
@endsection
