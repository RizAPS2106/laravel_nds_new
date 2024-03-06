<?php

namespace App\Http\Controllers\Marker;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\MasterPart;
use App\Models\MasterTujuan;
use App\Models\MasterSecondary;
use App\Models\Part;
use App\Models\PartDetail;
use App\Models\PartForm;
use App\Models\FormCutInput;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class PartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Get Part Data
            $part = Part::selectRaw("
                    part.id,
                    part.kode,
                    part.buyer,
                    part.act_costing_ws,
                    part.style,
                    part.color,
                    part.panel,
                    COUNT(DISTINCT form_cut_input.id) total_form,
                    GROUP_CONCAT(DISTINCT CONCAT(master_part.nama_part, '-', master_part.bagian) ORDER BY master_part.nama_part SEPARATOR ' || ') part_details,
                    a.sisa
                ")
                ->leftJoin("part_detail", "part_detail.part_kode", "=", "part.kode")
                ->leftJoin("master_part", "master_part.kode", "part_detail.master_part_kode")
                ->leftJoin("part_form", "part_form.part_kode", "part.kode")
                ->leftJoin("form_cut_input", "form_cut_input.no_form", "part_form.no_form")
                ->leftJoin(
                    DB::raw("
                        (
                            select
                                part_kode,
                                count(id) total,
                                SUM(CASE WHEN cons_part IS NULL THEN 0 ELSE 1 END) terisi,
                                count(id) - SUM(CASE WHEN cons_part IS NULL THEN 0 ELSE 1 END) sisa
                            from
                                part_detail
                            group by
                                part_kode
                        ) a"
                    ),
                    "part.kode", "=", "a.part_kode"
                )
                ->groupBy("part.kode");

            return DataTables::eloquent($part)
            ->filter(function ($query) {
                if (request()->tanggal_awal) {
                    $query->whereRaw("DATE(part.created_at) >= '" . request()->tanggal_awal . "'");
                    $query->orWhereRaw("DATE(part.updated_at) >= '" . request()->tanggal_awal . "'");
                }

                if (request()->tanggal_akhir) {
                    $query->whereRaw("DATE(part.created_at) <= '" . request()->tanggal_akhir . "'");
                    $query->orWhereRaw("DATE(part.updated_at) <= '" . request()->tanggal_akhir . "'");
                }
            })
            ->filterColumn('kode', function ($query, $keyword) {
                $query->whereRaw("LOWER(kode) LIKE LOWER('%" . $keyword . "%')");
            })
            ->filterColumn('buyer', function ($query, $keyword) {
                $query->whereRaw("LOWER(buyer) LIKE LOWER('%" . $keyword . "%')");
            })
            ->filterColumn('act_costing_ws', function ($query, $keyword) {
                $query->whereRaw("LOWER(act_costing_ws) LIKE LOWER('%" . $keyword . "%')");
            })
            ->filterColumn('style', function ($query, $keyword) {
                $query->whereRaw("LOWER(style) LIKE LOWER('%" . $keyword . "%')");
            })
            ->filterColumn('color', function ($query, $keyword) {
                $query->whereRaw("LOWER(color) LIKE LOWER('%" . $keyword . "%')");
            })
            ->filterColumn('panel', function ($query, $keyword) {
                $query->whereRaw("LOWER(panel) LIKE LOWER('%" . $keyword . "%')");
            })
            ->order(function ($query) {
                $query->
                    orderBy('part.created_at', 'desc')->
                    orderBy('part.updated_at', 'desc')->
                    orderBy('part.kode', 'desc');
            })
            ->toJson();
        }

        return view("marker.part.part", ["page" => "dashboard-marker", "subPageGroup" => "proses-marker", "subPage" => "part"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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

        $masterParts = MasterPart::all();
        $masterTujuan = MasterTujuan::all();
        $masterSecondary = MasterSecondary::all();

        return view('marker.part.create-part', ['orders' => $orders, 'masterParts' => $masterParts, 'masterTujuan' => $masterTujuan, 'masterSecondary' => $masterSecondary, 'page' => 'dashboard-marker',  "subPageGroup" => "proses-marker", "subPage" => "part"]);
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
        $colors = DB::connection('mysql_sb')
            ->select("
                select
                    sd.color
                from
                    so_det sd
                inner join so on sd.id_so = so.id
                inner join act_costing ac on so.id_cost = ac.id
                where
                    ac.id = '" . $request->act_costing_id . "' and sd.cancel = 'N'
                group by
                    sd.color
            ");

        return $colors ? $colors[0] : null;
    }

    public function getPanelList(Request $request)
    {
        $notInclude = "";
        $existParts = Part::where("act_costing_id", $request->act_costing_id)->get();
        if ($existParts->count() > 0) {
            $i = 0;
            $notInclude = "where nama_panel not in (";
            foreach ($existParts as $existPart) {
                $notInclude .= ($i == 0 ? "'" . $existPart->panel . "'" : ", '" . $existPart->panel . "'");
                $i++;
            }
            $notInclude .= ")";
        }

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
                            ac.id = '" . $request->act_costing_id . "' and k.status = 'M' and k.cancel = 'N' and sd.cancel = 'N' and so.cancel_h = 'N' and ac.status = 'confirm' and mi.mattype = 'F'
                        group by
                            id_panel
                    ) a
                inner join masterpanel mp on a.id_panel = mp.id
                " . $notInclude . "
            ");

        $optionsHtml = "<option value=''>Pilih Panel</option>";
        foreach ($panels as $panel) {
            $optionsHtml .= " <option value='" . $panel->panel . "'>" . $panel->panel . "</option> ";
        }

        return $optionsHtml;
    }

    public function getPartDetail(Request $request)
    {
        $partDetails = PartDetail::all();

        $html = "<option value=''>Pilih Panel</option>";

        foreach ($panels as $panel) {
            $html .= " <option value='" . $panel->panel . "'>" . $panel->panel . "</option> ";
        }

        return $html;
    }

    public function getMasterParts(Request $request)
    {
        $masterParts = MasterPart::all();

        $masterPartOptions = "<option value=''>Pilih Part</option>";
        foreach ($masterParts as $masterPart) {
            $masterPartOptions .= "<option value='".$masterPart->kode."'>".$masterPart->nama_part." - ".$masterPart->bagian."</option>";
        }

        return $masterPartOptions;
    }

    public function getMasterTujuan(Request $request)
    {
        $masterTujuan = MasterTujuan::all();

        $masterTujuanOptions = "<option value=''>Pilih Proses</option>";
        foreach ($masterTujuan as $tujuan) {
            $masterTujuanOptions .= "<option value='".$tujuan->kode."'>".$tujuan->tujuan."</option>";
        }

        return $masterTujuanOptions;
    }

    public function getMasterSecondaries(Request $request)
    {
        $masterSecondary = MasterSecondary::all();

        $masterSecondaryOptions = "<option value=''>Pilih Proses</option>";
        foreach ($masterSecondary as $secondary) {
            $masterSecondaryOptions .= "<option value='".$secondary->kode."' data-tujuan='".$secondary->tujuan_kode."'>".$secondary->proses."</option>";
        }

        return $masterSecondaryOptions;
    }

    public function getMasterSecondariesFilter(Request $request)
    {
        $masterSecondary = DB::select("
            select
                kode,
                proses
            from
                master_secondary
            where
                kode = '" . $request->tujuan_kode . "'
        ");
        $html = "<option value=''>Pilih Proses</option>";

        foreach ($masterSecondary as $dataproses) {
            $html .= " <option value='" . $dataproses->kode . "'>" . $dataproses->proses . "</option> ";
        }

        return $html;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $timestamp = Carbon::now();

        // Part
        $part = Part::select("kode")->orderBy("kode", "desc")->first();
        $partNumber = $part ? intval(substr($part->kode, -5)) + 1 : 1;
        $partCode = 'PRT' . sprintf('%05s', $partNumber);
        $totalPartDetail = intval($request["jumlah_part_detail"]);

        $validatedRequest = $request->validate([
            "act_costing_id" => "required",
            "act_costing_ws" => "required",
            "color" => "required",
            "panel" => "required",
            "buyer" => "required",
            "style" => "required",
        ]);

        if ($totalPartDetail > 0) {
            $partStore = Part::create([
                "kode" => $partCode,
                "act_costing_id" => $validatedRequest['act_costing_id'],
                "act_costing_ws" => $validatedRequest['act_costing_ws'],
                "buyer" => $validatedRequest['buyer'],
                "style" => $validatedRequest['style'],
                "color" => $validatedRequest['color'],
                "panel" => $validatedRequest['panel'],
                "created_by" => Auth::user()->username,
            ]);

            // Part Detail
            $partDetails = [];
            for ($i = 0; $i < $totalPartDetail; $i++) {
                if ($request["part"][$i] && $request["proses"][$i] && $request["cons"][$i] && $request["unit_cons"][$i]) {
                    array_push($partDetails, [
                        "part_kode" => $partStore->kode,
                        "master_part_kode" => $request["part"][$i],
                        "master_secondary_kode" => $request["proses"][$i],
                        "cons_part" => $request["cons"][$i],
                        "unit_cons_part" => $request["unit_cons"][$i],
                        "created_by" => Auth::user()->username,
                        "created_at" => $timestamp,
                        "updated_at" => $timestamp,
                    ]);
                }
            }
            $partDetailStore = PartDetail::insert($partDetails);

            // Form Cut
            $formCuts = FormCutInput::select('form_cut_input.id')->
                leftJoin('marker_input', 'marker_input.kode', '=', 'form_cut_input.marker_input_kode')->
                where("marker_input.act_costing_id", $partStore->act_costing_id)->
                where("marker_input.act_costing_ws", $partStore->act_costing_ws)->
                where("marker_input.buyer", $partStore->buyer)->
                where("marker_input.style", $partStore->style)->
                where("marker_input.panel", $partStore->panel)->
                where("form_cut_input.status_form", "SELESAI PENGERJAAN")->
                orderBy("no_cut_form", "asc")->
                get();

            foreach ($formCuts as $formCut) {
                $isExist = PartForm::where("part_kode", $partStore->kode)->where("form_kode", $formCut->kode)->count();

                if ($isExist < 1) {
                    $partForm = PartForm::select("kode")->orderBy("kode", "desc")->first();
                    $partFormNumber = $partForm ? intval(substr($partForm->kode, -5)) + 1 : 1;
                    $partFormCode = "PFM" . sprintf('%05s', $partFormNumber);

                    $addToPartForm = PartForm::create([
                        "kode" => $partFormCode,
                        "part_kode" => $partStore->kode,
                        "no_form" => $formCut->no_form,
                        "created_by" => Auth::user()->username,
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now(),
                    ]);
                }
            }

            return array(
                "status" => 200,
                "message" => "Parting berhasil ditambahkan. <br> '".$partCode."'",
                "redirect" => route('manage-part-form', ["id" => $partStore->id]),
                "additional" => []
            );
        }

        return array(
            "status" => 400,
            "message" => "Parting gagal ditambahkan.",
            "redirect" => "",
            "additional" => []
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function show(Part $part)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function edit(Part $part)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Part $part, $id = 0)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function destroy(Part $part, $id = 0)
    {
        $part=Part::find("id", $id);

        // Only for parting with no form
        $countPartForm = PartForm::where("part_kode", $part->kode)->count();
        if ($countPartForm < 1) {
            $deletePart = $part->delete();

            if ($deletePart) {
                return array(
                    'status' => 200,
                    'message' => 'Part <br>"'. $part->kode .'"<br> berhasil dihapus',
                    'redirect' => '',
                    'table' => 'datatable-part',
                    'additional' => [],
                );
            }
        }

        return array(
            'status' => 400,
            'message' => 'Part <br>"'. $part->kode .'"<br> tidak dapat dihapus',
            'redirect' => '',
            'table' => 'datatable-part',
            'additional' => [],
        );
    }

    public function managePartForm(Request $request, $id = 0)
    {
        if ($request->ajax()) {
            // Parting's form
            $formCutInputs = FormCutInput::selectRaw("
                    form_cut_input.id,
                    form_cut_input.tanggal,
                    form_cut_input.no_form,
                    form_cut_input.marker_input_kode,
                    form_cut_input.total_ply,
                    form_cut_input.no_cut_form,
                    meja.name nama_meja,
                    marker_input.act_costing_ws,
                    marker_input.buyer,
                    marker_input.urutan_marker,
                    marker_input.style,
                    marker_input.color,
                    marker_input.panel,
                    GROUP_CONCAT(DISTINCT CONCAT(master_size_new.size, '(', marker_input_detail.ratio, ')') ORDER BY master_size_new.urutan ASC SEPARATOR ', ') marker_details
                ")->
                leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.marker_input_kode")->
                leftJoin("marker_input_detail", "marker_input_detail.marker_input_kode", "=", "marker_input.kode")->
                leftJoin("master_size_new", "master_size_new.size", "=", "marker_input_detail.size")->
                leftJoin("part_form", "part_form.no_form", "=", "form_cut_input.no_form")->
                leftJoin("part", "part.kode", "=", "part_form.part_kode")->
                leftJoin("users as meja", "meja.id", "=", "form_cut_input.meja_id")->
                where("part.id", $id)->
                where("marker_input.act_costing_ws", $request->act_costing_ws)->
                where("marker_input.panel", $request->panel)->
                where("form_cut_input.status_form", "finish")->
                whereRaw("part_form.id is not null")->
                groupBy("form_cut_input.id");

            return Datatables::eloquent($formCutInputs)->filterColumn('act_costing_ws', function ($query, $keyword) {
                $query->whereRaw("LOWER(act_costing_ws) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('buyer', function ($query, $keyword) {
                $query->whereRaw("LOWER(buyer) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('style', function ($query, $keyword) {
                $query->whereRaw("LOWER(style) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('color', function ($query, $keyword) {
                $query->whereRaw("LOWER(color) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('panel', function ($query, $keyword) {
                $query->whereRaw("LOWER(panel) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('nama_meja', function ($query, $keyword) {
                $query->whereRaw("LOWER(nama_meja) LIKE LOWER('%" . $keyword . "%')");
            })->order(function ($query) {
                $query->orderBy('form_cut_input.no_cut_form', 'asc');
            })->toJson();
        }

        // Parting
        $part = Part::selectRaw("
                part.id,
                part.kode,
                part.buyer,
                part.act_costing_ws,
                part.style,
                part.color,
                part.panel,
                GROUP_CONCAT(DISTINCT CONCAT(master_part.nama_part, ' - ', master_part.bagian) ORDER BY master_part.nama_part ASC SEPARATOR ', ') part_details
            ")->
            leftJoin("part_detail", "part_detail.part_kode", "=", "part.kode")->
            leftJoin("master_part", "master_part.kode", "part_detail.master_part_kode")->
            where("part.id", $id)->
            groupBy("part.id")->
            first();

        return view("marker.part.manage-part-form", ["part" => $part, "page" => "dashboard-marker",  "subPageGroup" => "proses-marker", "subPage" => "part"]);
    }

    public function getPartForm(Request $request, $id = 0)
    {
        $formCutInputs = FormCutInput::selectRaw("
                form_cut_input.id,
                form_cut_input.tanggal,
                form_cut_input.no_form,
                form_cut_input.marker_input_kode,
                form_cut_input.total_ply,
                form_cut_input.no_cut_form,
                meja.name nama_meja,
                marker_input.act_costing_ws,
                marker_input.buyer,
                marker_input.urutan_marker,
                marker_input.color,
                marker_input.style,
                marker_input.panel,
                GROUP_CONCAT(DISTINCT CONCAT(master_size_new.size, '(', marker_input_detail.ratio, ')') ORDER BY master_size_new.urutan ASC SEPARATOR ', ') marker_details
            ")->
            leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.marker_input_kode")->
            leftJoin("marker_input_detail", "marker_input_detail.marker_input_kode", "=", "marker_input.kode")->
            leftJoin("master_size_new", "master_size_new.size", "=", "marker_input_detail.size")->
            leftJoin("users as meja", "meja.id", "=", "form_cut_input.meja_id")->
            leftJoin("part_form", "part_form.no_form", "=", "form_cut_input.no_form")->
            where("form_cut_input.status_form", "finish")->
            where("marker_input.act_costing_ws", $request->act_costing_ws)->
            where("marker_input.panel", $request->panel)->
            whereRaw("part_form.id is null")->
            groupBy("form_cut_input.id");

        return Datatables::eloquent($formCutInputs)->
            filterColumn('act_costing_ws', function ($query, $keyword) {
                $query->whereRaw("LOWER(act_costing_ws) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('buyer', function ($query, $keyword) {
                $query->whereRaw("LOWER(buyer) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('style', function ($query, $keyword) {
                $query->whereRaw("LOWER(style) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('color', function ($query, $keyword) {
                $query->whereRaw("LOWER(color) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('panel', function ($query, $keyword) {
                $query->whereRaw("LOWER(panel) LIKE LOWER('%" . $keyword . "%')");
            })->filterColumn('nama_meja', function ($query, $keyword) {
                $query->whereRaw("LOWER(nama_meja) LIKE LOWER('%" . $keyword . "%')");
            })->order(function ($query) {
                $query->orderBy('form_cut_input.no_cut_form', 'asc');
            })->toJson();
    }

    public function showPartForm(Request $request)
    {
        $formCutInputs = FormCutInput::selectRaw("
                DATE(form_cut_input.waktu_selesai) tanggal_selesai,
                form_cut_input.no_form,
                form_cut_input.no_cut_form,
                meja.name nama_meja,
                marker_input.act_costing_ws,
                marker_input.buyer,
                marker_input.style,
                marker_input.color,
                marker_input.panel,
                marker_input.urutan_marker,
                form_cut_input.total_ply,
                part_detail.id part_detail_id,
                part_form.kode part_form_kode,
                part.kode part_kode,
                GROUP_CONCAT(DISTINCT master_part.nama_part ORDER BY master_part.nama_part ASC SEPARATOR ' || ') part_details,
                GROUP_CONCAT(DISTINCT CONCAT(marker_input_detail.size, '(', marker_input_detail.ratio, ')') ORDER BY master_size_new.urutan ASC SEPARATOR ' / ') marker_details
            ")->
            leftJoin("part_form", "part_form.no_form", "=", "form_cut_input.no_form")->
            leftJoin("part", "part.kode", "=", "part_form.part_kode")->
            leftJoin("part_detail", "part_detail.part_kode", "=", "part.kode")->
            leftJoin("master_part", "master_part.kode", "=", "part_detail.master_part_kode")->
            leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.marker_input_kode")->
            leftJoin("marker_input_detail", "marker_input_detail.marker_input_kode", "=", "marker_input.kode")->
            leftJoin("master_size_new", "master_size_new.size", "=", "marker_input_detail.size")->
            leftJoin("users as meja", "meja.id", "=", "form_cut_input.meja_id")->
            whereRaw("part_form.id is not null")->
            where("part.id", $request->id)->
            groupBy("form_cut_input.id");

        return Datatables::of($formCutInputs)->
        filterColumn('id_marker', function ($query, $keyword) {
            $query->whereRaw("LOWER(form_cut_input.marker_kode) LIKE LOWER('%" . $keyword . "%')");
        })->filterColumn('no_form', function ($query, $keyword) {
            $query->whereRaw("LOWER(form_cut_input.no_form) LIKE LOWER('%" . $keyword . "%')");
        })->filterColumn('nama_meja', function ($query, $keyword) {
            $query->whereRaw("LOWER(users.name) LIKE LOWER('%" . $keyword . "%')");
        })->filterColumn('act_costing_ws', function ($query, $keyword) {
            $query->whereRaw("LOWER(marker_input.act_costing_ws) LIKE LOWER('%" . $keyword . "%')");
        })->filterColumn('buyer', function ($query, $keyword) {
            $query->whereRaw("LOWER(marker_input.buyer) LIKE LOWER('%" . $keyword . "%')");
        })->filterColumn('style', function ($query, $keyword) {
            $query->whereRaw("LOWER(marker_input.style) LIKE LOWER('%" . $keyword . "%')");
        })->filterColumn('color', function ($query, $keyword) {
            $query->whereRaw("LOWER(marker_input.color) LIKE LOWER('%" . $keyword . "%')");
        })->filterColumn('panel', function ($query, $keyword) {
            $query->whereRaw("LOWER(marker_input.panel) LIKE LOWER('%" . $keyword . "%')");
        })->filterColumn('kode_part_form', function ($query, $keyword) {
            $query->whereRaw("LOWER(part_form.kode) LIKE LOWER('%" . $keyword . "%')");
        })->filterColumn('kode_part', function ($query, $keyword) {
            $query->whereRaw("LOWER(part.kode) LIKE LOWER('%" . $keyword . "%')");
        })->filterColumn('nama_part', function ($query, $keyword) {
            $query->whereRaw("LOWER(master_part.nama_part) LIKE LOWER('%" . $keyword . "%')");
        })->filterColumn('no_cut', function ($query, $keyword) {
            $query->whereRaw("LOWER(form_cut_input.no_cut) LIKE LOWER('%" . $keyword . "%')");
        })->filterColumn('total_ply', function ($query, $keyword) {
            $query->whereRaw("LOWER(form_cut_input.total_ply) LIKE LOWER('%" . $keyword . "%')");
        })->order(function ($query) {
            $query->
                orderBy('marker_input.act_costing_ws', 'desc')->
                orderBy('form_cut_input.no_cut_form', 'asc')->
                orderBy('form_cut_input.waktu_selesai', 'asc')->
                orderByRaw('FIELD(form_cut_input.tipe_form, null, "NORMAL", "MANUAL")');
        })->toJson();
    }

    public function storePartForm(Request $request)
    {
        $success = [];
        $fail = [];
        $exist = [];

        foreach ($request->partForms as $partForm) {
            $isExist = PartForm::where("part_kode", $request->part_kode)->where("no_form", $partForm['no_form'])->count();

            if ($isExist < 1) {
                $lastPartForm = PartForm::select("kode")->orderBy("kode", "desc")->first();
                $urutanPartForm = $lastPartForm ? intval(substr($lastPartForm->kode, -5)) + 1 : 1;
                $kodePartForm = "PFM" . sprintf('%05s', $urutanPartForm);

                $addToPartForm = PartForm::create([
                    "kode" => $kodePartForm,
                    "part_kode" => $request->part_kode,
                    "no_form" => $partForm['no_form'],
                    "created_at" => Auth::user()->username,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ]);

                if ($addToPartForm) {
                    array_push($success, ['no_form' => $partForm['no_form']]);
                } else {
                    array_push($fail, ['no_form' => $partForm['no_form']]);
                }
            } else {
                array_push($exist, ['no_form' => $partForm['no_form']]);
            }
        }

        if (count($success) > 0) {
            return array(
                'status' => 200,
                'message' => 'Form berhasil ditambahkan',
                'redirect' => '',
                'table' => 'datatable-selected',
                'additional' => ["success" => $success, "fail" => $fail, "exist" => $exist],
            );
        } else {
            return array(
                'status' => 400,
                'message' => 'Form tidak ditemukan',
                'redirect' => '',
                'table' => 'datatable-selected',
                'additional' => ["success" => $success, "fail" => $fail, "exist" => $exist],
            );
        }
    }

    public function destroyPartForm(Request $request)
    {
        $success = [];
        $fail = [];
        $exist = [];

        foreach ($request->partForms as $partForm) {
            $isExist = PartForm::where("part_kode", $request->part_kode)->where("no_form", $partForm['no_form'])->count();

            if ($isExist > 0) {
                $removeCutPlan = PartForm::where("part_kode", $request->part_kode)->where("no_form", $partForm['no_form'])->delete();

                if ($removeCutPlan) {
                    array_push($success, ['no_form' => $partForm['no_form']]);
                } else {
                    array_push($fail, ['no_form' => $partForm['no_form']]);
                }
            } else {
                array_push($exist, ['no_form' => $partForm['no_form']]);
            }
        }

        if (count($success) > 0) {
            return array(
                'status' => 200,
                'message' => 'Part Form berhasil disingkirkan',
                'redirect' => '',
                'table' => 'datatable-selected',
                'additional' => ["success" => $success, "fail" => $fail],
            );
        } else {
            return array(
                'status' => 400,
                'message' => 'Part Form tidak ditemukan',
                'redirect' => '',
                'table' => 'datatable-selected',
                'additional' => ["success" => $success, "fail" => $fail],
            );
        }
    }

    public function managePartSecondary(Request $request, $id = 0)
    {
        $part = Part::selectRaw("
                part.id,
                part.kode,
                part.buyer,
                part.act_costing_ws,
                part.style,
                part.color,
                part.panel,
                GROUP_CONCAT(DISTINCT CONCAT(master_part.nama_part, ' - ', master_part.bagian) ORDER BY master_part.nama_part ASC SEPARATOR ', ') part_details
            ")->
            leftJoin("part_detail", "part_detail.part_kode", "=", "part.kode")->
            leftJoin("master_part", "master_part.kode", "part_detail.master_part_kode")->
            where("part.id", $id)->
            groupBy("part.id")->
            first();

        $masterParts = MasterPart::all();
        $masterTujuan = MasterTujuan::all();
        $masterSecondary = MasterSecondary::all();

        return view("marker.part.manage-part-secondary", ["part" => $part, "masterParts" => $masterParts, "masterTujuan" => $masterTujuan, "page" => "dashboard-marker",  "subPageGroup" => "proses-marker", "subPage" => "part"]);
    }

    public function showPartList(Request $request)
    {
        $partList = DB::select(
            "
            SELECT
                pd.id,
                pd.cons_part,
                UPPER(pd.unit_cons_part) unit_cons_part,
                CONCAT(mp.nama_part, ' - ', mp.bagian) nama_part,
                ms.kode,
                ms.tujuan,
                ms.proses
            FROM
                part_detail pd
            inner join master_part mp on pd.master_part_kode = mp.kode
            left join master_secondary ms on pd.master_secondary_kode = ms.kode
            where
                part_kode = '" . $request->kode . "'
            "
        );

        return DataTables::of($partList)->toJson();
    }

    public function storePartSecondary(Request $request)
    {
        $validatedRequest = $request->validate([
            "kode" => "required",
            "part" => "required",
            "cons" => "required",
            "tujuan" => "required",
            "proses" => "required",
        ]);

        $upsertPartDetail = PartDetail::updateOrCreate(['part_kode' => $request->kode, 'master_part_kode' => $validatedRequest['part']],[
            'master_secondary_kode' => $validatedRequest['proses'],
            'cons_part' => $validatedRequest['cons'],
            'unit_cons_part' => 'METER',
            'created_by' => Auth::user()->username,
        ]);

        if ($upsertPartDetail) {
            return array(
                'icon' => 'success',
                'message' => 'Data Part "' . $validatedRequest['part'] . '" berhasil diupdate',
            );
        }
        return array(
            'icon' => 'fail',
            'message' => 'Data Part "' . $validatedRequest['part'] . '" berhasil diupdate',
        );
    }
}
