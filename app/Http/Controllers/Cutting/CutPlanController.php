<?php

namespace App\Http\Controllers\Cutting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\CutPlan;
use App\Models\FormCutInput;

use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use DB;

class CutPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cuttingPlans = CutPlan::selectRaw("
                    cutting_plan.id,
                    cutting_plan.tanggal,
                    cutting_plan.kode,
                    COUNT(form_cut_input.no_form) total_form,
                    COUNT(IF(form_cut_input.status_form='idle', 1, null)) total_idle,
                    COUNT(IF(form_cut_input.status_form='marker' OR form_cut_input.status_form ='form' OR form_cut_input.status_form ='form detail' OR form_cut_input.status_form ='form spreading' OR form_cut_input.status_form ='pilot marker' OR form_cut_input.status_form ='pilot form detail', 1, null)) total_on_progress,
                    COUNT(IF(form_cut_input.status_form='finish', 1, null)) total_finish
                ")
                ->leftJoin('form_cut_input', 'cutting_plan.no_form', '=', 'form_cut_input.no_form')
                ->groupBy("tanggal", "kode")
                ->orderBy('tanggal', 'desc');

            return DataTables::eloquent($cuttingPlans)->filter(function ($query) {
                if (request('tanggal_awal')) {
                    $query->whereRaw("tanggal >= '" . request('tanggal_awal') . "'");
                }

                if (request('tanggal_akhir')) {
                    $query->whereRaw("tanggal <= '" . request('tanggal_akhir') . "'");
                }
            }, true)->
            filterColumn('kode', function ($query, $keyword) {
                $query->whereRaw("LOWER(cutting_plan.kode) LIKE LOWER('%" . $keyword . "%')");
            })->
            filterColumn('tanggal', function ($query, $keyword) {
                $query->whereRaw("LOWER(cutting_plan.tanggal) LIKE LOWER('%" . $keyword . "%')");
            })->
            order(function ($query) {
                $query->orderBy('cutting_plan.updated_at', 'desc');
            })->
            toJson();
        }

        return view('cutting.cut-plan.cut-plan', ["page" => "dashboard-cutting", "subPageGroup" => "cuttingplan-cutting", "subPage" => "cut-plan"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $additionalQuery = "";

            $thisStoredCutPlan = CutPlan::select("no_form")->groupBy("no_form")->get();

            if ($thisStoredCutPlan->count() > 0) {
                $i = 0;
                $additionalQuery .= " AND a.no_form NOT IN (";
                foreach ($thisStoredCutPlan as $cutPlan) {
                    if ($i+1 == count($thisStoredCutPlan)) {
                        $additionalQuery .= "'".$cutPlan->no_form . "' ";
                    } else {
                        $additionalQuery .= "'".$cutPlan->no_form . "' , ";
                    }

                    $i++;
                }
                $additionalQuery .= ") ";
            }

            $keywordQuery = "";
            if ($request->search["value"]) {
                $keywordQuery = "
                    and (
                        a.tanggal like '%" . $request->search["value"] . "%' OR
                        a.no_form like '%" . $request->search["value"] . "%' OR
                        a.status_form like '%" . $request->search["value"] . "%' OR
                        a.marker_input_kode like '%" . $request->search["value"] . "%' OR
                        b.act_costing_ws like '%" . $request->search["value"] . "%' OR
                        b.panel like '%" . $request->search["value"] . "%' OR
                        b.color like '%" . $request->search["value"] . "%' OR
                        users.name like '%" . $request->search["value"] . "%'
                    )
                ";
            }

            $spreadingForms = DB::select("
                SELECT
                    a.id form_id,
                    b.id marker_id,
                    a.tanggal,
                    a.no_form,
                    a.status_form,
                    a.meja_id,
                    a.marker_input_kode,
                    b.act_costing_ws,
                    b.style,
                    b.color,
                    b.panel,
                    UPPER(users.name) meja,
                    b.panjang_marker,
                    UPPER(b.unit_panjang_marker) unit_panjang_marker,
                    b.comma_marker,
                    UPPER(b.unit_comma_marker) unit_comma_marker,
                    b.lebar_marker,
                    UPPER(b.unit_lebar_marker) unit_lebar_marker,
                    a.qty_ply,
                    b.gelar_qty_marker,
                    b.po_marker,
                    b.urutan_marker,
                    b.cons_marker,
                    a.tipe_form,
                    CONCAT(b.panel, ' - ', b.urutan_marker) panel,
                    GROUP_CONCAT(DISTINCT CONCAT(marker_input_detail.size, '(', marker_input_detail.ratio, ')') ORDER BY master_size_new.urutan ASC SEPARATOR ', ') marker_details
                FROM `form_cut_input` a
                    left join marker_input b on a.marker_input_kode = b.kode
                    left join marker_input_detail on b.kode = marker_input_detail.marker_input_kode
                    left join master_size_new on marker_input_detail.size = master_size_new.size
                    left join users on users.id = a.meja_id
                WHERE
                    a.status_form = 'idle' and
                    b.cancel = 'N'
                    " . $additionalQuery . "
                    " . $keywordQuery . "
                GROUP BY
                    a.id
                ORDER BY
                    b.cancel asc, a.tanggal desc, a.no_form desc
            ");

            return DataTables::of($spreadingForms)->toJson();
        }

        return view('cutting.cut-plan.create-cut-plan', ["page" => "dashboard-cutting", "subPageGroup" => "cuttingplan-cutting", "subPage" => "cut-plan"]);
    }

    public function getSelectedForm(Request $request, $noCutPlan = 0)
    {
        $additionalQuery = "";

        $thisStoredCutPlan = CutPlan::select("no_form")->where("tanggal", $request->tanggal)->get();

        if ($thisStoredCutPlan->count() > 0) {
            $additionalQuery .= " and (";

            $i = 0;
            $length = $thisStoredCutPlan->count();
            foreach ($thisStoredCutPlan as $cutPlan) {
                if ($i == 0) {
                    $additionalQuery .= " a.no_form = '" . $cutPlan->no_form . "' ";
                } else {
                    $additionalQuery .= " or a.no_form = '" . $cutPlan->no_form . "' ";
                }

                $i++;
            }

            $additionalQuery .= " ) ";
        } else {
            $additionalQuery .= " and a.no_form = '0' ";
        }

        $keywordQuery = "";
        if ($request->search["value"]) {
            $keywordQuery = "
                    and (
                        a.tanggal like '%" . $request->search["value"] . "%' OR
                        a.no_form like '%" . $request->search["value"] . "%' OR
                        a.status_form like '%" . $request->search["value"] . "%' OR
                        a.meja_id like '%" . $request->search["value"] . "%' OR
                        a.marker_input_kode like '%" . $request->search["value"] . "%' OR
                        b.act_costing_ws like '%" . $request->search["value"] . "%' OR
                        b.panel like '%" . $request->search["value"] . "%' OR
                        b.color like '%" . $request->search["value"] . "%' OR
                        users.name like '%" . $request->search["value"] . "%'
                    )
                ";
        }

        $spreadingForms = DB::select("
                SELECT
                    a.id form_id,
                    b.id marker_id,
                    a.meja_id,
                    a.tanggal,
                    a.no_form,
                    a.status_form,
                    a.marker_input_kode,
                    UPPER(users.name) meja,
                    b.act_costing_ws,
                    b.style,
                    b.panel,
                    b.color,
                    b.panjang_marker,
                    UPPER(b.unit_panjang_marker) unit_panjang_marker,
                    b.comma_marker,
                    UPPER(b.unit_comma_marker) unit_comma_marker,
                    b.lebar_marker,
                    UPPER(b.unit_lebar_marker) unit_lebar_marker,
                    b.gelar_qty_marker,
                    b.po_marker,
                    b.urutan_marker,
                    b.cons_marker,
                    a.tipe_form,
                    CONCAT(b.panel, ' - ', b.urutan_marker) panel,
                    GROUP_CONCAT(DISTINCT CONCAT(marker_input_detail.size, '(', marker_input_detail.ratio, ')') ORDER BY master_size_new.urutan ASC SEPARATOR ', ') marker_details,
                    SUM(marker_input_detail.ratio) * a.qty_ply qty_output,
                    COALESCE(SUM(marker_input_detail.ratio) * c.total_lembar_gelaran, 0) qty_act,
                    COALESCE(a.total_ply, 0) total_ply,
                    COALESCE(a.qty_ply, 0) qty_ply
                FROM `form_cut_input` a
                    left join marker_input b on a.marker_input_kode = b.kode
                    left join marker_input_detail on b.kode = marker_input_detail.marker_input_kode
                    left join master_size_new on marker_input_detail.size = master_size_new.size
                    left join users on users.id = a.meja_id
                    left join (select no_form, sum(lembar_gelaran) total_lembar_gelaran from form_cut_input_detail group by no_form) c on a.no_form = c.no_form
                WHERE
                    a.id is not null
                    " . $additionalQuery . "
                    " . $keywordQuery . "
                GROUP BY
                    a.id
                ORDER BY
                    b.cancel desc, FIELD(a.status_form, 'form', 'marker', 'form detail', 'form spreading', 'idle', 'finish'), a.tanggal desc, panel asc
            ");

        return DataTables::of($spreadingForms)->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dateFormat = date("dmY", strtotime($request->tanggal));
        $kodeCutPlan = "CP-" . $dateFormat;

        $success = [];
        $fail = [];
        $exist = [];

        foreach ($request->formCutPlan as $req) {
            $isExist = CutPlan::where("tanggal", $request->tanggal)->where("no_form", $req['no_form'])->count();

            if ($isExist < 1) {
                $addToCutPlan = CutPlan::create([
                    "kode" => $kodeCutPlan,
                    "tanggal" => $request->tanggal,
                    "no_form" => $req['no_form']
                ]);

                if ($addToCutPlan) {
                    array_push($success, ['no_form' => $req['no_form']]);
                } else {
                    array_push($fail, ['no_form' => $req['no_form']]);
                }
            } else {
                array_push($exist, ['no_form' => $req['no_form']]);
            }
        }

        if (count($success) > 0) {
            return array(
                'status' => 200,
                'message' => 'Cut Plan berhasil ditambahkan',
                'redirect' => '',
                'table' => 'datatable-selected',
                'additional' => ["success" => $success, "fail" => $fail, "exist" => $exist],
            );
        } else {
            return array(
                'status' => 400,
                'message' => 'Data tidak ditemukan',
                'redirect' => '',
                'table' => 'datatable-selected',
                'additional' => ["success" => $success, "fail" => $fail, "exist" => $exist],
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = 0)
    {
        $now = Carbon::now();

        $success = [];
        $fail = [];
        $exist = [];

        $approvedBy = Auth::user()->id;
        $approvedAt = $now;

        if (count($request['no_form']) > 0) {
            foreach ($request['no_form'] as $noFormId => $noFormVal) {
                $updateCutPlan = CutPlan::where('kode', $request['manage_kode'])->
                where('no_form', $request['no_form'][$noFormId])->
                update([
                    'app' => $request['approve'] ? ((array_key_exists($noFormId, $request['approve'])) ? $request['approve'][$noFormId] : 'N') : 'N',
                    'app_by' => $request['approve'] ? ((array_key_exists($noFormId, $request['approve'])) ? $approvedBy : null) : 'N',
                    'app_at' => $request['approve'] ? ((array_key_exists($noFormId, $request['approve'])) ? $approvedAt : null) : 'N',
                ]);

                $updateForm = FormCutInput::where('no_form', $request['no_form'][$noFormId])->update([
                    'meja_id' => (array_key_exists($noFormId, $request['meja_id'])) ? $request['meja_id'][$noFormId] : null,
                    'app' => $request['approve'] ? ((array_key_exists($noFormId, $request['approve'])) ? $request['approve'][$noFormId] : 'N') : 'N',
                    'app_by' => $request['approve'] ? ((array_key_exists($noFormId, $request['approve'])) ? $approvedBy : null) : 'N',
                    'app_at' => $request['approve'] ? ((array_key_exists($noFormId, $request['approve'])) ? $approvedAt : null) : 'N',
                ]);

                if ($updateCutPlan && $updateForm) {
                    array_push($success, $noFormVal);
                } else {
                    array_push($fail, $noFormVal);
                }
            }

            return array(
                'status' => 200,
                'message' => 'Form berhasil diubah',
                'redirect' => '',
                'additional' => ["success" => $success, "fail" => $fail, "exist" => $exist],
            );
        }

        return array(
            'status' => 400,
            'message' => 'Data tidak ditemukan',
            'redirect' => '',
            'additional' => ["success" => $success, "fail" => $fail, "exist" => $exist],
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $success = [];
        $fail = [];

        foreach ($request->formCutPlan as $req) {
            $isExist = CutPlan::where("tanggal", $request->tanggal)->where("no_form", $req['no_form'])->count();

            if ($isExist > 0) {
                $removeCutPlan = CutPlan::where("tanggal", $request->tanggal)->where("no_form", $req['no_form'])->delete();

                if ($removeCutPlan) {
                    array_push($success, ['no_form' => $req['no_form']]);
                } else {
                    array_push($fail, ['no_form' => $req['no_form']]);
                }
            } else {
                array_push($exist, ['no_form' => $req['no_form']]);
            }
        }

        if (count($success) > 0) {
            return array(
                'status' => 200,
                'message' => 'Cut Plan berhasil disingkirkan',
                'redirect' => '',
                'table' => 'datatable-selected',
                'additional' => ["success" => $success, "fail" => $fail],
            );
        } else {
            return array(
                'status' => 400,
                'message' => 'Data tidak ditemukan',
                'redirect' => '',
                'table' => 'datatable-selected',
                'additional' => ["success" => $success, "fail" => $fail],
            );
        }
    }

    public function getCutPlanForm(Request $request)
    {
        if ($request->ajax()) {
            $additionalQuery = "";

            $cutPlanForm = CutPlan::with('formCutInput')->where("kode", $request->kode)->groupBy("no_form");

            return DataTables::eloquent($cutPlanForm)->filter(function ($query) {
                $formInfoFilter = request('form_info_filter');
                $markerInfoFilter = request('marker_info_filter');
                $mejaFilter = request('meja_filter');
                $approveFilter = request('approve_filter');

                if (request('tanggal_awal')) {
                    $query->whereRaw("tanggal >= '" . request('tanggal_awal') . "'");
                }

                if (request('tanggal_akhir')) {
                    $query->whereRaw("tanggal <= '" . request('tanggal_akhir') . "'");
                }

                if ($formInfoFilter) {
                    $query->whereHas('formCutInput', function ($query) use ($formInfoFilter) {
                        $query->whereRaw("(
                                LOWER(form_cut_input.tanggal) LIKE LOWER('%" . $formInfoFilter . "%') OR
                                LOWER(form_cut_input.no_form) LIKE LOWER('%" . $formInfoFilter . "%') OR
                                LOWER(form_cut_input.tipe_form) LIKE LOWER('%" . $formInfoFilter . "%') OR
                                LOWER(form_cut_input.status_form) LIKE LOWER('%" . $formInfoFilter . "%') OR
                                LOWER(form_cut_input.qty_ply) LIKE LOWER('%" . $formInfoFilter . "%')
                            )");
                    });
                }

                if ($markerInfoFilter) {
                    $query->whereHas('formCutInput', function ($query) use ($markerInfoFilter) {
                        $query->whereHas('marker', function ($query) use ($markerInfoFilter) {
                            $query->whereRaw("(
                                    LOWER(marker_input.kode) LIKE LOWER('%" . $markerInfoFilter . "%') OR
                                    LOWER(marker_input.buyer) LIKE LOWER('%" . $markerInfoFilter . "%') OR
                                    LOWER(marker_input.act_costing_ws) LIKE LOWER('%" . $markerInfoFilter . "%') OR
                                    LOWER(marker_input.style) LIKE LOWER('%" . $markerInfoFilter . "%') OR
                                    LOWER(marker_input.color) LIKE LOWER('%" . $markerInfoFilter . "%') OR
                                    LOWER(marker_input.panel) LIKE LOWER('%" . $markerInfoFilter . "%')
                                )");
                        });
                    });
                }

                if ($mejaFilter) {
                    $query->whereHas('formCutInput', function ($query) use ($mejaFilter) {
                        $query->whereHas('alokasiMeja', function ($query) use ($mejaFilter) {
                            $query->whereRaw("(
                                    LOWER(users.name) LIKE LOWER('%" . $mejaFilter . "%') OR
                                    LOWER(users.username) LIKE LOWER('%" . $mejaFilter . "%')
                                )");
                        });
                    });
                }

                if ($approveFilter) {
                    $query->whereRaw("app = '" . $approveFilter . "'");
                }
            })->addIndexColumn()->addColumn('form_info', function ($row) {
                $totalPly = ($row->formCutInput ? $row->formCutInput->total_ply : 0);
                $qtyPly = ($row->formCutInput ? $row->formCutInput->qty_ply : 0);

                $formInfo = "<ul class='list-group'>";
                $formInfo = $formInfo . "<li class='list-group-item'>Tanggal Form :<br><b>" . ($row->formCutInput ? $row->formCutInput->tanggal : '-') . "</b></li>";
                $formInfo = $formInfo . "<li class='list-group-item'>No. Form :<br><b>" . $row->no_form . "</b></li>";
                $formInfo = $formInfo . "<li class='list-group-item'>Qty Ply :<br><b>".'<div class="progress border border-sb position-relative my-1" style="min-width: 50px;height: 21px"><p class="position-absolute" style="top: 50%;left: 50%;transform: translate(-50%, -50%);">'.($totalPly ? $totalPly : 0).'/'.($qtyPly ? $qtyPly : 0).'</p><div class="progress-bar" style="background-color: #75baeb;width: '.((($totalPly ? $totalPly : 0)/($qtyPly ? $qtyPly : 1))*100).'%" role="progressbar"></div></div>' . "</b></li>";
                $formInfo = $formInfo . "<li class='list-group-item'>Status :<br><b>" . ($row->formCutInput ? $row->formCutInput->status_form : '-') . "</b></li>";
                $formInfo = $formInfo . "</ul>";

                return $formInfo;
            })->addColumn('marker_info', function ($row) {
                $markerData = $row->formCutInput ? $row->formCutInput->marker : null;

                $markerInfo = "<ul class='list-group'>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Kode Marker :<br><b>" . ($markerData ? $markerData->kode : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>WS Number :<br><b>" . ($markerData ? $markerData->act_costing_ws : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Buyer :<br><b>" . ($markerData ? $markerData->buyer : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Style :<br><b>" . ($markerData ? $markerData->style : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Color :<br><b>" . ($markerData ? $markerData->color : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Panel :<br><b>" . ($markerData ? $markerData->panel . ' - ' . $markerData->urutan_marker : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Tipe Marker :<br><b>" . ($markerData ? strtoupper($markerData->tipe_marker) : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>PO :<br><b>" . ($markerData ? ($markerData->po_marker ? $markerData->po_marker : '-') : '-') . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Keterangan :<br><b>" . ($markerData ? ($markerData->notes ? $markerData->notes : '-') : '-') . "</b></li>";
                $markerInfo = $markerInfo . "</ul>";

                return $markerInfo;
            })->addColumn('marker_detail_info', function ($row) {
                $markerData = $row->formCutInput ? $row->formCutInput->marker : null;

                $markerInfo = "<ul class='list-group'>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Panjang : <br><b>" . ($markerData ? $markerData->panjang_marker . " " . $markerData->unit_panjang_marker . " " . $markerData->comma_marker . " " . $markerData->unit_comma_marker : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Lebar : <br><b>" . ($markerData ? $markerData->lebar_marker . " " . $markerData->unit_lebar_marker : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Gelar Qty : <br> <b>" . ($markerData ? $markerData->gelar_qty_marker : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Urutan : <br><b>" . ($markerData ? $markerData->urutan_marker : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Cons WS : <br><b>" . ($markerData ? $markerData->cons_ws : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Cons Marker : <br><b>" . ($markerData ? $markerData->cons_marker : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Cons Piping : <br><b>" . ($markerData ? $markerData->cons_piping_marker : "-") . "</b></li>";
                $markerInfo = $markerInfo . "<li class='list-group-item'>Gramasi : <br><b>" . ($markerData ? $markerData->gramasi_marker : "-") . "</b></li>";
                $markerInfo = $markerInfo . "</ul>";

                return $markerInfo;
            })->addColumn('ratio_info', function ($row) {
                $markerDetailData = $row->formCutInput && $row->formCutInput->marker ? $row->formCutInput->marker->markerDetails : null;

                $markerDetailInfo = "
                        <table class='table table-bordered table-sm w-auto'>
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    <th>Ratio</th>
                                    <th>Cut</th>
                                    <th>Output</th>
                                </tr>
                            </thead>
                            <tbody>
                    ";

                if ($markerDetailData) {
                    foreach ($markerDetailData as $markerDetail) {
                        $markerDetailInfo .= "
                                <tr>
                                    <td>" . $markerDetail->size . "</td>
                                    <td>" . $markerDetail->ratio . "</td>
                                    <td>" . $markerDetail->qty_cutting . "</td>
                                    <td>" . ($markerDetail->ratio * ($row->formCutInput ? $row->formCutInput->qty_ply : 1)) . "</td>
                                </tr>
                            ";
                    }
                }

                $markerDetailInfo .= "
                            </tbody>
                        </table>
                    ";

                return $markerDetailInfo;
            })->addColumn('input_no_form', function ($row) {
                $input = "<input type='hidden' class='form-control' id='no_form_" . $row->id . "' name='no_form[" . $row->id . "]' value='" . $row->no_form . "'>";

                return $input;
            })->addColumn('meja', function ($row) {
                $meja = User::where('type', 'meja')->get();

                $input = "
                        <select class='form-select select2bs4' id='meja_id_" . $row->id . "' name='meja_id[" . $row->id . "]'>
                            <option value=''>Pilih Meja</option>
                    ";

                foreach ($meja as $m) {
                    $input .= "<option value='" . $m->id . "' " . ($row->formCutInput && $m->id == $row->formCutInput->meja_id ? 'class="fw-bold" selected' : '') . ">" . strtoupper($m->name) . "</option>";
                }

                $input .= "
                        </select>
                    ";

                if ($row->formCutInput && $row->formCutInput->status_form != 'idle') {
                    $input = "
                            <input class='form-control' type='hidden' id='meja_id_" . $row->id . "' name='meja_id[" . $row->id . "]' value='" . ($row->formCutInput ? $row->formCutInput : '-')->meja_id . "' readonly>
                            <input class='form-control' type='text' value='" . ($row->formCutInput ? strtoupper($row->formCutInput->alokasiMeja ? $row->formCutInput->alokasiMeja->name : '') : '') . "' readonly>
                        ";
                }

                return $input;
            })->addColumn('approve', function ($row) {
                $input = "
                        <div class='form-check w-100 text-center'><input type='checkbox' class='form-check-input border-success' id='approve_" . $row->id . "' name='approve[" . $row->id . "]' value='Y' " . ($row->app == 'y' ? 'checked' : '') . " " . ($row->formCutInput ? ($row->formCutInput->status_form != 'idle' ? 'disabled' : '') : '') . "></div>
                        " . ($row->formCutInput ? ($row->formCutInput->status_form != 'idle' ? '<input type="hidden" class="form-control" id="approve_' . $row->id . '" name="approve[' . $row->id . ']" value="' . $row->formCutInput->app . '">' : '') : '');

                return $input;
            })
            ->rawColumns(['form_info', 'marker_info', 'marker_detail_info', 'ratio_info', 'input_no_form', 'meja', 'approve'])
            ->filterColumn('marker_info', function ($query, $keyword) {
                $query->whereHas('formCutInput', function ($query) use ($keyword) {
                    $query->whereHas('marker', function ($query) use ($keyword) {
                        $query->whereRaw("(
                                marker_input.kode LIKE '%" . $keyword . "%' OR
                                marker_input.act_costing_ws LIKE '%" . $keyword . "%' OR
                                marker_input.style LIKE '%" . $keyword . "%' OR
                                marker_input.color LIKE '%" . $keyword . "%' OR
                                marker_input.panel LIKE '%" . $keyword . "%'
                            )");
                    });
                });
            })->filterColumn('form_info', function ($query, $keyword) {
                $query->whereHas('formCutInput', function ($query) use ($keyword) {
                    $query->whereRaw("(
                            form_cut_input.no_form LIKE '%" . $keyword . "%' OR
                            form_cut_input.tanggal LIKE '%" . $keyword . "%'
                        )");
                });
            })->order(function ($query) {
                $query->orderByRaw('FIELD(app, "n", "y")')->orderBy('no_form', 'desc');
            })->toJson();
        }
    }
}
