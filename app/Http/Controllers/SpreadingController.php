<?php

namespace App\Http\Controllers;

use App\Models\CutPlan;
use App\Models\FormCutInput;
use App\Models\FormCutInputDetail;
use App\Models\FormCutInputDetailLap;
use App\Models\MarkerDetail;
use App\Models\Marker;
use App\Models\Stocker;
use App\Models\StockerDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
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
                $additionalQuery .= " and a.tgl_form_cut >= '" . $request->dateFrom . "' ";
            }

            if ($request->dateTo) {
                $additionalQuery .= " and a.tgl_form_cut <= '" . $request->dateTo . "' ";
            }

            $keywordQuery = "";
            if ($request->search["value"]) {
                $keywordQuery = "
                    and (
                        a.id_marker like '%" . $request->search["value"] . "%' OR
                        a.no_meja like '%" . $request->search["value"] . "%' OR
                        a.no_form like '%" . $request->search["value"] . "%' OR
                        a.tgl_form_cut like '%" . $request->search["value"] . "%' OR
                        b.act_costing_ws like '%" . $request->search["value"] . "%' OR
                        panel like '%" . $request->search["value"] . "%' OR
                        b.color like '%" . $request->search["value"] . "%' OR
                        a.status like '%" . $request->search["value"] . "%' OR
                        users.name like '%" . $request->search["value"] . "%'
                    )
                ";
            }

            $data_spreading = DB::select("
                SELECT
                    a.id,
                    a.no_meja,
                    a.id_marker,
                    a.no_form,
                    a.no_cut,
                    a.tgl_form_cut,
                    b.id marker_id,
                    b.act_costing_ws ws,
                    b.style,
                    CONCAT(b.panel, ' - ', b.urutan_marker) panel,
                    b.color,
                    a.status,
                    users.name nama_meja,
                    b.panjang_marker,
                    UPPER(b.unit_panjang_marker) unit_panjang_marker,
                    b.comma_marker,
                    UPPER(b.unit_comma_marker) unit_comma_marker,
                    b.lebar_marker,
                    UPPER(b.unit_lebar_marker) unit_lebar_marker,
                    CONCAT(COALESCE(a.total_lembar, '0'), '/', a.qty_ply) ply_progress,
                    COALESCE(a.qty_ply, 0) qty_ply,
                    COALESCE(b.gelar_qty, 0) gelar_qty,
                    COALESCE(a.total_lembar, '0') total_lembar,
                    b.po_marker,
                    b.urutan_marker,
                    b.cons_marker,
                    UPPER(b.tipe_marker) tipe_marker,
                    a.tipe_form_cut,
                    COALESCE(b.notes, '-') notes,
                    GROUP_CONCAT(DISTINCT CONCAT(marker_input_detail.size, '(', marker_input_detail.ratio, ')') ORDER BY master_size_new.urutan ASC SEPARATOR ', ') marker_details,
                    cutting_plan.tgl_plan,
                    cutting_plan.app
                FROM `form_cut_input` a
                left join cutting_plan on cutting_plan.no_form_cut_input = a.no_form
                left join users on users.id = a.no_meja
                left join marker_input b on a.id_marker = b.kode and b.cancel = 'N'
                left join marker_input_detail on b.id = marker_input_detail.marker_id
                left join master_size_new on marker_input_detail.size = master_size_new.size
                where
                    a.id is not null
                    " . $additionalQuery . "
                    " . $keywordQuery . "
                GROUP BY a.id
                ORDER BY
                    FIELD(a.status, 'PENGERJAAN MARKER', 'PENGERJAAN FORM CUTTING', 'PENGERJAAN FORM CUTTING DETAIL', 'PENGERJAAN FORM CUTTING SPREAD', 'SPREADING', 'SELESAI PENGERJAAN'),
                    FIELD(a.tipe_form_cut, null, 'PILOT', 'NORMAL', 'MANUAL'),
                    FIELD(a.app, 'Y', 'N', null),
                    a.no_form desc,
                    a.updated_at desc
            ");

            return DataTables::of($data_spreading)->toJson();
        }

        $meja = User::select("id", "name", "username")->where('type', 'meja')->get();

        return view('spreading.spreading', ['meja' => $meja, 'page' => 'dashboard-cutting', "subPageGroup" => "proses-cutting", "subPage" => "spreading"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tgl_f = Carbon::today()->toDateString();
        // dd($tgl_f);

        // $data_ws = DB::select("select act_costing_id, act_costing_ws ws from marker_input where tgl_cutting = '$tgl_f' group by act_costing_id");

        $data_ws = DB::select("select act_costing_id, act_costing_ws ws from marker_input a
        left join (select id_marker from form_cut_input group by id_marker ) b on a.kode = b.id_marker
        where a.cancel = 'N' and ((a.gelar_qty_balance is null and b.id_marker is null) or a.gelar_qty_balance > 0)
        group by act_costing_id");


        return view('spreading.create-spreading', ['data_ws' => $data_ws, 'page' => 'dashboard-cutting', "subPageGroup" => "proses-cutting", "subPage" => "spreading"]);
    }

    public function getOrderInfo(Request $request)
    {
        $order = DB::connection('mysql_sb')->table('act_costing')->selectRaw('act_costing.id, act_costing.kpno, act_costing.styleno, act_costing.qty order_qty, mastersupplier.supplier buyer')->leftJoin('mastersupplier', 'mastersupplier.Id_Supplier', '=', 'act_costing.id_buyer')->where('act_costing.kpno', $request->ws)->first();

        return json_encode($order);
    }

    public function getno_marker(Request $request)
    {
        $tgl_f = Carbon::today()->toDateString();
        // $datano_marker = DB::select("select *,  concat(kode,' - ',color, ' - (',panel, ' - ',urutan_marker, ' )') tampil
        // from marker_input where act_costing_id = '" . $request->cbows . "' and tgl_cutting = '$tgl_f' order by urutan_marker asc");
        $datano_marker = DB::select("select *,  concat(kode,' - ',color, ' - (',panel, ' - ',urutan_marker, ' )') tampil  from marker_input a
        left join (select id_marker from form_cut_input group by id_marker ) b on a.kode = b.id_marker
        where act_costing_id = '" . $request->cbows . "' and (((a.gelar_qty_balance is null or a.gelar_qty_balance = 0) and b.id_marker is null) or a.gelar_qty_balance > 0) and a.cancel = 'N' order by urutan_marker asc");
        $html = "<option value=''>Pilih No Marker</option>";

        foreach ($datano_marker as $datanomarker) {
            $html .= " <option value='" . $datanomarker->id . "'>" . $datanomarker->tampil . "</option> ";
        }

        return $html;
    }

    public function getdata_marker(Request $request)
    {
        $data_marker = DB::select("select a.* from marker_input a
        where a.id = '" . $request->cri_item . "'");

        return json_encode($data_marker ? $data_marker[0] : null);
    }

    public function getdata_ratio(Request $request)
    {
        $markerId = $request->cbomarker ? $request->cbomarker : 0;

        $data_ratio = DB::select("
            select
                *
            from
                marker_input_detail
            where marker_id = '" . $markerId . "'
        ");

        return DataTables::of($data_ratio)->toJson();
    }

    public function store(Request $request)
    {
        ini_set('max_execution_time', 3600);

        $txttglcut = date('Y-m-d');
        $validatedRequest = $request->validate([
            "txtqty_ply_cut" => "required",
            "txtpanel" => "required",
            "txtcolor" => "required",
            "txtbuyer" => "required",
            "txtstyle" => "required",
            "txt_p_marker" => "required",
            "txt_unit_p_marker" => "required",
            "txt_comma_p_marker" => "required",
            "txt_unit_comma_p_marker" => "required",
            "txt_po_marker" => "required",
            "txt_l_marker" => "required",
            "txt_unit_l_marker" => "required",
            "txt_qty_gelar" => "required",
            "txt_ws" => "required",
            "txt_cons_ws" => "required",
            "txt_cons_marker" => "required",
            "txtid_marker" => "required",
        ]);

        $qtyPlyMarkerModulus = intval($request['hitungmarker']) % intval($request['txtqty_ply_cut']);
        $timestamp = Carbon::now();
        $formcutDetailData = [];
        $message = "";

        if ($request['tarik_sisa']) {
            $request['hitungform'] = $request['hitungform'] > 1 ? $request['hitungform'] - 1 : $request['hitungform'];
        }

        $keterangan = $request["notes"];

        if ($request["tipe_form"] != "Pilot") {
            if ((!$request["notes"] || $request["notes"] == "") && $request["tipe_form"] != "Regular") {
                $keterangan = $request["tipe_form"];
            }

            $request["tipe_form"] = "normal";
        }

        $totalQtyPly = 0;
        for ($i = 1; $i <= intval($request['hitungform']); $i++) {
            $date = date('Y-m-d');
            $hari = substr($date, 8, 2);
            $bulan = substr($date, 5, 2);
            $now = Carbon::now();

            $lastForm = FormCutInput::select("no_form")->whereRaw("no_form LIKE '".$hari."-".$bulan."%'")->orderBy("id", "desc")->first();
            $urutan =  $lastForm ? (str_replace($hari."-".$bulan."-", "", $lastForm->no_form) + $i) : $i;

            $no_form = "$hari-$bulan-$urutan";

            $qtyPly = $request['txtqty_ply_cut'];

            if ($i == intval($request['hitungform'])) {
                if ($request['tarik_sisa']) {
                    $qtyPly = $qtyPlyMarkerModulus > 0 ? $request['txtqty_ply_cut'] + $qtyPlyMarkerModulus : $request['txtqty_ply_cut'];
                } else {
                    if (intval($request['hitungform'] > 1)) {
                        $qtyPly = $qtyPlyMarkerModulus > 0 ? $qtyPlyMarkerModulus : $request['txtqty_ply_cut'];
                    }
                }
            }

            array_push($formcutDetailData, [
                "id_marker" => $request["txtid_marker"],
                "tipe_form_cut" => $request["tipe_form"],
                "no_form" => $no_form,
                "tgl_form_cut" => $txttglcut,
                "status" => "SPREADING",
                "user" => "user",
                "cancel" => "N",
                "qty_ply" => $qtyPly,
                "tgl_input" => $timestamp,
                "notes" => $keterangan,
                "created_at" => $timestamp,
                "updated_at" => $timestamp,
            ]);

            $totalQtyPly += $qtyPly;
            $message .= "$no_form <br>";
        }

        $markerDetailStore = FormCutInput::insert($formcutDetailData);

        if ($totalQtyPly > 0) {
            $updateMarker = Marker::where("kode", $request["txtid_marker"])->
                update([
                    'gelar_qty_balance' => DB::raw('gelar_qty_balance - '.$totalQtyPly)
                ]);
        }

        return array(
            "status" => 200,
            "message" => $message,
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
                    $spreadingFormDetails = FormCutInputDetail::where('no_form_cut_input', $spreadingForm->no_form_cut_input)->get();
                    $deleteSpreadingFormDetail = FormCutInputDetail::where('no_form_cut_input', $spreadingForm->no_form_cut_input)->delete();
                    $deleteCutPlan = CutPlan::where('no_form_cut_input', $spreadingForm->no_form_cut_input)->delete();

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
