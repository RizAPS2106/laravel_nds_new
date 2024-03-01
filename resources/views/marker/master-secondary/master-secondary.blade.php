@extends('layouts.index')

@section('content')
    <div class="card">
        <div class="card-header bg-sb text-light">
            <h5 class="card-title fw-bold mb-0"><i class="fas fa-location-arrow fa-sm"></i> Master Secondary</h5>
        </div>
        <div class="card-body">
            <button type="button" class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#createMasterSecondaryModal">
                <i class="fas fa-plus"></i>
                Baru
            </button>
            <div class="table-responsive">
                <table id="datatable-master-secondary" class="table table-bordered table-sm w-100">
                    <thead>
                        <tr>
                            <th class="align-bottom">Action</th>
                            <th>Kode Secondary</th>
                            <th>Tujuan Secondary</th>
                            <th>Proses</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createMasterSecondaryModal" tabindex="-1" aria-labelledby="createMasterSecondaryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('store-master-secondary') }}" method="post" onsubmit="submitForm(this, event)">
                    <div class="modal-header bg-sb text-light">
                        <h1 class="modal-title fs-5" id="createMasterSecondaryLabel"><i class="fas fa-location-arrow fa-sm"></i> Tambah Data Master Secondary</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tujuan</label>
                            <select class="form-control select2bs4" id="tujuan" name="tujuan" style="width: 100%;">
                                <option selected="selected" value="">Pilih Tujuan</option>
                                @foreach ($tujuan as $item)
                                    <option value="{{ $item->isi }}">{{ $item->tampil }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Proses</label>
                            <input type="text" class="form-control" name="proses" id="proses" autocomplete="off" value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editMasterSecondaryModal" tabindex="-1" aria-labelledby="editMasterSecondaryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('update-master-secondary') }}" method="post" onsubmit="submitForm(this, event)">
                    @method('PUT')
                    <div class="modal-header bg-sb text-light">
                        <h1 class="modal-title fs-5" id="editMasterPartLabel"><i class="fa fa-edit"></i> Ubah Data Master Secondary</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="edit_id" id="edit_id" value="">
                        <div class="mb-3">
                            <label class="form-label">Tujuan Secondary</label>
                            <select class="form-control select2bs4" id="edit_tujuan" name="edit_tujuan" style="width: 100%;">
                                <option selected="selected" value="">Pilih Tujuan</option>
                                @foreach ($tujuan as $item)
                                    <option value="{{ $item->isi }}">
                                        {{ $item->tampil }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Proses</label>
                            <input type="text" class="form-control" name="edit_proses" id="edit_proses" value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('custom-script')
    <script>
        let datatableMasterSecondary= $("#datatable-master-secondary").DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('master-secondary') }}',
            },
            columns: [
                {
                    data: 'id'
                },
                {
                    data: 'kode',
                },
                {
                    data: 'tujuan',
                },
                {
                    data: 'proses'
                },
            ],
            columnDefs: [
                {
                    targets: [0],
                    className: "align-middle",
                    render: (data, type, row, meta) => {
                        return `
                            <div class='d-flex gap-1 justify-content-center'>
                                <a class='btn btn-primary btn-sm' data-bs-toggle="modal" data-bs-target="#editMasterSecondaryModal" onclick='editData(` + JSON.stringify(row) + `, "editMasterSecondaryModal", [{"function" : "datatableMasterSecondaryReload()"}]);'>
                                    <i class='fa fa-edit'></i>
                                </a>
                                <a class='btn btn-danger btn-sm' data='` + JSON.stringify(row) + `' data-url='{{ route('destroy-master-secondary') }}/` + row['id'] + `' onclick='deleteData(this)'>
                                    <i class='fa fa-trash'></i>
                                </a>
                            </div>
                        `;
                    }
                }
            ],
        });

        function datatableMasterSecondaryReload() {
            datatableMasterSecondary.ajax.reload()
        }
    </script>
@endsection
