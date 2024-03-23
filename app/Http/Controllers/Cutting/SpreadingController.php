<?php

namespace App\Http\Controllers\Cutting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Models\Marker;
use App\Models\MarkerDetail;
use App\Models\CutPlan;
use App\Models\FormCutInput;
use App\Models\FormCutInputDetail;
use App\Models\FormCutInputDetailLap;
use App\Models\Stocker;
use App\Models\StockerDetail;
use App\Models\User;

use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use DB;

class SpreadingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $additionalQuery = "";

            if ($request->dateFrom) {
                $additionalQuery .= " and form_cut_input.tanggal >= '" . $request->tanggal_awal . "' ";
            }

            if ($request->dateTo) {
                $additionalQuery .= " and form_cut_input.tanggal <= '" . $request->tanggal_akhir . "' ";
            }

            $keywordQuery = "";
            if ($request->search["value"]) {
                $keywordQuery = "
                    and (
                        form_cut_input.tanggal like '%" . $request->search["value"] . "%' OR
                        form_cut_input.no_form like '%" . $request->search["value"] . "%' OR
                        form_cut_input.meja_username like '%" . $request->search["value"] . "%' OR
                        form_cut_input.status_form like '%" . $request->search["value"] . "%' OR
                        marker_input.kode like '%" . $request->search["value"] . "%' OR
                        marker_input.act_costing_ws like '%" . $request->search["value"] . "%' OR
                        marker_input.color like '%" . $request->search["value"] . "%' OR
                        marker_input.panel like '%" . $request->search["value"] . "%'
                    )
                ";
            }

            $spreadingForms = DB::select("
                SELECT
                    form_cut_input.id,
                    form_cut_input.marker_input_kode,
                    marker_input.act_costing_ws,
                    marker_input.style,
                    marker_input.color,
                    CONCAT(marker_input.panel, ' - ', marker_input.urutan_marker) panel,
                    marker_input.panjang_marker,
                    UPPER(marker_input.unit_panjang_marker) unit_panjang_marker,
                    marker_input.comma_marker,
                    UPPER(marker_input.unit_comma_marker) unit_comma_marker,
                    marker_input.lebar_marker,
                    UPPER(marker_input.unit_lebar_marker) unit_lebar_marker,
                    form_cut_input.tanggal,
                    form_cut_input.no_form,
                    form_cut_input.no_cut_form,
                    form_cut_input.tipe_form,
                    form_cut_input.status_form,
                    form_cut_input.meja_id,
                    users.name,
                    CONCAT(COALESCE(form_cut_input.total_ply, '0'), '/', COALESCE(form_cut_input.qty_ply, 0)) ply_progress,
                    COALESCE(form_cut_input.qty_ply, 0) qty_ply,
                    COALESCE(form_cut_input.total_ply, '0') total_ply,
                    marker_input.po_marker,
                    marker_input.urutan_marker,
                    marker_input.cons_marker,
                    marker_input.tipe_marker,
                    COALESCE(marker_input.gelar_qty_marker, 0) gelar_qty_marker,
                    COALESCE(marker_input.notes, '-') notes,
                    GROUP_CONCAT(DISTINCT CONCAT(marker_input_detail.size, '(', marker_input_detail.ratio, ')') ORDER BY master_size_new.urutan ASC SEPARATOR ', ') marker_details,
                    cutting_plan.tanggal tanggal_plan,
                    cutting_plan.app
                FROM form_cut_input
                LEFT JOIN cutting_plan ON cutting_plan.no_form = form_cut_input.no_form
                LEFT JOIN users ON users.id = form_cut_input.meja_id
                LEFT JOIN marker_input ON form_cut_input.marker_input_kode = marker_input.kode and marker_input.cancel = 'N'
                LEFT JOIN marker_input_detail ON marker_input.kode = marker_input_detail.marker_input_kode
                LEFT JOIN master_size_new ON marker_input_detail.size = master_size_new.size
                WHERE
                    form_cut_input.id is not null
                    " . $additionalQuery . "
                    " . $keywordQuery . "
                GROUP BY
                    form_cut_input.id
                ORDER BY
                    FIELD(form_cut_input.status_form, null, 'marker', 'form', 'form detail', 'form spreading', 'spreading', 'finish'),
                    FIELD(form_cut_input.tipe_form, null, 'pilot', 'manual', 'normal'),
                    FIELD(form_cut_input.app, 'y', 'n', null),
                    form_cut_input.no_form desc,
                    form_cut_input.updated_at desc
            ");

            return DataTables::of($spreadingForms)->toJson();
        }

        $users = User::select("id", "name", "username")->where('type', 'meja')->get();

        return view('spreading.spreading', ['users' => $users, 'page' => 'dashboard-cutting', "subPageGroup" => "proses-cutting", "subPage" => "spreading"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $availableOrders = DB::select("
            select
                act_costing_id,
                act_costing_ws
            from
                marker_input
            left join
                (select marker_input_kode from form_cut_input group by marker_input_kode ) form_marker on marker_input.kode = form_marker.marker_input_kode
            where
                marker_input.cancel = 'N' and
                ((marker_input.gelar_qty_balance_marker is null and form_marker.marker_input_kode is null) or marker_input.gelar_qty_balance_marker > 0)
            group by
                act_costing_id
        ");


        return view('spreading.create-spreading', ['orders' => $availableOrders, 'page' => 'dashboard-cutting', "subPageGroup" => "proses-cutting", "subPage" => "spreading"]);
    }

    public function getMarkerOptions(Request $request)
    {
        $markers = DB::select("
            select
                *,
                concat(kode,' - ',color, ' - (',panel, ' - ',urutan_marker, ' )') tampil
            from
                marker_input
                left join (select marker_input_kode from form_cut_input group by marker_input_kode ) form_marker on marker_input.kode = form_marker.marker_input_kode
            where
                marker_input.act_costing_id = '" . $request->act_costing_id . "' and
                (((marker_input.gelar_qty_balance_marker is null or marker_input.gelar_qty_balance_marker = 0) and form_marker.marker_input_kode is null) or marker_input.gelar_qty_balance_marker > 0) and
                marker_input.cancel = 'N'
            order by
                urutan_marker asc
        ");

        $options = "<option value=''>Pilih No Marker</option>";

        foreach ($markers as $marker) {
            $options .= " <option value='" . $marker->kode . "'>" . $marker->kode . "</option> ";
        }

        return $options;
    }

    public function getMarkerInfo(Request $request)
    {
        $markerInfo = DB::select("
            select
                *
            from
                marker_input
            where kode = '" . $request->marker_input_kode . "'
        ");

        return json_encode($markerInfo ? $markerInfo[0] : null);
    }

    public function getMarkerRatio(Request $request)
    {
        $markerRatio = DB::select("
            select
                *
            from
                marker_input_detail
            where marker_input_kode = '" . $request->marker_input_kode . "'
        ");

        return DataTables::of($markerRatio)->toJson();
    }

    public function store(Request $request)
    {
        ini_set('max_execution_time', 3600);

        $validatedRequest = $request->validate([
            "act_costing_ws" => "required",
            "cons_ws" => "required",
            "panel" => "required",
            "color" => "required",
            "buyer" => "required",
            "style" => "required",
            "marker_input_kode" => "required",
            "p_marker" => "required",
            "unit_p_marker" => "required",
            "comma_p_marker" => "required",
            "unit_comma_p_marker" => "required",
            "l_marker" => "required",
            "unit_l_marker" => "required",
            "cons_marker" => "required",
            "po_marker" => "required",
            "qty_ply_marker" => "required",
            "qty_ply_cutting" => "required",
        ]);

        $timestamp = Carbon::now();
        $formCutInputArr = [];
        $message = "";

        // Tipe form beserta keterangan
        if ($request["tipe_form"] != "Pilot") {
            if ((!$request["notes"] || $request["notes"] == "") && $request["tipe_form"] != "normal") {
                $request["notes"] = $request["tipe_form"];
            }

            $request["tipe_form"] = "normal";
        }

        $qtyPlyMarkerModulus = intval($request['qty_ply_marker']) % intval($request['qty_ply_cutting']);

        $totalQtyPly = 0;
        for ($i = 1; $i <= intval($request['total_form']); $i++) {
            $date = date('Y-m-d');
            $hari = substr($date, 8, 2);
            $bulan = substr($date, 5, 2);
            $now = Carbon::now();

            $lastForm = FormCutInput::select("no_form")->whereRaw("no_form LIKE '".$hari."-".$bulan."%'")->orderBy("id", "desc")->first();
            $urutan =  $lastForm ? (str_replace($hari."-".$bulan."-", "", $lastForm->no_form) + $i) : $i;

            $noForm = "$hari-$bulan-$urutan";

            $qtyPly = $request['qty_ply_cutting'];

            if ($i == intval($request['total_form'])) {
                if ($request['tarik_sisa']) {
                    $qtyPly = $qtyPlyMarkerModulus > 0 ? $request['qty_ply_cutting'] + $qtyPlyMarkerModulus : $request['qty_ply_cutting'];
                } else {
                    if (intval($request['total_form'] > 1)) {
                        $qtyPly = $qtyPlyMarkerModulus > 0 ? $qtyPlyMarkerModulus : $request['qty_ply_cutting'];
                    }
                }
            }

            array_push($formCutInputArr, [
                "tanggal" => date('Y-m-d'),
                "marker_input_kode" => $request["marker_input_kode"],
                "no_form" => $noForm,
                "tipe_form" => $request["tipe_form"],
                "qty_ply" => $qtyPly,
                "notes" => $request["notes"],
                "cancel" => "n",
                "created_by" => Auth::user()->id,
                "created_by_username" => Auth::user()->username,
                "created_at" => $timestamp,
                "updated_at" => $timestamp,
            ]);

            $totalQtyPly += $qtyPly;
            $message .= $noForm ."<br>";
        }

        $markerDetailStore = FormCutInput::insert($formCutInputArr);

        if ($totalQtyPly > 0) {
            $updateMarker = Marker::where("kode", $request["marker_input_kode"])->
                update([
                    'gelar_qty_balance_marker' => DB::raw('gelar_qty_balance_marker - '.$totalQtyPly)
                ]);
        }

        return array(
            "status" => 200,
            "message" => "Marker berhasil dijadikan Form <br>". $message,
            "callback" => "clearStep()",
            "additional" => [],
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Spreading  $spreading
     * @return \Illuminate\Http\Response
     */
    public function show()   {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Spreading  $spreading
     * @return \Illuminate\Http\Response
     */
    public function edit()   {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Spreading  $spreading
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validatedRequest = $request->validate([
            "edit_id" => "required",
            "edit_no_meja" => "required",
        ]);

        $updateNoMeja = FormCutInput::where('id', $validatedRequest['edit_id'])->update([
            'no_meja' => $validatedRequest['edit_no_meja']
        ]);

        if ($updateNoMeja) {
            $updatedData = FormCutInput::where('id', $validatedRequest['edit_id'])->first();
            $meja = User::where('id', $validatedRequest['edit_no_meja'])->first();
            return array(
                'status' => 200,
                'message' => 'Alokasi Meja "' . ucfirst($meja->name) . '" ke form "' . $updatedData->no_form . '" berhasil',
                'redirect' => '',
                'table' => 'datatable',
                'additional' => [],
            );
        }

        return array(
            'status' => 400,
            'message' => 'Data produksi gagal diubah',
            'redirect' => '',
            'table' => 'datatable',
            'additional' => [],
        );
    }

    public function updateStatus(Request $request) {
        $validatedRequest = $request->validate([
            "edit_id_status" => "required",
            "edit_status" => "required",
        ]);

        $updateStatusForm = FormCutInput::where('id', $validatedRequest['edit_id_status'])->update([
            'status' => $validatedRequest['edit_status']
        ]);

        if ($updateStatusForm) {
            $updatedData = FormCutInput::where('id', $validatedRequest['edit_id_status'])->first();
            return array(
                'status' => 200,
                'message' => 'Form  "' . $updatedData->no_form. '" berhasil diubah ke status '.$validatedRequest['edit_status'].'. ',
                'redirect' => '',
                'table' => 'datatable',
                'additional' => [],
            );
        }

        return array(
            'status' => 400,
            'message' => 'Data produksi gagal diubah',
            'redirect' => '',
            'table' => 'datatable',
            'additional' => [],
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FormCutInput  $formCutInput
     * @return \Illuminate\Http\Response
     */
    public function destroy(FormCutInput $formCutInput, $id)
    {
        $spreadingForm = FormCutInput::where('id', $id)->first();

        $checkStocker = Stocker::where("form_cut_id", $id)->get();
        $checkNumbering = StockerDetail::where("form_cut_id", $id)->get();

        if ($checkStocker->count() < 1 && $checkNumbering->count() < 1) {
            $deleteSpreadingForm = FormCutInput::where('id', $id)->delete();

            if ($deleteSpreadingForm) {
                $updateMarkerBalance = Marker::where("kode", $spreadingForm->id_marker)->update([
                    "gelar_qty_balance" => DB::raw('gelar_qty_balance + '.$spreadingForm->qty_ply)
                ]);

                if ($updateMarkerBalance) {
                    $spreadingFormDetails = FormCutInputDetail::where('no_form', $spreadingForm->no_form)->get();
                    $deleteSpreadingFormDetail = FormCutInputDetail::where('no_form', $spreadingForm->no_form)->delete();
                    $deleteCutPlan = CutPlan::where('no_form', $spreadingForm->no_form)->delete();

                    if ($deleteSpreadingFormDetail) {
                        $idFormDetailLapArr = [];
                        foreach ($spreadingFormDetails as $spreadingFormDetail) {
                            array_push($idFormDetailLapArr, $spreadingFormDetail->id);
                        }

                        $deleteSpreadingFormDetailLap = FormCutInputDetailLap::whereIn("form_cut_input_detail_id", $idFormDetailLapArr)->delete();
                    }

                    return array(
                        "status" => 200,
                        "message" => "Form berhasil dihapus",
                        "table" => "datatable"
                    );
                }
            }

            return array(
                "status" => 200,
                "message" => "Form tidak berhasil dihapus",
                "table" => "datatable"
            );
        }
    }
}
