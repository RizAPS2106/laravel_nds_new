<?php

namespace App\Http\Controllers\Marker;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;

use App\Models\MasterPart;

use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class MasterPartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Datatable server-side
        if ($request->ajax()) {
            $masterParts = MasterPart::query();

            return DataTables::eloquent($masterParts)->
                // Filter
                filterColumn('kode', function ($query, $keyword) {
                    $query->whereRaw("LOWER(kode) LIKE LOWER('%" . $keyword . "%')");
                })->
                filterColumn('nama_part', function ($query, $keyword) {
                    $query->whereRaw("LOWER(nama_part) LIKE LOWER('%" . $keyword . "%')");
                })->
                filterColumn('bagian', function ($query, $keyword) {
                    $query->whereRaw("LOWER(bagian) LIKE LOWER('%" . $keyword . "%')");
                })->

                // Order
                order(function ($query) {
                    $query->
                        orderBy('cancel', 'asc')->
                        orderBy('updated_at', 'desc')->
                        orderBy('kode', 'desc');
                })->
                toJson();
        }

        return view("marker.master-part.master-part", ["page" => "dashboard-marker",  "subPageGroup" => "master-marker", "subPage" => "master-part"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation
        $validatedRequest = $request->validate([
            "nama_part" => "required",
            "bagian" => "required",
        ]);

        $masterPartLastCode = MasterPart::select("kode")->orderBy("id", "desc")->first();
        $masterPartNumber = $masterPartLastCode ? intval(str_replace("MP", "", $masterPartLastCode->kode)) + 1 : 1;
        $masterPartCode = 'MP' . sprintf('%05s', $masterPartNumber);

        $masterPartStore = MasterPart::create([
            "kode" => $masterPartCode,
            "nama_part" => $validatedRequest["nama_part"],
            "bagian" => $validatedRequest["bagian"],
        ]);

        if ($masterPartStore) {
            return array(
                "status" => 200,
                "message" => "Part <br> '".$validatedRequest["nama_part"]."' <br> berhasil ditambahkan. <br> '".$masterPartCode."'",
                "redirect" => "",
                "additional" => [],
            );
        }

        return array(
            "status" => 400,
            "message" => "Part <br> '".$validatedRequest["nama_part"]."' <br> gagal ditambahkan.",
            "redirect" => "",
            "additional" => [],
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MasterPart  $masterPart
     * @return \Illuminate\Http\Response
     */
    public function show(MasterPart $masterPart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MasterPart  $masterPart
     * @return \Illuminate\Http\Response
     */
    public function edit(MasterPart $masterPart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MasterPart  $masterPart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MasterPart $masterPart, $id = 0)
    {
        // Validation
        $validatedRequest = $request->validate([
            "edit_id" => "required",
            "edit_nama_part" => "required",
            "edit_bagian" => "required",
        ]);

        // Master Part
        $masterPart = MasterPart::find($validatedRequest['edit_id']);

        if ($masterPart) {
            $masterPart->nama_part = $validatedRequest['edit_nama_part'];
            $masterPart->bagian = $validatedRequest['edit_bagian'];

            if ($masterPart->save()) {
                return array(
                    'status' => 200,
                    'message' => 'Data master part <br> "'.$masterPart->kode. '" <br> berhasil diubah <br> "'.$masterPart->nama_part.'"',
                    'redirect' => '',
                    'table' => 'datatable-master-part',
                    'additional' => [],
                );
            }
        }

        return array(
            'status' => 400,
            'message' => 'Data master part <br> "'.$validatedRequest['edit_nama_part'].'" <br> gagal diubah',
            'redirect' => '',
            'table' => 'datatable-master-part',
            'additional' => [],
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MasterPart  $masterPart
     * @return \Illuminate\Http\Response
     */
    public function destroy(MasterPart $masterPart, $id = 0)
    {
        $masterPart = MasterPart::find($id);

        if ($masterPart->delete()) {
            return array(
                'status' => 200,
                'message' => 'Master Part <br> "'.$masterPart->nama_part.'" <br> berhasil dihapus. <br> "'.$masterPart->kode.'"',
                'redirect' => '',
                'table' => 'datatable-master-part',
                'additional' => [],
            );
        }

        return array(
            'status' => 400,
            'message' => 'Master Part <br> "'.$masterPart->nama_part.'" <br> gagal dihapus. <br> "'.$masterPart->kode.'"',
            'redirect' => '',
            'table' => 'datatable-master-part',
            'additional' => [],
        );
    }
}
