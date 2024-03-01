<?php

namespace App\Http\Controllers\Marker;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Models\MasterSecondary;

use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class MasterSecondaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $masterSecondaries = MasterSecondary::query();

            return DataTables::eloquent($masterSecondaries)->
            filterColumn('kode', function ($query, $keyword) {
                $query->whereRaw("LOWER(kode) LIKE LOWER('%" . $keyword . "%')");
            })->
            filterColumn('tujuan', function ($query, $keyword) {
                $query->whereRaw("LOWER(tujuan) LIKE LOWER('%" . $keyword . "%')");
            })->
            filterColumn('proses', function ($query, $keyword) {
                $query->whereRaw("LOWER(proses) LIKE LOWER('%" . $keyword . "%')");
            })->
            order(function ($query) {
                $query->
                    orderBy('cancel', 'asc')->
                    orderBy('updated_at', 'desc')->
                    orderBy('kode', 'desc');
            })->
            toJson();
        }

        $tujuan = DB::select("select tujuan isi, tujuan tampil from master_tujuan");

        return view("marker.master-secondary.master-secondary", ["page" => "dashboard-marker",  "subPageGroup" => "master-marker", "subPage" => "master-secondary", 'tujuan' => $tujuan]);
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
        $validatedRequest = $request->validate([
            "tujuan" => "required",
            "proses" => "required",
        ]);

        $masterSecondaryLastCode = MasterSecondary::select("kode")->orderBy("id", "desc")->first();
        $masterSecondaryNumber = $masterSecondaryLastCode ? intval(str_replace("MS", "", $masterSecondaryLastCode->kode)) + 1 : 1;
        $masterSecondaryCode = 'MS' . sprintf('%05s', $masterSecondaryNumber);

        $masterSecondaryStore = MasterSecondary::create([
            "kode" => $masterSecondaryCode,
            "tujuan" => $validatedRequest["tujuan"],
            "proses" =>  strtoupper($validatedRequest["proses"]),
            "cancel" =>  'N',
            'created_by' => Auth::user()->name,
        ]);

        if ($masterSecondaryStore) {
            return array(
                "status" => 200,
                "message" => "Secondary <br> '".$validatedRequest["tujuan"]."' <br> berhasil ditambahkan. <br> '".$masterSecondaryCode."'",
                "additional" => [],
            );
        }

        return array(
            "status" => 400,
            "message" => "Part <br> '".$validatedRequest["tujuan"]."' <br> gagal ditambahkan.",
            "additional" => [],
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MasterSecondary  $masterSecondary
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request)
    {
        $data_master_secondary = DB::select("
            SELECT
                *
            FROM
                master_secondary
            where
                id = '$request->id'
        ");

        return $data_master_secondary ? json_encode($data_master_secondary[0]) : null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MasterSecondary  $masterSecondary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validatedRequest = $request->validate([
            "edit_id" => "required",
            "edit_tujuan" => "required",
            "edit_proses" => "required",
        ]);

        $masterSecondary = MasterSecondary::where("id", $validatedRequest['edit_id'])->first();

        if ($masterSecondary) {
            $masterSecondary->tujuan = $validatedRequest['edit_tujuan'];
            $masterSecondary->proses = $validatedRequest['edit_proses'];

            if ($masterSecondary->save()) {
                return array(
                    'status' => 200,
                    'message' => 'Data Master Secondary <br> "' . $masterSecondary->kode . '" <br> berhasil diubah <br> "'. $masterSecondary->tujuan.'-'.$masterSecondary->proses .'"',
                    'redirect' => '',
                    'table' => 'datatable-master-secondary',
                    'additional' => [],
                );
            }
        }

        return array(
            'status' => 400,
            'message' => 'Data master part <br> "'.$masterSecondary->tujuan.'-'.$masterSecondary->proses.'" <br> gagal diubah',
            'redirect' => '',
            'table' => 'datatable-master-part',
            'additional' => [],
        );
    }

    public function destroy(MasterSecondary $MasterSecondary, $id = 0)
    {
        $destroyMasterSecondary = MasterSecondary::find($id)->delete();

        if ($destroyMasterSecondary) {
            return array(
                'status' => 200,
                'message' => 'Master Secondary berhasil dihapus',
                'redirect' => '',
                'table' => 'datatable-master-secondary',
                'additional' => [],
            );
        }

        return array(
            'status' => 400,
            'message' => 'Master Secondary gagal dihapus',
            'redirect' => '',
            'table' => 'datatable-master-secondary',
            'additional' => [],
        );
    }
}
