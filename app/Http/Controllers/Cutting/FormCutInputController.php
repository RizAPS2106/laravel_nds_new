<?php

namespace App\Http\Controllers\Cutting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use App\Models\Marker;
use App\Models\MarkerDetail;
use App\Models\Part;
use App\Models\PartForm;
use App\Models\FormCutInput;
use App\Models\FormCutInputDetail;
use App\Models\FormCutInputDetailLap;
use App\Models\FormCutInputLostTime;
use App\Models\ScannedItem;
use App\Models\CutPlan;

use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use DB;

class FormCutInputController extends Controller
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
                $additionalQuery .= "and (cutting_plan.tanggal >= '" . $request->dateFrom . "' or DATE(a.updated_at) >= '". $request->dateFrom ."')";
            }

            if ($request->dateTo) {
                $additionalQuery .= " and (cutting_plan.tanggal <= '" . $request->dateTo . "' or DATE(a.updated_at) <= '". $request->dateTo ."')";
            }

            if (Auth::user()->type == "meja") {
                $additionalQuery .= " and a.meja_id = '" . Auth::user()->id . "' ";
            }

            $keywordQuery = "";
            if ($request->search["value"]) {
                $keywordQuery = "
                    and (
                        a.tanggal like '%" . $request->search["value"] . "%' OR
                        a.meja_id like '%" . $request->search["value"] . "%' OR
                        a.meja_username like '%" . $request->search["value"] . "%' OR
                        a.no_form like '%" . $request->search["value"] . "%' OR
                        a.tipe_form like '%" . $request->search["value"] . "%' OR
                        a.status_form like '%" . $request->search["value"] . "%' OR
                        a.marker_input_kode like '%" . $request->search["value"] . "%' OR
                        b.act_costing_ws like '%" . $request->search["value"] . "%' OR
                        b.panel like '%" . $request->search["value"] . "%' OR
                        b.color like '%" . $request->search["value"] . "%' OR
                        users.name like '%" . $request->search["value"] . "%' OR
                        user_app.name like '%" . $request->search["value"] . "%'
                    )
                ";
            }

            $spreadingForms = DB::select("
                SELECT
                    a.id,
                    b.id marker_id,
                    a.tanggal,
                    a.no_form,
                    a.tipe_form,
                    a.status_form,
                    a.meja_id,
                    a.meja_username,
                    a.marker_input_kode,
                    b.act_costing_ws,
                    b.style,
                    CONCAT(b.panel, ' - ', b.urutan_marker) panel,
                    b.color,
                    UPPER(users.name) meja,
                    b.panjang_marker panjang_marker,
                    UPPER(b.unit_panjang_marker) unit_panjang_marker,
                    b.comma_marker comma_marker,
                    UPPER(b.unit_comma_marker) unit_comma_marker,
                    b.lebar_marker lebar_marker,
                    UPPER(b.unit_lebar_marker) unit_lebar_marker,
                    CONCAT(COALESCE(a.total_ply, '0'), '/', a.qty_ply) ply_progress,
                    COALESCE(b.gelar_qty_marker, 0) gelar_qty,
                    COALESCE(a.qty_ply, 0) qty_ply,
                    COALESCE(a.total_ply, '0') total_ply,
                    b.po_marker po_marker,
                    b.urutan_marker urutan_marker,
                    b.cons_marker cons_marker,
                    UPPER(b.tipe_marker) tipe_marker,
                    b.notes,
                    cutting_plan.app,
                    user_app.name app_by_name,
                    GROUP_CONCAT(DISTINCT CONCAT(marker_input_detail.size, '(', marker_input_detail.ratio, ')') ORDER BY master_size_new.urutan ASC SEPARATOR ', ') marker_details
                FROM
                    cutting_plan
                    LEFT JOIN form_cut_input a ON a.no_form = cutting_plan.no_form
                    LEFT OUTER JOIN marker_input b ON a.marker_input_kode = b.kode and b.cancel = 'N'
                    LEFT OUTER JOIN marker_input_detail ON b.kode = marker_input_detail.marker_input_kode
                    LEFT JOIN master_size_new ON marker_input_detail.size = master_size_new.size
                    LEFT JOIN users ON users.id = a.meja_id
                    LEFT JOIN users as user_app ON user_app.id = cutting_plan.app_by
                WHERE
                    a.id is not null
                    " . $additionalQuery . "
                    " . $keywordQuery . "
                GROUP BY a.id
                ORDER BY
                    FIELD(a.status_form, 'marker', 'form', 'form detail', 'form spreading', 'idle', 'finish'),
                    FIELD(a.tipe_form, null, 'normal', 'manual', 'pilot'),
                    FIELD(cutting_plan.app, 'y', 'n', null),
                    a.no_form desc,
                    a.updated_at desc
            ");

            return DataTables::of($spreadingForms)->toJson();
        }

        return view('cutting.form-cutting.form-cut.form-cut-input', ["page" => "dashboard-cutting", "subPageGroup" => "proses-cutting", "subPage" => "form-cut-input"]);
    }

    public function getRatio(Request $request)
    {
        $markerId = $request->cbomarker ? $request->cbomarker : 0;

        $data_ratio = DB::select("
            select
                *
            from
                marker_input_detail
            where
                marker_id = '" . $markerId . "'
        ");

        return DataTables::of($data_ratio)->toJson();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FormCut  $formCut
     * @return \Illuminate\Http\Response
     */
    public function show(FormCut $formCut)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FormCut  $formCut
     * @return \Illuminate\Http\Response
     */
    public function edit(FormCut $formCut)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FormCut  $formCut
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FormCut $formCut)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FormCut  $formCut
     * @return \Illuminate\Http\Response
     */
    public function destroy(FormCut $formCut)
    {
        //
    }

    /**
     * Process the form cut input.
     *
     * @param  \App\Models\FormCut  $formCut
     * @return \Illuminate\Http\Response
     */
    public function process($id = 0)
    {
        $formCutInputData = FormCutInput::leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.marker_input_kode")->
            leftJoin("users", "users.id", "=", "form_cut_input.meja_id")->
            where('form_cut_input.id', $id)->
            first();

        $actCostingData = DB::connection("mysql_sb")->
            table('act_costing')->
            selectRaw('act_costing.id id, act_costing.styleno style, mastersupplier.Supplier buyer')->
            leftJoin('mastersupplier', 'mastersupplier.Id_Supplier', 'act_costing.id_buyer')->
            groupBy('act_costing.id')->
            where('act_costing.id', $formCutInputData->act_costing_id)->
            get();

        $markerDetailData = MarkerDetail::selectRaw("
                marker_input.kode kode_marker,
                marker_input_detail.size,
                marker_input_detail.so_det_id,
                marker_input_detail.ratio,
                marker_input_detail.qty_cutting
            ")->
            leftJoin("marker_input", "marker_input.kode", "=", "marker_input_detail.marker_input_kode")->
            where("marker_input.kode", $formCutInputData->marker_input_kode)->
            where("marker_input.cancel", "N")->
            get();

        if (Auth::user()->type == "meja" && Auth::user()->id != $formCutInputData->meja_id) {
            return Redirect::to('/home');
        }

        return view("cutting.form-cutting.form-cut.process-form-cut-input", [
            'id' => $id,
            'formCutInputData' => $formCutInputData,
            'actCostingData' => $actCostingData,
            'markerDetailData' => $markerDetailData,
            'page' => 'dashboard-cutting',
            "subPageGroup" => "proses-cutting",
            "subPage" => "form-cut-input"
        ]);
    }

    // public function getNumberData(Request $request)
    // {
    //     $numberData = DB::connection('mysql_sb')->table("bom_jo_item")->selectRaw("
    //             bom_jo_item.cons cons_ws
    //         ")->
    //         leftJoin("so_det", "so_det.id", "=", "bom_jo_item.id_so_det")->
    //         leftJoin("so", "so.id", "=", "so_det.id_so")->
    //         leftJoin("act_costing", "act_costing.id", "=", "so.id_cost")->
    //         leftJoin("masteritem", "masteritem.id_gen", "=", "bom_jo_item.id_item")->
    //         leftJoin("masterpanel", "masterpanel.id", "=", "bom_jo_item.id_panel")->
    //         where("act_costing.id", $request->act_costing_id)->where("so_det.color", $request->color)->
    //         where("masterpanel.nama_panel", $request->panel)->
    //         where("bom_jo_item.status", "M")->
    //         where("bom_jo_item.cancel", "N")->
    //         where("so_det.cancel", "N")->
    //         where("so.cancel_h", "N")->
    //         where("act_costing.status", "CONFIRM")->
    //         where("masteritem.mattype", "F")->
    //         where("masteritem.mattype", "F")->
    //         groupBy("so_det.color", "bom_jo_item.id_item", "bom_jo_item.unit")->first();

    //     return json_encode($numberData);
    // }

    public function getScannedItem($id = 0)
    {
        $newItem = DB::connection("mysql_sb")->select("
            SELECT
                br.id_roll roll_id,
                mi.itemdesc item_detail,
                mi.id_item item_id,
                goods_code,
                supplier,
                bpbno_int,
                pono,
                invno,
                ac.kpno,
                no_roll roll,
                qty_out qty,
                no_lot lot,
                bpb.unit,
                kode_rak
            FROM
                whs_bppb_det br
                INNER JOIN masteritem mi ON br.id_item = mi.id_item
                INNER JOIN bpb ON br.id_jo = bpb.id_jo AND br.id_item = bpb.id_item
                INNER JOIN mastersupplier ms ON bpb.id_supplier = ms.Id_Supplier
                INNER JOIN jo_det jd ON br.id_jo = jd.id_jo
                INNER JOIN so ON jd.id_so = so.id
                INNER JOIN act_costing ac ON so.id_cost = ac.id
                INNER JOIN master_rak mr ON br.no_rak = mr.kode_rak
            WHERE
                br.id_roll = '".$id."'
                AND cast(
                qty_out AS DECIMAL ( 11, 3 )) > 0.000
                LIMIT 1
        ");
        if ($newItem) {
            $scannedItem = ScannedItem::where('roll_id', $id)->where('item_id', $newItem[0]->item_id)->first();

            if ($scannedItem) {
                if (floatval($scannedItem->qty) > 0) {
                    return json_encode($scannedItem);
                }

                return json_encode(null);
            }

            return json_encode($newItem ? $newItem[0] : null);
        }

        $item = DB::connection("mysql_sb")->select("
            SELECT
                br.id roll_id,
                mi.itemdesc item_detail,
                mi.id_item item_id,
                goods_code,
                supplier,
                bpbno_int,
                pono,
                invno,
                ac.kpno,
                roll_no roll,
                roll_qty qty,
                lot_no lot,
                bpb.unit,
                kode_rak
            FROM
                bpb_roll br
                INNER JOIN bpb_roll_h brh ON br.id_h = brh.id
                INNER JOIN masteritem mi ON brh.id_item = mi.id_item
                INNER JOIN bpb ON brh.bpbno = bpb.bpbno
                AND brh.id_jo = bpb.id_jo
                AND brh.id_item = bpb.id_item
                INNER JOIN mastersupplier ms ON bpb.id_supplier = ms.Id_Supplier
                INNER JOIN jo_det jd ON brh.id_jo = jd.id_jo
                INNER JOIN so ON jd.id_so = so.id
                INNER JOIN act_costing ac ON so.id_cost = ac.id
                INNER JOIN master_rak mr ON br.id_rak_loc = mr.id
            WHERE
                br.id = '" . $id . "'
                AND cast(
                roll_qty AS DECIMAL ( 11, 3 )) > 0.000
                LIMIT 1
        ");
        if ($item) {
            $scannedItem = ScannedItem::where('roll_id', $id)->where('item_id', $item[0]->item_id)->first();

            if ($scannedItem) {
                if (floatval($scannedItem->qty) > 0) {
                    return json_encode($scannedItem);
                }

                return json_encode(null);
            }

            return json_encode($item ? $item[0] : null);
        }

        return  null;
    }

    public function getItem(Request $request) {
        $items = DB::connection("mysql_sb")->select("
            select
                ac.id,
                ac.id_buyer,
                ac.styleno,
                jd.id_jo,
                ac.kpno,
                mi.id_item item_id,
                mi.itemdesc
            from
                jo_det jd
                inner join (select * from so where so_date >= '2023-01-01') so on jd.id_so = so.id
                inner join act_costing ac on so.id_cost = ac.id
                inner join bom_jo_item k on jd.id_jo = k.id_jo
                inner join masteritem mi on k.id_item = mi.id_gen
            where
                jd.cancel = 'N' and k.cancel = 'N' and mi.Mattype = 'F' and ac.id = '".$request->act_costing_id."'
            group by
                id_cost, k.id_item
        ");

        return json_encode($items ? $items : null);
    }

    public function startProcess($id = 0, Request $request)
    {
        $updateFormCutInput = FormCutInput::where("id", $id)->update([
            "status_form" => "form",
            "waktu_mulai" => $request->startTime,
        ]);

        if ($updateFormCutInput) {
            return array(
                "status" => 200,
                "message" => "alright",
                "additional" => [],
            );
        }

        return array(
            "status" => 400,
            "message" => "nothing really matter anymore",
            "additional" => [],
        );
    }

    public function nextProcessOne($id = 0, Request $request)
    {
        $updateFormCutInput = FormCutInput::where("id", $id)->update([
            "status_form" => "form detail",
            "shell_form" => $request->shell
        ]);

        if ($updateFormCutInput) {
            return array(
                "status" => 200,
                "message" => "alright",
                "additional" => [],
            );
        }

        return array(
            "status" => 400,
            "message" => "nothing really matter anymore",
            "additional" => [],
        );
    }

    public function nextProcessTwo($id = 0, Request $request)
    {
        $validatedRequest = $request->validate([
            "p_act" => "required",
            "unit_p_act" => "required",
            "comma_act" => "required",
            "unit_comma_act" => "required",
            "l_act" => "required",
            "unit_l_act" => "required",
            "cons_act" => "required",
            "cons_piping" => "required",
            "cons_ampar" => "required",
            "unit_cons_ampar" => "required",
            "est_piping" => "required",
            "unit_est_piping" => "required",
            "est_kain" => "required",
            "unit_est_kain" => "required",
        ]);

        $updateFormCutInput = FormCutInput::where("id", $id)->update([
            "status_form" => "form spreading",
            "p_act" => $validatedRequest['p_act'],
            "unit_p_act" => $validatedRequest['unit_p_act'],
            "comma_p_act" => $validatedRequest['comma_act'],
            "unit_comma_p_act" => $validatedRequest['unit_comma_act'],
            "l_act" => $validatedRequest['l_act'],
            "unit_l_act" => $validatedRequest['unit_l_act'],
            "cons_act" => $validatedRequest['cons_act'],
            "cons_piping" => $validatedRequest['cons_piping'],
            "cons_ampar" => $validatedRequest['cons_ampar'],
            "unit_cons_ampar" => $validatedRequest['unit_cons_ampar'],
            "est_piping" => $validatedRequest['est_piping'],
            "unit_est_piping" => $validatedRequest['unit_est_piping'],
            "est_kain" => $validatedRequest['est_kain'],
            "unit_est_kain" => $validatedRequest['unit_est_kain']
        ]);

        if ($updateFormCutInput) {
            return array(
                "status" => 200,
                "message" => "alright",
                "additional" => [],
            );
        }

        return array(
            "status" => 400,
            "message" => "nothing really matter anymore",
            "additional" => [],
        );
    }

    public function getTimeRecord($noForm = 0)
    {
        $timeRecordSummary = FormCutInputDetail::where("no_form", $noForm)->where('status', '!=', 'not complete')->where('status', '!=', 'extension')->orderBy('id', 'asc')->get();

        return json_encode($timeRecordSummary);
    }

    public function storeTimeRecord(Request $request)
    {
        $validatedRequest = $request->validate([
            "no_form" => "required",
            "meja_id" => "required",
            "item_color" => "nullable",
            "item_detail" => "nullable",
            "current_item_id" => "required",
            "current_roll" => "nullable",
            "current_roll_id" => "nullable",
            "current_qty" => "required",
            "current_qty_roll" => "required",
            "current_unit" => "required",
            "current_group" => "required",
            "current_est_amparan" => "required",
            "current_sisa_gelaran" => "required",
            "current_lembar_gelaran" => "required",
            "current_waktu_average" => "required",
            "current_kepala_kain" => "required",
            "current_sisa_tidak_bisa" => "required",
            "current_reject" => "required",
            "current_sisa_kain" => "required",
            "current_total_pemakaian_roll" => "required",
            "current_short_roll" => "required",
            "current_piping" => "required",
            "current_remark" => "required",
            "current_sambungan" => "required",
            "p_act" => "required",
            "comma_act" => "required"
        ]);

        // Set Form Cut Detail Status
            $status = 'complete';
            if ($validatedRequest['current_sisa_gelaran'] > 0) {
                $status = 'need extension';
            }

        // Check Form Cut Detail Before for Stocker Grouping
            $formCutBefore = FormCutInputDetail::select('group', 'stocker_group')->where('no_form', $validatedRequest['no_form'])->whereRaw('(form_cut_input_detail.status = "complete" || form_cut_input_detail.status = "need extension" || form_cut_input_detail.status = "extension complete")')->orderBy('id', 'desc')->first();
            $stockerGroup = $formCutBefore ? ($formCutBefore->group == $validatedRequest['current_group'] ? $formCutBefore->stocker_group : $formCutBefore->stocker_group + 1) : 1;

        // Form Cut Detail Item Qty
            $itemQty = ($validatedRequest["current_unit"] != "KGM" ? floatval($validatedRequest['current_qty']) : floatval($validatedRequest['current_qty_roll']));
            $itemUnit = ($validatedRequest["current_unit"] != "KGM" ? "METER" : $validatedRequest['current_unit']);

        // Store Form Cut Detail Data
            $storeFormCutInputDetail = FormCutInputDetail::selectRaw("form_cut_input_detail.*")->
                where('form_cut_input_detail.status', 'not complete')->
                updateOrCreate(
                    [
                        "no_form" => $validatedRequest['no_form']
                    ],
                    [
                        "roll_id" => $validatedRequest['current_roll_id'],
                        "item_id" => $validatedRequest['current_item_id'],
                        "item_detail" => $validatedRequest['item_detail'],
                        "item_color" => $validatedRequest['item_color'],
                        "group" => $validatedRequest['current_group'],
                        "lot" => $request["current_lot"],
                        "roll" => $validatedRequest['current_roll'],
                        "qty" => $itemQty,
                        "unit" => $itemUnit,
                        "sisa_gelaran" => $validatedRequest['current_sisa_gelaran'],
                        "sambungan" => $validatedRequest['current_sambungan'],
                        "est_amparan" => $validatedRequest['current_est_amparan'],
                        "lembar_gelaran" => $validatedRequest['current_lembar_gelaran'],
                        "waktu_average" => $validatedRequest['current_waktu_average'],
                        "kepala_kain" => $validatedRequest['current_kepala_kain'],
                        "sisa_tidak_bisa" => $validatedRequest['current_sisa_tidak_bisa'],
                        "reject" => $validatedRequest['current_reject'],
                        "sisa_kain" => $validatedRequest['current_sisa_kain'],
                        "total_pemakaian_roll" => $validatedRequest['current_total_pemakaian_roll'],
                        "short_roll" => $validatedRequest['current_short_roll'],
                        "piping" => $validatedRequest['current_piping'],
                        "remark" => $validatedRequest['current_remark'],
                        "status" => $status,
                        "metode" => $request->metode ? $request->metode : ($validatedRequest['current_roll_id'] ? "scan" : "select"),
                        "stocker_group" => $stockerGroup,
                    ]
                );

        // When Store Form Cut Detail Data Success
            if ($storeFormCutInputDetail) {
                // Set Item Remaining Qty
                    // $itemRemain = $itemQty - floatval($validatedRequest['current_total_pemakaian_roll']) - floatval($validatedRequest['current_kepala_kain']) - floatval($validatedRequest['current_sisa_tidak_bisa']) - floatval($validatedRequest['current_reject']) - floatval($validatedRequest['current_piping']);
                    $itemRemain = $validatedRequest['current_sisa_kain'];

                // Save Scanned Item
                    ScannedItem::updateOrCreate(
                        [
                            "roll_id" => $validatedRequest['current_roll_id'],
                            "item_id" => $validatedRequest['current_item_id']
                        ],
                        [
                            "item_detail" => $validatedRequest['item_detail'],
                            "item_color" => $validatedRequest['item_color'],
                            "lot" => $request['current_lot'],
                            "roll" => $validatedRequest['current_roll'],
                            "qty" => $status == 'need extension' ? ($itemRemain > 0 ? 0 : $itemRemain) : $itemRemain,
                            "unit" => $itemUnit,
                        ]
                    );

                // When Form Cut Detail Status is Need Extension
                    if ($status == 'need extension') {
                        // Store Form Cut Detail Extension
                            $storeFormCutInputDetailExt = FormCutInputDetail::create([
                                "sambungan_id" => $storeFormCutInputDetail->id,
                                "no_form" => $validatedRequest['no_form'],
                                "group" => $validatedRequest['current_group'],
                                "status" => "extension",
                                "stocker_group" => $stockerGroup
                            ]);

                            if ($storeFormCutInputDetailExt) {
                                return array(
                                    "status" => 200,
                                    "message" => "alright",
                                    "additional" => [
                                        $storeFormCutInputDetail,
                                        $storeFormCutInputDetailExt
                                    ],
                                );
                            }
                    }

                return array(
                    "status" => 200,
                    "message" => "alright",
                    "additional" => [
                        $storeFormCutInputDetail
                    ],
                );
            }

        return array(
            "status" => 400,
            "message" => "nothing really matter anymore",
            "additional" => [],
        );
    }

    public function storeThisTimeRecord(Request $request)
    {
        // Time Record Lapping
            $lap = $request->lap;

        // Form Cut Detail Item Qty
            $itemQty = ($request["current_unit"] != "KGM" ? floatval($request['current_qty']) : floatval($request['current_qty_roll']));
            $itemUnit = ($request["current_unit"] != "KGM" ? "METER" : $request['current_unit']);

        // Store Form Cut Detail Data
            $storeFormCutInputDetail = FormCutInputDetail::selectRaw("form_cut_input_detail.*")->
                where('form_cut_input_detail.status', 'not complete')->
                updateOrCreate(
                    [
                        "no_form" => $request->no_form
                    ],
                    [
                        "roll_id" => $request->current_roll_id,
                        "item_id" => $request->current_item_id,
                        "item_detail" => $request->item_detail,
                        "item_color" => $request->item_color,
                        "group" => $request->current_group,
                        "lot" => $request->current_lot,
                        "roll" => $request->current_roll,
                        "qty" => $itemQty,
                        "unit" => $itemUnit,
                        "sisa_gelaran" => $request->current_sisa_gelaran,
                        "sambungan" => $request->current_sambungan,
                        "est_amparan" => $request->current_est_amparan,
                        "lembar_gelaran" => $request->current_lembar_gelaran,
                        "waktu_average" => $request->current_waktu_average,
                        "kepala_kain" => $request->current_kepala_kain,
                        "sisa_tidak_bisa" => $request->current_sisa_tidak_bisa,
                        "reject" => $request->current_reject,
                        "sisa_kain" => $request->current_sisa_kain,
                        "total_pemakaian_roll" => $request->current_total_pemakaian_roll,
                        "short_roll" => $request->current_short_roll,
                        "piping" => $request->current_piping,
                        "remark" => $request->current_remark,
                        "status" => "not complete",
                        "metode" => $request->metode ? $request->metode : ($validatedRequest['current_roll_id'] ? "scan" : "select"),
                    ]
                );

        // When Store Form Cut Detail Data Success
            if ($storeFormCutInputDetail) {
                // When Time Record Lap Added
                    if ($lap > 0) {
                        // Save Time Record Lap
                            $storeTimeRecordLap = FormCutInputDetailLap::updateOrCreate(
                                [
                                    "form_cut_input_detail_id" => $storeFormCutInputDetail->id,
                                    "lembar_gelaran_ke" => $lap
                                ],
                                [
                                    "waktu" => $request["time_record"][$lap]
                                ]
                            );

                            if ($storeTimeRecordLap) {
                                return array(
                                    "status" => 200,
                                    "message" => "alright",
                                    "additional" => [],
                                );
                            }
                    }

                return array(
                    "status" => 200,
                    "message" => "alright",
                    "additional" => [],
                );
            }

        return array(
            "status" => 400,
            "message" => "nothing really matter anymore",
            "additional" => [],
        );
    }

    public function storeTimeRecordExtension(Request $request)
    {
        $validatedRequest = $request->validate([
            "sambungan_status" => "required",
            "sambungan_id" => "required",
            "no_form" => "required",
            "meja_id" => "required",
            "item_detail" => "required",
            "item_color" => "nullable",
            "current_item_id" => "required",
            "current_roll" => "nullable",
            "current_roll_id" => "nullable",
            "current_qty" => "required",
            "current_qty_roll" => "required",
            "current_unit" => "required",
            "current_group" => "required",
            "current_sisa_gelaran" => "required",
            "current_est_amparan" => "required",
            "current_lembar_gelaran" => "required",
            "current_waktu_average" => "required",
            "current_kepala_kain" => "required",
            "current_sisa_tidak_bisa" => "required",
            "current_reject" => "required",
            "current_sisa_kain" => "nullable",
            "current_total_pemakaian_roll" => "required",
            "current_short_roll" => "required",
            "current_piping" => "required",
            "current_remark" => "required",
            "current_sambungan" => "required"
        ]);

        // Check Form Cut Detail Before for Stocker Grouping
            $formCutBefore = FormCutInputDetail::select('group', 'stocker_group')->where('no_form', $validatedRequest['no_form'])->whereRaw('(form_cut_input_detail.status = "complete" || form_cut_input_detail.status = "need extension" || form_cut_input_detail.status = "extension complete")')->orderBy('id', 'desc')->first();
            $stockerGroup = $formCutBefore ? ($formCutBefore->group == $validatedRequest['current_group'] ? $formCutBefore->stocker_group : $formCutBefore->stocker_group + 1) : 1;

        // Form Cut Detail Item Qty
            $itemQty = ($validatedRequest["current_unit"] != "KGM" ? floatval($validatedRequest['current_qty']) : floatval($validatedRequest['current_qty_roll']));
            $itemUnit = ($validatedRequest["current_unit"] != "KGM" ? "METER" : $validatedRequest['current_unit']);

        // Store Form Cut Detail Data
            $storeFormCutInputDetail = FormCutInputDetail::selectRaw("form_cut_input_detail.*")->
                where('status', 'extension')->
                updateOrCreate(
                    [
                        'no_form' => $validatedRequest['no_form']
                    ],
                    [
                        "roll_id" => $validatedRequest['current_roll_id'],
                        "item_id" => $validatedRequest['current_item_id'],
                        "item_color" => $validatedRequest['item_color'],
                        "item_detail" => $validatedRequest['item_detail'],
                        "group" => $validatedRequest['current_group'],
                        "lot" => $request['current_lot'],
                        "roll" => $validatedRequest['current_roll'],
                        "qty" => $itemQty,
                        "unit" => $itemUnit,
                        "sisa_gelaran" => $validatedRequest['current_sisa_gelaran'],
                        "sambungan" => $validatedRequest['current_sambungan'],
                        "est_amparan" => $validatedRequest['current_est_amparan'],
                        "lembar_gelaran" => $validatedRequest['current_lembar_gelaran'],
                        "waktu_average" => $validatedRequest['current_waktu_average'],
                        "kepala_kain" => $validatedRequest['current_kepala_kain'],
                        "sisa_tidak_bisa" => $validatedRequest['current_sisa_tidak_bisa'],
                        "reject" => $validatedRequest['current_reject'],
                        "sisa_kain" => ($validatedRequest['current_sisa_kain'] ? $validatedRequest['current_sisa_kain'] : 0),
                        "total_pemakaian_roll" => $validatedRequest['current_total_pemakaian_roll'],
                        "short_roll" => $validatedRequest['current_short_roll'],
                        "piping" => $validatedRequest['current_piping'],
                        "remark" => $validatedRequest['current_remark'],
                        "status" => "extension complete",
                        "metode" => $request->metode ? $request->metode : ($validatedRequest['current_roll_id'] ? "scan" : "select"),
                        "stocker_group" => $stockerGroup,
                    ]
                );

        // When Store Form Cut Success
            if ($storeFormCutInputDetail) {
                // Set Item Remaining Qty
                    // $itemRemain = $validatedRequest['current_sisa_kain'];
                    $itemRemain = $itemQty - floatval($validatedRequest['current_total_pemakaian_roll']) - floatval($validatedRequest['current_kepala_kain']) - floatval($validatedRequest['current_sisa_tidak_bisa']) - floatval($validatedRequest['current_reject']) - floatval($validatedRequest['current_piping']);

                // Save Scanned Item
                    ScannedItem::updateOrCreate(
                        [
                            "roll_id" => $validatedRequest['current_roll_id'],
                            "item_id" => $validatedRequest['current_item_id']
                        ],
                        [
                            "item_detail" => $validatedRequest['item_detail'],
                            "item_color" => $validatedRequest['item_color'],
                            "lot" => $request['current_lot'],
                            "roll" => $validatedRequest['current_roll'],
                            "qty" => $itemRemain,
                            "unit" => $itemUnit,
                        ]
                    );

                // When Time Record Lap Added
                    // Save Time Record Lap
                        $storeTimeRecordLap = FormCutInputDetailLap::updateOrCreate(
                            [
                                "form_cut_input_detail_id" => $storeFormCutInputDetail->id,
                                "lembar_gelaran_ke" => 1
                            ],
                            [
                                "waktu" => $request["time_record"][1]
                            ]
                        );

                    // When Save Time Record Lap Success
                        if ($storeTimeRecordLap) {
                            // Store Form Cut Detail Next After Extension
                                $storeFormCutInputDetailNext = FormCutInputDetail::create([
                                    "no_form" => $validatedRequest['no_form'],
                                    "roll_id" => $validatedRequest['current_roll_id'],
                                    "item_id" => $validatedRequest['current_item_id'],
                                    "item_color" => $validatedRequest['item_color'],
                                    "item_detail" => $validatedRequest['item_detail'],
                                    "group" => $validatedRequest['current_group'],
                                    "lot" => $request['current_lot'],
                                    "roll" => $validatedRequest['current_roll'],
                                    "qty" => $itemRemain,
                                    "unit" => $itemUnit,
                                    "sambungan" => 0,
                                    "status" => "not complete",
                                    "metode" => $request->metode ? $request->metode : ($validatedRequest['current_roll_id'] ? "scan" : "select"),
                                ]);

                                if ($storeFormCutInputDetailNext) {
                                    return array(
                                        "status" => 200,
                                        "message" => "alright",
                                        "additional" => [
                                            $storeFormCutInputDetail,
                                            $storeFormCutInputDetailNext,
                                        ],
                                    );
                                }
                        }

                return array(
                    "status" => 200,
                    "message" => "alright",
                    "additional" => [
                        $storeFormCutInputDetail
                    ],
                );
            }

        return array(
            "status" => 400,
            "message" => "nothing really matter anymore",
            "additional" => [],
        );
    }

    public function checkSpreadingForm($noForm = 0, $mejaId = 0, Request $request)
    {
        // Get On Process Form Cut Detail
            $formCutInputDetailData = FormCutInputDetail::selectRaw('form_cut_input_detail.*')->
                leftJoin('form_cut_input', 'form_cut_input.no_form', '=', 'form_cut_input_detail.no_form')->
                where('form_cut_input.no_form', $noForm)->
                where('form_cut_input.meja_id', $mejaId)->
                orderBy('form_cut_input_detail.id', 'desc')->
                first();

        // Count On Process Form Cut Detail
            $formCutInputDetailCount = $formCutInputDetailData ? $formCutInputDetailData->count() : 0;

        // When On Process Form Cut Detail is Exist
            if ($formCutInputDetailCount > 0) {

                // When The On Process Form Cut Detail is 'Extension'
                    if ($formCutInputDetailData->status == 'extension') {
                        $thisFormCutInputDetail = FormCutInputDetail::select("sisa_gelaran", "unit")->where('id', $formCutInputDetailData->sambungan_id)->first();

                        return array(
                            "count" => $formCutInputDetailCount,
                            "data" => $formCutInputDetailData,
                            "sisaGelaran" => $thisFormCutInputDetail->sisa_gelaran,
                            "unitSisaGelaran" => $thisFormCutInputDetail->unit,
                        );
                // When The On Process Form Cut Detail is 'Not Complete'
                    } else if ($formCutInputDetailData->status == 'not complete') {
                        return array(
                            "count" => $formCutInputDetailCount,
                            "data" => $formCutInputDetailData,
                            "sisaGelaran" => null,
                            "unitSisaGelaran" => null,
                        );
                    }
            }

        return array(
            "count" => null,
            "data" => null
        );
    }

    public function checkTimeRecordLap($detailId = 0)
    {
        // Get On Process Form Cut Detail's Time Record Lap
            $formCutInputDetailLapData = FormCutInputDetailLap::where('form_cut_input_detail_id', $detailId)->get();

            return array(
                "count" => $formCutInputDetailLapData->count(),
                "data" => $formCutInputDetailLapData,
            );
    }

    public function storeLostTime(Request $request, $noForm = 0)
    {
        $currentLostTime = $request["current_lost_time"];

        $storeTimeRecordLap = FormCutInputLostTime::updateOrCreate(
            ["no_form" => $noForm, "lost_time_ke" => $request["current_lost_time"]],
            [
                "lost_time_ke" => $request["current_lost_time"],
                "waktu" => $request["lost_time"][$currentLostTime],
            ]
        );
    }

    public function checkLostTime($noForm = 0)
    {
        $formCutInputLostTimeData = FormCutInputLostTime::where('no_form', $noForm)->get();

        return array(
            "count" => $formCutInputLostTimeData->count(),
            "data" => $formCutInputLostTimeData,
        );
    }

    public function finishProcess($id = 0, Request $request)
    {
        // Get This Form Cut
            $formCutInputData = FormCutInput::where("id", $id)->first();

        // Count Similar Form Cut for No. Cut
            $formCutInputSimilarCount = FormCutInput::leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.marker_input_kode")->
                where("marker_input.act_costing_ws", $formCutInputData->marker->act_costing_ws)->
                where("marker_input.color", $formCutInputData->marker->color)->
                where("marker_input.panel", $formCutInputData->marker->panel)->
                where("form_cut_input.status_form", "finish")->
                count();

        // Finish Form Cut Process
            $updateFormCutInput = FormCutInput::where("id", $id)->update([
                "status_form" => "finish",
                "no_cut_form" => $formCutInputSimilarCount + 1,
                "waktu_selesai" => $request->finishTime,
                "cons_act" => $request->consAct,
                "unit_cons_act" => $request->unitConsAct,
                "cons_act_nosr" => $request->consActNoSr,
                "unit_cons_act_nosr" => $request->unitConsActNoSr,
                "total_ply" => $request->totalPly,
                "cons_ws_uprate" => $request->consWsUprate,
                "cons_marker_uprate" => $request->consMarkerUprate,
                "cons_ws_uprate_nosr" => $request->consWsUprateNoSr,
                "cons_marker_uprate_nosr" => $request->consMarkerUprateNoSr,
                "operator" => $request->operator,
            ]);

        // Delete Incomplete Form Cut Detail
            $incompleteFormCutDetail = FormCutInputDetail::where("no_form", $formCutInputData->no_form)->where("status", "not complete")->first();
            if ($incompleteFormCutDetail) {
                FormCutInputDetailLap::where("form_cut_input_detail_id", $incompleteFormCutDetail->id)->delete();
                FormCutInputDetail::where("no_form", $formCutInputData->no_form)->where("status", "not complete")->delete();
            }

        // Get Similar Part
            $partData = Part::select('id', 'kode')->
                where("act_costing_id", $formCutInputData->marker->act_costing_id)->
                where("act_costing_ws", $formCutInputData->marker->act_costing_ws)->
                where("panel", $formCutInputData->marker->panel)->
                where("buyer", $formCutInputData->marker->buyer)->
                where("style", $formCutInputData->marker->style)->
                first();

        if ($partData) {
            $lastPartForm = PartForm::select("kode")->orderBy("kode", "desc")->first();
            $urutanPartForm = $lastPartForm ? intval(substr($lastPartForm->kode, -5)) + 1 : 1;
            $kodePartForm = "PFM" . sprintf('%05s', $urutanPartForm);

            // Store Form to Part Form
            $addToPartForm = PartForm::create([
                "kode" => $kodePartForm,
                "part_kode" => $partData->kode,
                "no_form" => $formCutInputData->no_form,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]);
        }

        return $updateFormCutInput;
    }

    public function updateNoCut(Request $request) {
        $updatedForm = [];

        $markerGroups = Marker::select("act_costing_ws", "color", "panel")->groupBy("act_costing_ws", "color", "panel")->get();

        foreach ($markerGroups as $markerGroup) {
            $i = 0;

            $formCuts = FormCutInput::selectRaw("form_cut_input.id as id, form_cut_input.no_form, form_cut_input.status")->
                leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.id_marker")->
                where("marker_input.act_costing_ws", $markerGroup->act_costing_ws)->
                where("marker_input.color", $markerGroup->color)->
                where("marker_input.panel", $markerGroup->panel)->
                where("form_cut_input.status", "finish")->
                orderBy("form_cut_input.waktu_selesai", "asc")->
                get();

            foreach ($formCuts as $formCut) {
                $i++;

                $updateFormCut = FormCutInput::where("id", $formCut->id)->
                    update([
                        "no_cut" => $i
                    ]);

                if ($updateFormCut) {
                    array_push($updatedForm, ["ws_no_form" => $markerGroup->act_costing_ws."-".$markerGroup->color."-".$markerGroup->panel."-".$formCut->no_form."-".$formCut->status_form."-".$i]);
                }
            }
        }

        return $updatedForm;
    }
}
