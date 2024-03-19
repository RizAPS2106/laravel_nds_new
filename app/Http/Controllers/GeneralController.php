<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use App\Models\MarkerDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class GeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
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
                mastersupplier.supplier buyer,
                GROUP_CONCAT(DISTINCT so_det.color SEPARATOR ", ") colors
            ')->
            leftJoin('mastersupplier', 'mastersupplier.Id_Supplier', '=', 'act_costing.id_buyer')->
            leftJoin('so', 'so.id_cost', '=', 'act_costing.id')->
            leftJoin('so_det', 'so_det.id_so', '=', 'so.id')->
            where('act_costing.id', $request->act_costing_id)->
            groupBy('act_costing.id')->
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
                master_sb_ws.ws act_costing_ws,
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
                    $join->on('marker_input_detail.marker_input_kode', '=', DB::raw("'".$request->marker_input_kode."'"));
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
        $panels = DB::connection('mysql_sb')->
            select("
                select
                    nama_panel panel
                from
                    (
                        select
                            id_panel
                        from
                            bom_jo_item
                        inner join so_det sd on bom_jo_item.id_so_det = sd.id
                        inner join so on sd.id_so = so.id
                        inner join act_costing ac on so.id_cost = ac.id
                        inner join masteritem mi on bom_jo_item.id_item = mi.id_gen
                        where
                            ac.id = '" . $request->act_costing_id . "' and
                            bom_jo_item.status = 'M' and
                            bom_jo_item.cancel = 'N' and
                            sd.cancel = 'N' and
                            so.cancel_h = 'N' and
                            ac.status = 'confirm' and
                            mi.mattype = 'F'
                        group by
                            id_panel
                    ) a
                inner join masterpanel on a.id_panel = masterpanel.id
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
                    mp.nama_panel ='" . $request->panel . "' and
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
     * @param  \App\Models\Marker  $marker
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marker  $marker
     * @return \Illuminate\Http\Response
     */
    public function edit(Marker $marker, $id)
    {
        //
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
        //
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
}
