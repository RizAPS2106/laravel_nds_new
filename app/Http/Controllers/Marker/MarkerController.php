<?php

namespace App\Http\Controllers\Marker;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Models\Marker;
use App\Models\MarkerDetail;

use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use PDF;

class MarkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $markers = Marker::selectRaw("
                id,
                kode,
                tanggal,
                act_costing_ws,
                style,
                color,
                panel,
                gelar_qty_marker,
                gelar_qty_balance_marker,
                tipe_marker,
                urutan_marker,
                po_marker,
                CONCAT( panjang_marker, ' ', UPPER(unit_panjang_marker), ' ', comma_marker, ' ', UPPER(unit_comma_marker) ) panjang_marker,
                CONCAT( lebar_marker, ' ', UPPER(unit_lebar_marker) ) lebar_marker,
                COALESCE( gramasi_marker, 0 ) gramasi_marker,
                COALESCE( form_cut_inputs.total_form, 0 ) total_form,
                COALESCE( form_cut_inputs.total_ply, 0 ) total_ply,
                CONCAT( COALESCE( form_cut_inputs.total_ply, 0 ), '/', gelar_qty_marker ) ply_progress,
                COALESCE( notes, '-' ) notes,
                cancel
            ")->
            leftJoin(
                DB::raw("
                    (
                        select
                            marker_input_kode,
                            count(no_form) total_form,
                            sum(total_ply) total_ply
                        from
                            form_cut_input
                        group by
                            marker_input_kode
                    ) form_cut_inputs"
                ),
                "marker_input.kode",
                "=",
                "form_cut_inputs.marker_input_kode"
            );

            return DataTables::eloquent($markers)->
                filter(function ($query) {
                    if (request('tanggal_awal')) {
                        $query->whereRaw("tanggal >= '" . request('tanggal_awal') . "'");
                    }

                    if (request('tanggal_akhir')) {
                        $query->whereRaw("tanggal <= '" . request('tanggal_akhir') . "'");
                    }
                }, true)->
                filterColumn('kode', function ($query, $keyword) {
                    $query->whereRaw("LOWER(kode) LIKE LOWER('%" . $keyword . "%')");
                })->
                filterColumn('act_costing_ws', function ($query, $keyword) {
                    $query->whereRaw("LOWER(act_costing_ws) LIKE LOWER('%" . $keyword . "%')");
                })->
                filterColumn('color', function ($query, $keyword) {
                    $query->whereRaw("LOWER(color) LIKE LOWER('%" . $keyword . "%')");
                })->
                filterColumn('panel', function ($query, $keyword) {
                    $query->whereRaw("LOWER(panel) LIKE LOWER('%" . $keyword . "%')");
                })->
                filterColumn('po_marker', function ($query, $keyword) {
                    $query->whereRaw("LOWER(po_marker) LIKE LOWER('%" . $keyword . "%')");
                })->
                order(function ($query) {
                    $query->orderBy('cancel', 'asc')->orderBy('updated_at', 'desc');
                })->
                toJson();
        }

        return view('marker.marker.marker', ["subPageGroup" => "proses-marker", "subPage" => "marker", "page" => "dashboard-marker"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $markerDetail = MarkerDetail::selectRaw("
                    marker_input_detail.so_det_id,
                    marker_input.panel,
                    SUM(marker_input_detail.cut_qty) total_cut_qty
                ")->
                leftJoin('marker_input', 'marker_input.kode', '=', 'marker_input_detail.marker_input_kode')->
                where('marker_input.cancel', 'N')->
                groupBy("marker_input_detail.so_det_id", "marker_input.panel")->
                get();

            return $markerDetail;
        }

        $orders = DB::connection('mysql_sb')->
            table('act_costing')->
            select('id', 'kpno')->
            where('status', '!=', 'CANCEL')->
            where('cost_date', '>=', '2023-01-01')->
            where('type_ws', 'STD')->
            orderBy('cost_date', 'desc')->
            orderBy('kpno', 'asc')->
            groupBy('kpno')->
            get();

        return view('marker.marker.create-marker', ['orders' => $orders, 'page' => 'dashboard-marker', "subPageGroup" => "proses-marker", "subPage" => "marker"]);
    }

    public function getOrderInfo(Request $request)
    {
        $order = DB::connection('mysql_sb')->
            table('act_costing')->
            selectRaw('
                act_costing.id,
                act_costing.kpno,
                act_costing.styleno,
                act_costing.qty order_qty,
                mastersupplier.supplier buyer
            ')->
            leftJoin('mastersupplier', 'mastersupplier.Id_Supplier', '=', 'act_costing.id_buyer')->
            where('id', $request->act_costing_id)->
            first();

        return json_encode($order);
    }

    public function getColorList(Request $request)
    {
        $colors = DB::connection('mysql_sb')->
            select("
                select
                    sd.color
                from
                    so_det sd
                inner join so on sd.id_so = so.id
                inner join act_costing ac on so.id_cost = ac.id
                where
                    ac.id = '" . $request->act_costing_id . "' and
                    sd.cancel = 'N'
                group by
                    sd.color
            ");

        $options = "<option value=''>Pilih Color</option>";

        foreach ($colors as $color) {
            $options .= " <option value='" . $color->color . "'>" . $color->color . "</option> ";
        }

        return $options;
    }

    public function getSizeList(Request $request)
    {
        $sizesQuery = DB::table("master_sb_ws")->
            selectRaw("
                master_sb_ws.id_so_det so_det_id,
                master_sb_ws.ws no_ws,
                master_sb_ws.color,
                master_sb_ws.size,
                master_sb_ws.qty order_qty,
                COALESCE(marker_input_detail.ratio, 0) ratio,
                COALESCE(marker_input_detail.cut_qty, 0) cut_qty
            ")->
            where("master_sb_ws.id_act_cost", $request->act_costing_id)->
            where("master_sb_ws.color", $request->color);

        if ($request->marker_input_kode) {
            $sizesQuery->
                leftJoin('marker_input_detail', function ($join) use ($request) {
                    $join->on('marker_input_detail.so_det_id', '=', 'master_sb_ws.id_so_det');
                    $join->on('marker_input_detail.marker_input_kode', '=', DB::raw($request->marker_input_kode));
                })->
                leftJoin('master_size_new', 'master_size_new.size', '=', 'master_sb_ws.size')->
                leftJoin('marker_input', 'marker_input.id', '=', 'marker_input_detail.marker_input_kode');
        } else {
            $sizesQuery->
                leftJoin('marker_input_detail', 'marker_input_detail.so_det_id', '=', 'master_sb_ws.id_so_det')->
                leftJoin('marker_input', 'marker_input.id', '=', 'marker_input_detail.marker_input_kode')->
                leftJoin("master_size_new", "master_size_new.size", "=", "master_sb_ws.size");
        }

        $sizes = $sizesQuery->
            groupBy("id_act_cost", "color", "size")->
            orderBy("master_size_new.urutan")->
            get();

        return json_encode([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval(count($sizes)),
            "recordsFiltered" => intval(count($sizes)),
            "data" => $sizes
        ]);
    }

    public function getPanelList(Request $request)
    {
        $panels = DB::connection('mysql_sb')->select("
                select
                    nama_panel panel
                from
                    (
                        select
                            id_panel
                        from
                            bom_jo_item k
                        inner join so_det sd on k.id_so_det = sd.id
                        inner join so on sd.id_so = so.id
                        inner join act_costing ac on so.id_cost = ac.id
                        inner join masteritem mi on k.id_item = mi.id_gen
                        where
                            ac.id = '" . $request->act_costing_id . "' and
                            sd.color = '" . $request->color . "' and
                            k.status = 'M' and
                            k.cancel = 'N' and
                            sd.cancel = 'N' and
                            so.cancel_h = 'N' and
                            ac.status = 'confirm'
                            and mi.mattype = 'F'
                        group by
                            id_panel
                    ) a
                inner join masterpanel mp on a.id_panel = mp.id
            ");

        $options = "<option value=''>Pilih Panel</option>";

        foreach ($panels as $panel) {
            $options .= " <option value='" . $panel->panel . "'>" . $panel->panel . "</option> ";
        }

        return $options;
    }

    public function getNumber(Request $request)
    {
        $number = DB::connection('mysql_sb')->select("
                select
                    k.cons cons_ws,
                    sum(sd.qty) order_qty
                from
                    bom_jo_item k
                inner join so_det sd on k.id_so_det = sd.id
                inner join so on sd.id_so = so.id
                inner join act_costing ac on so.id_cost = ac.id
                inner join masteritem mi on k.id_item = mi.id_gen
                inner join masterpanel mp on k.id_panel = mp.id
                where
                    ac.id = '" . $request->act_costing_id . "' and
                    sd.color = '" . $request->color . "' and
                    mp.nama_panel = '" . $request->panel . "' and
                    k.status = 'M' and
                    k.cancel = 'N' and
                    sd.cancel = 'N' and
                    so.cancel_h = 'N' and
                    ac.status = 'confirm' and
                    mi.mattype = 'F'
                group by
                    sd.color,
                    k.id_item,
                    k.unit
                limit 1
            ");

        return json_encode($number ? $number[0] : null);
    }

    public function getCount(Request $request)
    {
        $countMarker = Marker::where('act_costing_id', $request->act_costing_id)->
            where('color', $request->color)->
            where('panel', $request->panel)->
            count() + 1;

        return $countMarker ? $countMarker : 1;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $markerCount = Marker::selectRaw("MAX(kode) latest_kode")->
            whereRaw("kode LIKE 'MRK/" . date('ym') . "/%'")->
            first();
        $markerNumber = intval(substr($markerCount->latest_kode, -5)) + 1;
        $markerCode = 'MRK/' . date('ym') . '/' . sprintf('%05s', $markerNumber);
        $totalQty = 0;

        $validatedRequest = $request->validate([
            "tanggal" => "required",
            "act_costing_id" => "required",
            "act_costing_ws" => "required",
            "buyer" => "required",
            "style" => "required",
            "color" => "required",
            "panel" => "required",
            "cons_ws" => "required|numeric|min:0",
            "tipe_marker" => "required",
            "urutan_marker" => "required",
            "po_marker" => "required",
            "p_marker" => "required|numeric|min:0",
            "p_unit" => "required",
            "comma_marker" => "required|numeric|min:0",
            "comma_unit" => "required",
            "l_marker" => "required|numeric|min:0",
            "l_unit" => "required",
            "gelar_qty_marker" => "required|numeric|gt:0",
            "cons_marker" => "required|numeric|gt:0",
            "gramasi_marker" => "required|numeric|gt:0",
            "cons_piping_marker" => "required|numeric|min:0"
        ]);

        foreach ($request["cut_qty"] as $qty) {
            $totalQty += $qty;
        }

        if ($totalQty > 0) {
            $markerStore = Marker::create([
                'tanggal' => $validatedRequest['tanggal'],
                'kode' => $markerCode,
                'act_costing_id' => $validatedRequest['act_costing_id'],
                'act_costing_ws' => $validatedRequest['act_costing_ws'],
                'buyer' => $validatedRequest['buyer'],
                'style' => $validatedRequest['style'],
                'color' => $validatedRequest['color'],
                'panel' => $validatedRequest['panel'],
                'cons_ws' => $validatedRequest['cons_ws'],
                'tipe_marker' => $validatedRequest['tipe_marker'],
                'urutan_marker' => $validatedRequest['urutan_marker'],
                'panjang_marker' => $validatedRequest['p_marker'],
                'unit_panjang_marker' => $validatedRequest['p_unit'],
                'comma_marker' => $validatedRequest['comma_marker'],
                'unit_comma_marker' => $validatedRequest['comma_unit'],
                'lebar_marker' => $validatedRequest['l_marker'],
                'unit_lebar_marker' => $validatedRequest['l_unit'],
                'gelar_qty_marker' => $validatedRequest['gelar_qty_marker'],
                'gelar_qty_balance_marker' => $validatedRequest['gelar_qty_marker'],
                'po_marker' => $validatedRequest['po_marker'],
                'cons_marker' => $validatedRequest['cons_marker'],
                'gramasi_marker' => $validatedRequest['gramasi_marker'],
                'cons_piping_marker' => $validatedRequest['cons_piping_marker'],
                'notes' => $request['notes'],
                'cancel' => 'N',
                'created_by' => Auth::user()->id,
            ]);

            $timestamp = Carbon::now();
            $markerDetailData = [];
            for ($i = 0; $i < intval($request['jumlah_so_det']); $i++) {
                array_push($markerDetailData, [
                    "marker_input_kode" => $markerCode,
                    "so_det_id" => $request["so_det_id"][$i],
                    "size" => $request["size"][$i],
                    "ratio" => $request["ratio"][$i],
                    "cut_qty" => $request["cut_qty"][$i],
                    "cancel" => 'N',
                    "created_by" => Auth::user()->id,
                    "created_at" => $timestamp,
                    "updated_at" => $timestamp,
                ]);
            }

            $markerDetailStore = MarkerDetail::insert($markerDetailData);

            return array(
                "status" => 200,
                "message" => $markerCode,
                "additional" => [],
            );
        }

        return array(
            "status" => 400,
            "message" => "Total Cut Qty Kosong",
            "additional" => [],
        );
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marker  $marker
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $markerData = DB::select("
            SELECT
                a.*,
                tanggal,
                CONCAT(panjang_marker, ' ', UPPER(unit_panjang_marker), ' ', comma_marker, ' ', UPPER(unit_comma_marker)) panjang_marker_format,
                CONCAT(lebar_marker, ' ', UPPER(unit_lebar_marker)) lebar_marker_format,
                b.qty_order
            from
                marker_input a
            left join
                (
                    select
                        id_act_cost,
                        sum(qty) qty_order
                    from
                        master_sb_ws
                    group by
                        id_act_cost
                )
            b on a.act_costing_id = b.id_act_cost
            where
                id = '$request->id'
        ");

        $markerDetailData = DB::select("
            SELECT
                a.size,
                ratio
            from
                marker_input_detail a
            left join master_size_new b on a.size = b.size
            where
                marker_input_kode = '".$markerData[0]->kode."'
            order by
                urutan
            asc
        ");

        $markerFormData = DB::select("
            select
                no_form,
                DATE_FORMAT(a.tanggal, '%d-%m-%Y') tanggal_form_cut,
                DATE_FORMAT(waktu_mulai, '%d-%m-%Y %T') waktu_mulai,
                DATE_FORMAT(waktu_selesai, '%d-%m-%Y %T') waktu_selesai,
                UPPER(u.name) no_meja,
                a.status_form
            from
                form_cut_input a
            inner join marker_input b on  a.marker_input_kode = b.kode
            left join users u on a.meja_id = u.id
            where
                b.id = '$request->id'
        ");

        foreach ($markerData as $marker) {

            $markerDetailTable = "";
            foreach ($markerDetailData as $markerDetail) :
                $markerDetailTable .= "
                    <tr>
                        <td align='center' valign='center'>$markerDetail->size</td>
                        <td align='center' valign='center'>$markerDetail->ratio</td>
                    </tr>
                ";
            endforeach;

            $markerFormTable = "";
            foreach ($markerFormData as $markerForm) :
                $markerForm .= "
                    <tr>
                        <td>" .$markerForm->tgl_form_cut. "</td>
                        <td>" .$markerForm->no_form. "</td>
                        <td>" . ($markerForm->no_meja ? $markerForm->no_meja : '-') . "</td>
                        <td>" . ($markerForm->waktu_mulai ? $markerForm->waktu_mulai : '-') . "</td>
                        <td>" . ($markerForm->waktu_selesai ? $markerForm->waktu_selesai : '-') . "</td>
                        <td>" . $markerForm->status . "</td>
                    </tr>
                ";
            endforeach;

            $modal = "
                <div class='row'>
                    <div class='col-sm-3'>
                        <div class='form-group'>
                            <label class='form-label'><small>Tanggal</small></label>
                            <input type='text' class='form-control' id='tanggal' name='tanggal' value = '" . $marker->tanggal . "' readonly>
                        </div>
                    </div>
                    <div class='col-sm-3'>
                        <div class='form-group'>
                            <label class='form-label'><small>No. WS</small></label>
                            <input type='text' class='form-control' id='no_ws' name='no_ws'  value = '" . $marker->act_costing_ws . "' readonly>
                        </div>
                    </div>
                    <div class='col-sm-3'>
                        <div class='form-group'>
                            <label class='form-label'><small>Color</small></label>
                            <input type='text' class='form-control' id='color' name='color'  value = '" . $marker->color . "' readonly>
                        </div>
                    </div>
                    <div class='col-sm-3'>
                        <div class='form-group'>
                            <label class='form-label'><small>Panel</small></label>
                            <input type='text' class='form-control' id='panel' name='panel'  value = '" . $marker->panel . "' readonly>
                        </div>
                    </div>
                </div>


                <div class='row'>
                    <div class='col-sm-4'>
                        <div class='form-group'>
                            <label class='form-label'><small>Buyer</small></label>
                            <input type='text' class='form-control' id='buyer' name='buyer' value = '" . $marker->buyer . "' readonly>
                        </div>
                    </div>
                    <div class='col-sm-4'>
                        <div class='form-group'>
                            <label class='form-label'><small>Panjang Marker</small></label>
                            <input type='text' class='form-control' id='p_marker' name='p_marker' value = '" . $marker->panjang_marker_format . "' readonly>
                        </div>
                    </div>
                    <div class='col-sm-4'>
                        <div class='form-group'>
                            <label class='form-label'><small>Lebar Marker</small></label>
                            <input type='text' class='form-control' id='l_marker' name='l_marker' value = '" . $marker->lebar_marker_format . "' readonly>
                        </div>
                    </div>
                </div>

                <div class='row'>
                    <div class='col-sm-6'>
                        <div class='form-group'>
                            <label class='form-label'><small>Style</small></label>
                            <input type='text' class='form-control' id='style' name='style' value = '" . $marker->style . "' readonly>
                        </div>
                    </div>
                    <div class='col-sm-6'>
                        <div class='form-group'>
                            <label class='form-label'><small>Qty Order</small></label>
                            <input type='text' class='form-control' id='qty_order' name='qty_order' value = '" . $marker->qty_order . "' readonly>
                        </div>
                    </div>
                </div>

                <div class='row'>
                    <div class='col-sm-3'>
                        <div class='form-group'>
                            <label class='form-label'><small>Cons. WS</small></label>
                            <input type='text' class='form-control' id='cons_ws' name='cons_ws' value = '" . $marker->cons_ws . "' readonly>
                        </div>
                    </div>
                    <div class='col-sm-3'>
                        <div class='form-group'>
                            <label class='form-label'><small>Cons. Piping</small></label>
                            <input type='text' class='form-control' id='cons_piping' name='cons_piping' value = '" . $marker->cons_piping_marker . "' readonly>
                        </div>
                    </div>
                    <div class='col-sm-3'>
                        <div class='form-group'>
                            <label class='form-label'><small>Cons. Marker</small></label>
                            <input type='text' class='form-control' id='cons_marker' name='cons_marker' value = '" . $marker->cons_marker . "' readonly>
                        </div>
                    </div>
                    <div class='col-sm-3'>
                        <div class='form-group'>
                            <label class='form-label'><small>Qty Gelar Marker</small></label>
                            <input type='text' class='form-control' id='gelar' name='gelar' value = '" . $marker->gelar_qty_marker . "' readonly>
                        </div>
                    </div>
                </div>

                <div class='row'>
                    <div class='col-sm-4'>
                        <div class='form-group'>
                            <label class='form-label'><small>PO</small></label>
                            <input type='text' class='form-control' id='po' name='po' value = '" . $marker->po_marker . "' readonly>
                        </div>
                    </div>
                    <div class='col-sm-4'>
                        <div class='form-group'>
                            <label class='form-label'><small>Gramasi</small></label>
                            <input type='text' class='form-control' id='gramasi' name='gramasi' value = '" . $marker->gramasi_marker . "' readonly>
                        </div>
                    </div>
                    <div class='col-sm-4'>
                        <div class='form-group'>
                            <label class='form-label'><small>Urutan</small></label>
                            <input type='text' class='form-control' id='urutan' name='urutan' value='" . $marker->urutan_marker . "' readonly>
                        </div>
                    </div>
                    <div class='col-sm-6'>
                        <div class='form-group'>
                            <label class='form-label'><small>Tipe Marker</small></label>
                            <input type='text' class='form-control' id='tipe_marker' name='tipe_marker' value='" . strtoupper(str_replace(" marker", "", $marker->tipe_marker)) . "' readonly>
                        </div>
                    </div>
                    <div class='col-sm-6'>
                        <div class='form-group'>
                            <label class='form-label'><small>Catatan</small></label>
                            <textarea class='form-control' id='notes' name='notes' readonly>" . ($marker->notes ? $marker->notes : '-') . "</textarea>
                        </div>
                    </div>
                </div>
            </div>


            <div class='row'>
                <div class='col-sm-12'>
                    <div class='card card-primary collapsed-card'>
                        <div class='card-header'>
                            <h1 class='card-title'>Detail Size</h1>
                            <div class='card-tools'>
                                <button type='button' class='btn btn-tool' data-card-widget='collapse'><i class='fas fa-plus'></i></button>
                            </div>
                        </div>
                        <div class='card-body' style='display: none;'>
                            <div class='table-responsive'>
                                <table class='table table-bordered table-striped table-sm w-100'>
                                    <thead>
                                        <tr>
                                            <th class='text-center'>Size</th>
                                            <th class='text-center'>Ratio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ". $markerDetailTable ."
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class='row'>
                <div class='col-md-12'>
                    <div class='card card-info collapsed-card'>
                        <div class='card-header'>
                            <h1 class='card-title'>Status Form</h1>
                            <div class='card-tools'>
                                <button type='button' class='btn btn-tool' data-card-widget='collapse'>
                                    <i class='fas fa-plus'></i>
                                </button>
                            </div>
                        </div>
                        <div class='card-body' style='display: none;'>
                            <div class='table-responsive'>
                                <table class='table table-bordered table-striped'>
                                    <thead>
                                        <tr>
                                            <th>Tanggal Form</th>
                                            <th>No. Form</th>
                                            <th>No. Meja</th>
                                            <th>Waktu Mulai</th>
                                            <th>Waktu Selesai</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ". $markerFormTable. "
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ";
        }

        return $modal;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marker  $marker
     * @return \Illuminate\Http\Response
     */
    public function edit(Marker $marker, $id)
    {
        $marker = Marker::where('id', $id)->first();

        return view('marker.marker.edit-marker', ['marker' => $marker]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marker  $marker
     * @return \Illuminate\Http\Response
     */
    public function update(Marker $marker, Request $request, $id)
    {
        $validatedRequest = $request->validate([
            "tanggal" => "required",
            "act_costing_id" => "required",
            "act_costing_ws" => "required",
            "buyer" => "required",
            "style" => "required",
            "color" => "required",
            "panel" => "required",
            "cons_ws" => "required|numeric|min:0",
            "kode_marker" => "required",
            "tipe_marker" => "required",
            "urutan_marker" => "required",
            "po_marker" => "required",
            "p_marker" => "required|numeric|min:0",
            "p_unit" => "required",
            "comma_marker" => "required|numeric|min:0",
            "comma_unit" => "required",
            "l_marker" => "required|numeric|min:0",
            "l_unit" => "required",
            "gelar_qty_marker" => "required|numeric|gt:0",
            "cons_marker" => "required|numeric|gt:0",
            "gramasi_marker" => "required|numeric|gt:0",
            "cons_piping_marker" => "required|numeric|min:0"
        ]);

        $totalQty = 0;
        foreach ($request["cut_qty"] as $qty) {
            $totalQty += $qty;
        }

        if ($totalQty > 0) {
            $marker = Marker::where('id', $id)->first();

            $markerUpdate = $marker->update([
                'tanggal' => $validatedRequest['tanggal'],
                'kode' => $validatedRequest['kode_marker'],
                'act_costing_id' => $validatedRequest['act_costing_id'],
                'act_costing_ws' => $validatedRequest['act_costing_ws'],
                'buyer' => $validatedRequest['buyer'],
                'style' => $validatedRequest['style'],
                'color' => $validatedRequest['color'],
                'panel' => $validatedRequest['panel'],
                'cons_ws' => $validatedRequest['cons_ws'],
                'tipe_marker' => $validatedRequest['tipe_marker'],
                'urutan_marker' => $validatedRequest['urutan_marker'],
                'panjang_marker' => $validatedRequest['p_marker'],
                'unit_panjang_marker' => $validatedRequest['p_unit'],
                'comma_marker' => $validatedRequest['comma_marker'],
                'unit_comma_marker' => $validatedRequest['comma_unit'],
                'lebar_marker' => $validatedRequest['l_marker'],
                'unit_lebar_marker' => $validatedRequest['l_unit'],
                'gelar_qty_marker' => $validatedRequest['gelar_qty_marker'],
                'gelar_qty_balance_marker' => $validatedRequest['gelar_qty_marker'],
                'po_marker' => $validatedRequest['po_marker'],
                'cons_marker' => $validatedRequest['cons_marker'],
                'gramasi_marker' => $validatedRequest['gramasi_marker'],
                'cons_piping_marker' => $validatedRequest['cons_piping_marker'],
                'notes' => $request['notes'],
                'cancel' => 'N',
                'created_by' => Auth::user()->id,
            ]);

            $timestamp = Carbon::now();
            for ($i = 0; $i < intval($request['jumlah_so_det']); $i++) {
                $markerDetail = MarkerDetail::where('marker_input_kode', $marker->kode)->where('so_det_id', $request["so_det_id"][$i])->first();
                if ($markerDetail) {
                    $markerDetail->update([
                        "size" => $request["size"][$i],
                        "ratio" => $request["ratio"][$i],
                        "cut_qty" => $request["cut_qty"][$i],
                        "cancel" => "N",
                        "created_at" => $timestamp,
                        "updated_at" => $timestamp,
                    ]);
                } else {
                    MarkerDetail::create([
                        "marker_input_kode" => $marker->kode,
                        "so_det_id" => $request["so_det_id"][$i],
                        "size" => $request["size"][$i],
                        "ratio" => $request["ratio"][$i],
                        "cut_qty" => $request["cut_qty"][$i],
                        "cancel" => 'N',
                        "created_at" => $timestamp,
                        "updated_at" => $timestamp,
                    ]);
                }
            }

            return array(
                "status" => 200,
                "message" => $marker->kode,
                "redirect" => route('marker'),
                "additional" => [],
            );
        }

        return array(
            "status" => 400,
            "message" => "Total Cut Qty Kosong",
            "redirect" => route('marker'),
            "additional" => [],
        );
    }

    public function showGramasi(Request $request)
    {
        $markerGramasi = DB::select("
            select
                marker_input.id,
                gramasi_marker,
                tipe_marker,
                status_marker,
                count(form_cut_input.id) jumlah_form
            from
                marker_input
                left join form_cut_input on form_cut_input.marker_input_kode = marker_input.kode
            where
                marker_input.id = '".$request->id."'
            group by
                marker_input.id
        ");

        return json_encode($markerGramasi ? $markerGramasi[0] : null);
    }

    public function updateStatus(Request $request, Marker $marker)
    {
        $updateStatus = DB::update("
            update
                marker_input
            set
                cancel = case when cancel = 'Y' then 'N' else 'Y' end
            where
                id = '$request->id'
        ");
    }

    public function updateMarker(Request $request)
    {
        $updateStatus = "";

        if ($request->pilot_status) {
            $updateStatus .= ", status_marker = '" . $request->pilot_status . "'";

            if ($request->pilot_status == "active") {
                $updateStatus .= ", tipe_marker = 'bulk marker', notes = 'Pilot to Bulk'";
            }
        }

        $quickUpdateMarker = DB::update("
            update
                marker_input
            set
                gramasi = '" . $request->gramasi_marker ." '
                " . $updateStatus . "
            where
                id = '".$request->id."'
        ");

        if ($quickUpdateMarker) {
            $marker = Marker::where('id', $request->id)->first();

            return array(
                'status' => 200,
                'message' => 'Data form "' . $marker->kode . '" berhasil diubah',
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
     * @param  \App\Models\Marker  $marker
     * @return \Illuminate\Http\Response
     */
    public function destroy(Marker $marker)
    {
        //
    }

    public function printMarker($kodeMarker)
    {
        $kodeMarker = str_replace("_", "/", $kodeMarker);

        $markerData = Marker::where('kode', $kodeMarker)->first();

        $actCostingData = DB::connection('mysql_sb')->
            table('act_costing')->
            selectRaw('
                SUM(so_det.qty) order_qty,
                so_det.unit unit_qty
            ')->
            leftJoin('so', 'so.id_cost', '=', 'act_costing.id')->
            leftJoin('so_det', 'so_det.id_so', '=', 'so.id')->
            where('act_costing.id', $markerData->act_costing_id)->
            where('so_det.color', $markerData->color)->
            groupBy('act_costing.id')->
            first();

        $soDetData = DB::connection('mysql_sb')->
            table('so_det')->
            selectRaw('
                so_det.id,
                so_det.size as size,
                so_det.qty as qty
            ')->
            leftJoin('so', 'so.id', '=', 'so_det.id_so')->
            leftJoin('act_costing', 'so.id_cost', '=', 'act_costing.id')->
            leftJoin('master_size_new', 'master_size_new.size', '=', 'so_det.size')->
            where('act_costing.id', $markerData->act_costing_id)->
            where('so_det.color', $markerData->color)->
            where('so_det.qty', '>', '0')->
            groupBy('so_det.size')->
            orderBy('master_size_new.urutan')->
            get();

        $orderQty = DB::connection('mysql_sb')->
            select("
                select
                    k.cons cons_ws, sum(sd.qty) order_qty
                from
                    bom_jo_item k
                inner join so_det sd on k.id_so_det = sd.id
                inner join so on sd.id_so = so.id
                inner join act_costing ac on so.id_cost = ac.id
                inner join masteritem mi on k.id_item = mi.id_gen
                inner join masterpanel mp on k.id_panel = mp.id
                where
                    ac.id = '" . $markerData->act_costing_id . "' and
                    sd.color = '" . $markerData->color . "' and
                    mp.nama_panel ='" . $markerData->panel . "' and
                    k.status = 'M' and
                    k.cancel = 'N' and
                    sd.cancel = 'N' and
                    so.cancel_h = 'N' and
                    ac.status = 'confirm' and
                    mi.mattype = 'F' and
                    sd.qty > 0
                group by
                    sd.color,
                    k.id_item,
                    k.unit
                limit 1
            ");

        if ($markerData) {
            // generate pdf
            PDF::setOption(['dpi' => 150]);
            $pdf = PDF::loadView('marker.marker.pdf.print-marker', ["markerData" => $markerData, "actCostingData" => $actCostingData, "soDetData" => $soDetData, "orderQty" => $orderQty])->setPaper('a4', 'landscape');

            $path = public_path('pdf/');
            $fileName = 'stocker-' . str_replace("/", "_", $kodeMarker) . '.pdf';
            $pdf->save($path . '/' . str_replace("/", "_", $kodeMarker));
            $generatedFilePath = public_path('pdf/' . str_replace("/", "_", $kodeMarker));

            return response()->download($generatedFilePath);
        }
    }
}
