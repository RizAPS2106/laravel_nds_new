@extends('layouts.index')

@section('content')
    <div class="card card-sb">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold">
                    <i class="fa fa-cog fa-sm"></i> Atur Cutting Plan
                </h5>
                <a href="{{ route('cut-plan') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-reply fa-sm"></i> Kembali ke Daftar Cutting Plan
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="#" method="post">
                <div class="mb-3">
                    <label>Tanggal Plan</label>
                    <input type="date" class="form-control" name="tanggal" id="tanggal" min='{{ date('Y-m-d') }}' value="{{ date('Y-m-d') }}">
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
                                Tambah Form Cut :
                            </h5>
                        </div>
                        {{-- <div class="col-6">
                            <input type="date" class="form-control form-control-sm w-auto float-end" id="tgl_form" name="tgl_form" value="{{ date('Y-m-d') }}">
                        </div> --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row justify-content-between mb-3">
                        <div class="col-6">
                            <p>Selected Form : <span class="fw-bold" id="selected-row-count-1">0</span></p>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-success btn-sm float-end" onclick="addToCutPlan(this)">
                                <i class="fa fa-plus fa-sm"></i> Tambahkan ke Cut Plan
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable-select" class="table table-bordered table-sm w-100">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No. Form</th>
                                    <th>No. Meja</th>
                                    <th>No. Marker</th>
                                    <th>No. WS</th>
                                    <th>Style</th>
                                    <th>Color</th>
                                    <th>Panel</th>
                                    <th>Size Ratio</th>
                                    <th>Qty Ply</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mb-3">
            <div class="card card-info h-100">
                <div class="card-header">
                    <h5 class="card-title fw-bold" style="padding-bottom: 2px">
                        Form Cut Terdaftar :
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <p>Selected Form : <span class="fw-bold" id="selected-row-count-2">0</span></p>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-danger btn-sm float-end" onclick="removeCutPlan(this)"><i class="fa fa-minus fa-sm"></i> Singkirkan dari Cut Plan</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable-selected" class="table table-bordered table-sm w-100">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No. Form</th>
                                    <th>No. Meja</th>
                                    <th>No. Marker</th>
                                    <th>No. WS</th>
                                    <th>Style</th>
                                    <th>Color</th>
                                    <th>Panel</th>
                                    <th class="align-bottom">Status</th>
                                    <th>Size Ratio</th>
                                    <th>Qty Ply</th>
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
    <!-- Page specific script -->
    <script>
        //Reset Form
        if (document.getElementById('store-cut-plan')) {
            document.getElementById('store-cut-plan').reset();
        }

        document.getElementById("tanggal").addEventListener("change", function() {
            let todayDate = new Date();
            let selectedDate = new Date(this.value);

            if (selectedDate < todayDate) {
                $("#tanggal").val(formatDate(todayDate));
            }

            datatableSelect.ajax.reload(() => {
                $('#datatable-select').DataTable().ajax.reload(() => {
                    document.getElementById('selected-row-count-1').innerText = $('#datatable-select').DataTable().rows('.selected').data().length;
                });
            });

            datatableSelected.ajax.reload(() => {
                $('#datatable-selected').DataTable().ajax.reload(() => {
                    document.getElementById('selected-row-count-2').innerText = $('#datatable-selected').DataTable().rows('.selected').data().length;
                });
            });
        });

        // document.getElementById("tgl_form").addEventListener("change", function () {
        //     datatableSelect.ajax.reload(() => {
        //         $('#datatable-select').DataTable().ajax.reload(() => {
        //             document.getElementById('selected-row-count-1').innerText = $('#datatable-select').DataTable().rows('.selected').data().length;
        //         });
        //     });
        // });

        $('#datatable-select thead tr').clone(true).appendTo('#datatable-select thead');
        $('#datatable-select thead tr:eq(1) th').each(function(i) {
            if (i != 8) {
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control form-control-sm" />');

                $('input', this).on('keyup change', function() {
                    if (datatableSelect.column(i).search() !== this.value) {
                        datatableSelect
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            } else {
                $(this).empty();
            }
        });

        let datatableSelect = $("#datatable-select").DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('create-cut-plan') }}',
                data: function(d) {
                    d.tanggal = $('#tanggal').val();
                },
            },
            columns: [
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
                    data: 'act_costing_ws'
                },
                {
                    data: 'style'
                },
                {
                    data: 'color'
                },
                {
                    data: 'panel'
                },
                {
                    data: 'marker_details'
                },
                {
                    data: 'qty_ply'
                },
            ],
            columnDefs: [
                {
                    targets: [2],
                    render: (data, type, row, meta) => {

                        return data ? data.toUpperCase() : "-";
                    }
                },
                {
                    targets: [0,1,2,3,4],
                    className: "text-nowrap"
                },
            ],
            rowCallback: function( row, data, index ) {
                if (data['tipe_form_cut'] == 'manual') {
                    $('td', row).css('background-color', '#e7dcf7');
                    $('td', row).css('border', '0.15px solid #d0d0d0');
                } else if (data['tipe_form_cut'] == 'pilot') {
                    $('td', row).css('background-color', '#c5e0fa');
                    $('td', row).css('border', '0.15px solid #d0d0d0');
                }
            }
        });

        // Datatable row selection
        datatableSelect.on('click', 'tbody tr', function(e) {
            e.currentTarget.classList.toggle('selected');
            document.getElementById('selected-row-count-1').innerText = $('#datatable-select').DataTable().rows('.selected').data().length;
        });

        function addToCutPlan(element) {
            let tanggal = $("#tanggal").val();
            let selectedForm = $('#datatable-select').DataTable().rows('.selected').data();
            let formCutPlan = [];
            for (let key in selectedForm) {
                if (!isNaN(key)) {
                    formCutPlan.push({
                        no_form: selectedForm[key]['no_form']
                    });
                }
            }

            if (tanggal && formCutPlan.length > 0) {
                element.setAttribute('disabled', true);

                $.ajax({
                    type: "POST",
                    url: '{!! route('store-cut-plan') !!}',
                    data: {
                        tanggal: tanggal,
                        formCutPlan: formCutPlan
                    },
                    success: function(res) {
                        element.removeAttribute('disabled');

                        if (res.status == 200) {
                            iziToast.success({
                                title: 'Success',
                                message: res.message,
                                position: 'topCenter'
                            });
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: res.message,
                                position: 'topCenter'
                            });
                        }

                        if (res.table != '') {
                            $('#' + res.table).DataTable().ajax.reload(() => {
                                document.getElementById('selected-row-count-2').innerText = $('#' + res.table).DataTable().rows('.selected').data().length;
                            });

                            $('#datatable-select').DataTable().ajax.reload(() => {
                                document.getElementById('selected-row-count-1').innerText = $('#datatable-select').DataTable().rows('.selected').data().length;
                            });
                        }

                        if (res.additional) {
                            let message = "";

                            if (res.additional['success'].length > 0) {
                                res.additional['success'].forEach(element => {
                                    message += element['no_form'] + " - Berhasil <br>";
                                });
                            }

                            if (res.additional['fail'].length > 0) {
                                res.additional['fail'].forEach(element => {
                                    message += element['no_form'] + " - Gagal <br>";
                                });
                            }

                            if (res.additional['exist'].length > 0) {
                                res.additional['exist'].forEach(element => {
                                    message += element['no_form'] + " - Sudah Ada <br>";
                                });
                            }

                            if ((res.additional['success'].length + res.additional['fail'].length + res
                                    .additional['exist'].length) > 1) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Hasil Transfer',
                                    html: message,
                                    showCancelButton: false,
                                    showConfirmButton: true,
                                    confirmButtonText: 'Oke',
                                });
                            }
                        }
                    },
                    error: function(jqXHR) {
                        element.removeAttribute('disabled');

                        let res = jqXHR.responseJSON;
                        let message = '';

                        for (let key in res.errors) {
                            message = res.errors[key];
                        }

                        iziToast.error({
                            title: 'Error',
                            message: 'Terjadi kesalahan. ' + message,
                            position: 'topCenter'
                        });
                    }
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: "Harap isi tanggal plan dan tentukan form cut nya",
                    showCancelButton: false,
                    showConfirmButton: true,
                    confirmButtonText: 'Oke',
                });
            }
        }

        $('#datatable-selected thead tr').clone(true).appendTo('#datatable-selected thead');
        $('#datatable-selected thead tr:eq(1) th').each(function(i) {
            if (i != 8 && i != 9) {
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control form-control-sm"/>');

                $('input', this).on('keyup change', function() {
                    if (datatableSelected.column(i).search() !== this.value) {
                        datatableSelected
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            } else {
                $(this).empty();
            }
        });

        let datatableSelected = $("#datatable-selected").DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('get-selected-form') }}',
                data: function(d) {
                    d.tanggal = $('#tanggal').val();
                },
            },
            columns: [
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
                    data: 'act_costing_ws'
                },
                {
                    data: 'style'
                },
                {
                    data: 'color'
                },
                {
                    data: 'panel'
                },
                {
                    data: 'status_form'
                },
                {
                    data: 'marker_details'
                },
                {
                    data: 'qty_ply'
                },
            ],
            columnDefs: [
                {
                    targets: [2],
                    render: (data, type, row, meta) => {
                        let color = "";

                        if (row.status == 'SELESAI PENGERJAAN') {
                            color = '#087521';
                        } else if (row.status == 'PENGERJAAN FORM CUTTING') {
                            color = '#2243d6';
                        } else if (row.status == 'PENGERJAAN FORM CUTTING DETAIL') {
                            color = '#2243d6';
                        } else if (row.status == 'PENGERJAAN FORM CUTTING SPREAD') {
                            color = '#2243d6';
                        }

                        return data ? "<span style='color: " + color + "' >" + data.toUpperCase() + "</span>" : "<span style=' color: " + color + "'>-</span>"
                    }
                },
                {
                    targets: [8],
                    className: "text-center align-middle",
                    render: (data, type, row, meta) => {
                        icon = "";

                        switch (data) {
                            case "idle":
                                icon = `<i class="fas fa-file fa-lg"></i>`;
                                break;
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
                    targets: [0,1,2,3,4],
                    className: "text-nowrap"
                },
                {
                    targets: '_all',
                    render: (data, type, row, meta) => {
                        let color = "";

                        if (row.status == 'finish') {
                            color = '#087521';
                        } else if (row.status == 'form') {
                            color = '#2243d6';
                        } else if (row.status == 'form detail') {
                            color = '#2243d6';
                        } else if (row.status == 'form spreading') {
                            color = '#2243d6';
                        }

                        return data ? "<span style='color: " + color + "' >" + data + "</span>" : "<span style=' color: " + color + "'>-</span>"
                    }
                }
            ],
            rowCallback: function( row, data, index ) {
                if (data['tipe_form_cut'] == 'manual') {
                    $('td', row).css('background-color', '#e7dcf7');
                    $('td', row).css('border', '0.15px solid #d0d0d0');
                } else if (data['tipe_form_cut'] == 'pilot') {
                    $('td', row).css('background-color', '#c5e0fa');
                    $('td', row).css('border', '0.15px solid #d0d0d0');
                }
            }
        });

        function removeCutPlan(element) {
            element.setAttribute('disabled', true);

            let tanggal = $("#tanggal").val();
            let selectedForm = $('#datatable-selected').DataTable().rows('.selected').data();
            let formCutPlan = [];
            for (let key in selectedForm) {
                if (!isNaN(key)) {
                    formCutPlan.push({
                        no_form: selectedForm[key]['no_form'],
                        status_form: selectedForm[key]['status_form']
                    });
                }
            }

            if (tanggal && formCutPlan.length > 0 && !(formCutPlan.some(item => item.status_form.includes('form') || item.status_form.includes('form detail') || item.status_form.includes('form spreading') || item.status_form.includes('finish')))) {
                Swal.fire({
                    icon: 'info',
                    title: 'Singkirkan Form yang dipilih?',
                    text: 'Yakin akan menyingkirkan form?',
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonText: 'Singkirkan',
                    confirmButtonColor: "#d33141",
                }).then(async (result) => {
                    element.removeAttribute('disabled');

                    if (result.isConfirmed) {
                        element.setAttribute('disabled', true);

                        $.ajax({
                            type: "DELETE",
                            url: '{!! route('destroy-cut-plan') !!}',
                            data: {
                                tanggal: tanggal,
                                formCutPlan: formCutPlan
                            },
                            success: function(res) {
                                element.removeAttribute('disabled');

                                if (res.status == 200) {
                                    iziToast.success({
                                        title: 'Success',
                                        message: res.message,
                                        position: 'topCenter'
                                    });
                                } else {
                                    iziToast.error({
                                        title: 'Error',
                                        message: res.message,
                                        position: 'topCenter'
                                    });
                                }

                                if (res.table != '') {
                                    $('#' + res.table).DataTable().ajax.reload(() => {
                                        document.getElementById('selected-row-count-2')
                                            .innerText = $('#' + res.table).DataTable()
                                            .rows('.selected').data().length;
                                    });

                                    $('#datatable-select').DataTable().ajax.reload(() => {
                                        document.getElementById('selected-row-count-1')
                                            .innerText = $('#datatable-select').DataTable()
                                            .rows('.selected').data().length;
                                    });
                                }

                                if (res.additional) {
                                    let message = "";

                                    if (res.additional['success'].length > 0) {
                                        res.additional['success'].forEach(element => {
                                            message += element['no_form'] +
                                                " - Berhasil <br>";
                                        });
                                    }

                                    if (res.additional['fail'].length > 0) {
                                        res.additional['fail'].forEach(element => {
                                            message += element['no_form'] + " - Gagal <br>";
                                        });
                                    }

                                    if (res.additional['success'].length + res.additional['fail'].length > 1) {
                                        Swal.fire({
                                            icon: 'info',
                                            title: 'Plan berhasil disingkirkan',
                                            html: message,
                                            showCancelButton: false,
                                            showConfirmButton: true,
                                            confirmButtonText: 'Oke',
                                        });
                                    }
                                }
                            },
                            error: function(jqXHR) {
                                element.removeAttribute('disabled');

                                let res = jqXHR.responseJSON;
                                let message = '';

                                for (let key in res.errors) {
                                    message = res.errors[key];
                                }

                                iziToast.error({
                                    title: 'Error',
                                    message: 'Terjadi kesalahan. ' + message,
                                    position: 'topCenter'
                                });
                            }
                        })
                    }
                });
            } else {
                element.removeAttribute('disabled');

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: "Harap isi tanggal plan dan tentukan form cut yang belum diproses",
                    showCancelButton: false,
                    showConfirmButton: true,
                    confirmButtonText: 'Oke',
                });
            }
        }

        // Datatable selected row selection
        datatableSelected.on('click', 'tbody tr', function(e) {
            e.currentTarget.classList.toggle('selected');
            document.getElementById('selected-row-count-2').innerText = $('#datatable-selected').DataTable().rows('.selected').data().length;
        });
    </script>
@endsection
