<?php

namespace App\Http\Controllers;

use App\Models\Stocker;
use App\Models\StockerDetail;
use App\Models\FormCutInput;
use App\Models\FormCutInputDetail;
use App\Models\FormCutInputDetailLap;
use App\Models\Marker;
use App\Models\MasterLokasi;
use App\Models\UnitLokasi;
use App\Models\InMaterialFabric;
use App\Models\InMaterialFabricDet;
use Illuminate\Support\Facades\Auth;
use App\Models\MarkerDetail;
use App\Models\InMaterialLokasi;
use App\Models\MutLokasiHeader;
use App\Models\MutLokasi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use DB;
use QrCode;
use DNS1D;
use PDF;

class MutLokasiController extends Controller
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
            $keywordQuery = "";

            if ($request->no_ws != 'ALL') {
                $where = " and no_ws = '" . $request->no_ws . "' ";
            }else{
                $where = "";
            }


            $dataMutlokas = DB::connection('mysql_sb')->select("select id,no_mut,tgl_mut,no_ws,deskripsi,CONCAT(created_by,' (',created_at, ') ') user_create,status,CONCAT(id,'-',no_mut,'-',tgl_mut,'-',no_ws,'-',deskripsi,'-',CONCAT(created_by,' (',created_at, ') ') ,'-',status) filter from whs_mut_lokasi_h where tgl_mut BETWEEN '".$request->tgl_awal."' and '".$request->tgl_akhir."' ".$where."  order by no_mut asc");


            return DataTables::of($dataMutlokas)->toJson();
        }

         $nows = DB::connection('mysql_sb')->select("select DISTINCT no_ws from whs_mut_lokasi_h");

        return view("mut-lokasi.mut-lokasi", ['nows' => $nows,"page" => "dashboard-warehouse"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $no_ws = DB::connection('mysql_sb')->select("select jd.kpno from (select * from bpb where bpbdate >= '2021-01-01' and LEFT(bpbno_int,2) = 'GK'  GROUP BY id_jo) a
        //          inner join (select ac.id_buyer, supplier buyer,ac.styleno,jd.id_jo, ac.kpno from jo_det jd
        //  inner join so on jd.id_so = so.id
        //  inner join act_costing ac on so.id_cost = ac.id
        //          inner join mastersupplier mb on ac.id_buyer = mb.id_supplier
        //  where jd.cancel = 'N'
        //  group by id_cost order by id_jo asc) jd on a.id_jo = jd.id_jo");
        $no_ws = DB::connection('mysql_sb')->select("select DISTINCT no_ws kpno from whs_lokasi_inmaterial where LEFT(no_dok,2) = 'GK'");
        $kode_gr = DB::connection('mysql_sb')->select("
            select CONCAT(kode,'/',bulan,tahun,'/',nomor) kode from (select 'MT' kode, DATE_FORMAT(CURRENT_DATE(), '%m') bulan, DATE_FORMAT(CURRENT_DATE(), '%y') tahun,if(MAX(no_mut) is null,'00001',LPAD(SUBSTR(MAX(no_mut),9,5)+1,5,0)) nomor from whs_mut_lokasi_h where MONTH(tgl_mut) = MONTH(CURRENT_DATE()) and YEAR(tgl_mut) = YEAR(CURRENT_DATE())) a");

        return view('mut-lokasi.create-mutlokasi', ['kode_gr' => $kode_gr,'no_ws' => $no_ws, 'page' => 'dashboard-warehouse']);
    }

    public function editmutlok($id)
    {

        $det_data = DB::connection('mysql_sb')->select("select a.id,id_item,kode_item,item_desc,a.no_ws,no_bpb,no_lot,no_roll,ROUND(qty_roll,2) qty_roll,ROUND(qty_mutasi,2) qty_mutasi,unit,a.rak_asal,rak_tujuan from whs_mut_lokasi a inner join whs_mut_lokasi_h b on b.no_mut = a.no_mut where a.status = 'Y' and b.id = '$id'");
        $sum_data = DB::connection('mysql_sb')->select("select count(id_item)jml from (select id_item,kode_item,item_desc,a.no_ws,no_bpb,no_lot,no_roll,ROUND(qty_roll,2) qty_roll,ROUND(qty_mutasi,2) qty_mutasi,unit,a.rak_asal,rak_tujuan from whs_mut_lokasi a inner join whs_mut_lokasi_h b on b.no_mut = a.no_mut where a.status = 'Y' and b.id = '$id') a");
        $d_header = DB::connection('mysql_sb')->select("select no_mut kode,tgl_mut,no_ws,rak_asal,deskripsi from whs_mut_lokasi_h where id = '$id'");
        $lokasi = DB::connection('mysql_sb')->table('whs_master_lokasi')->select('id', 'kode_lok')->where('status', '=', 'active')->get();

        return view('mut-lokasi.edit-mutlokasi', ['d_header' => $d_header,'det_data' => $det_data,'sum_data' => $sum_data, 'lokasi' => $lokasi, 'page' => 'dashboard-warehouse']);
    }

    public function updatemutlok(Request $request)
    {
        
        for ($i = 1; $i <= intval($request['txt_sum_roll']); $i++) {
                $updateInMaterialDet = MutLokasi::where('id', $request["id_det"][$i])->update([
                'qty_mutasi' => $request["qty_mut"][$i],
                'rak_tujuan' => $request["selectlok"][$i],
            ]);
            }

        $massage = 'Edit Data Successfully';

            return array(
                "status" => 200,
                "message" => $massage,
                "additional" => [],
                "redirect" => url('/mutasi-lokasi')
            );
        
    }

    public function getRakList(Request $request)
    {
//         $nomorrak = DB::connection('mysql_sb')->select("select DISTINCT br.id_rak_loc, kode_rak
// from bpb_roll br
// inner join bpb_roll_h brh on br.id_h = brh.id
// inner join masteritem mi on brh.id_item = mi.id_item
// inner join bpb on brh.bpbno = bpb.bpbno and brh.id_jo = bpb.id_jo and brh.id_item = bpb.id_item
// inner join mastersupplier ms on bpb.id_supplier = ms.Id_Supplier
// inner join jo_det jd on brh.id_jo = jd.id_jo
// inner join so on jd.id_so = so.id
// inner join act_costing ac on so.id_cost = ac.id
// inner join master_rak mr on br.id_rak_loc = mr.id
// where ac.kpno = '" . $request->no_ws . "' and LEFT(bpbno_int,2) = 'GK'  ");

        $nomorrak = DB::connection('mysql_sb')->select("select DISTINCT kode_lok kode_rak from whs_lokasi_inmaterial where no_ws = '" . $request->no_ws . "' and LEFT(no_dok,2) = 'GK' ");

        $html = "<option value=''>Pilih Rak</option>";

        foreach ($nomorrak as $norak) {
            $html .= " <option value='" . $norak->kode_rak . "'>" . $norak->kode_rak . "</option> ";
        }

        return $html;
    }


    public function getListroll(Request $request)
    {
        
        // $det_item = DB::connection('mysql_sb')->select("select br.id,mi.itemdesc, mi.id_item, goods_code, supplier, bpbno_int,pono,invno,ac.kpno,roll_no, roll_qty, lot_no, bpb.unit, kode_rak, CONCAT(mi.id_item,'-',goods_code,'-',mi.itemdesc,'-',ac.kpno,'-',bpbno_int,'-',bpb.unit) filter
        //     from bpb_roll br
        //     inner join bpb_roll_h brh on br.id_h = brh.id
        //     inner join masteritem mi on brh.id_item = mi.id_item
        //     inner join bpb on brh.bpbno = bpb.bpbno and brh.id_jo = bpb.id_jo and brh.id_item = bpb.id_item
        //     inner join mastersupplier ms on bpb.id_supplier = ms.Id_Supplier
        //     inner join jo_det jd on brh.id_jo = jd.id_jo
        //     inner join so on jd.id_so = so.id
        //     inner join act_costing ac on so.id_cost = ac.id
        //     inner join master_rak mr on br.id_rak_loc = mr.id
        //     where mr.kode_rak = '" . $request->rak . "' and ac.kpno = '" . $request->no_ws . "' and LEFT(bpbno_int,2) = 'GK' 
        //     group by br.id
        //     order by br.id");

        $det_item = DB::connection('mysql_sb')->select("select no_roll_buyer,id_jo,id,itemdesc, id_item, goods_code, supplier, bpbno_int,pono,invno,kpno,roll_no, roll_qty, lot_no, unit, kode_rak, filter from (select a.id_jo,a.id,a.item_desc itemdesc,a.id_item,a.kode_item goods_code,b.supplier,a.no_dok bpbno_int,b.no_po pono,b.no_invoice invno,a.no_ws kpno,a.no_roll roll_no,a.no_roll_buyer,a.qty_aktual roll_qty,a.no_lot lot_no,a.satuan unit,a.kode_lok kode_rak, CONCAT(a.id_item,'-',kode_item,'-',item_desc,'-',a.no_ws,'-',a.no_dok,'-',a.satuan) filter,COALESCE(c.qty_out,0) qty_out,(a.qty_aktual - COALESCE(c.qty_out,0)) qty_sisa from whs_lokasi_inmaterial a inner join whs_inmaterial_fabric b on b.no_dok = a.no_dok left join (select id_roll,sum(qty_out) qty_out from whs_bppb_det GROUP BY id_roll) c on c.id_roll = a.id where a.kode_lok = '" . $request->rak . "' and a.no_ws = '" . $request->no_ws . "' and LEFT(a.no_dok,2) = 'GK') a where a.qty_sisa > 0");

        $lokasi = DB::connection('mysql_sb')->table('whs_master_lokasi')->select('id', 'kode_lok')->where('status', '=', 'active')->get();
        
        $pilih_lokasi = " <option value='-'>Pilih Rak</option> ";
        $html = '';
            $jml_qty_sj = 0;
            $jml_qty_ak = 0;
            $x = 1;
        foreach ($lokasi as $lok) {
            $pilih_lokasi .= " <option value='" . $lok->kode_lok . "'>" . $lok->kode_lok . "</option> ";
        }
        foreach ($det_item as $detitem) {
            $html .= ' <tr>
                        <td >'.$detitem->id_item.' <input type="hidden" id="id_item'.$x.'" name="id_item['.$x.']" value="'.$detitem->id_item.'" / readonly></td>
                        <td >'.$detitem->goods_code.' <input type="hidden" id="kode_item'.$x.'" name="kode_item['.$x.']" value="'.$detitem->goods_code.'" / readonly></td>
                        <td >'.$detitem->itemdesc.' <input type="hidden" id="desk_item'.$x.'" name="desk_item['.$x.']" value="'.$detitem->itemdesc.'" / readonly></td>
                        <td >'.$detitem->kpno.' <input type="hidden" id="nows'.$x.'" name="nows['.$x.']" value="'.$detitem->kpno.'" / readonly></td>
                        <td >'.$detitem->bpbno_int.' <input type="hidden" id="no_bpb'.$x.'" name="no_bpb['.$x.']" value="'.$detitem->bpbno_int.'" / readonly></td>
                        <td >'.$detitem->lot_no.' <input type="hidden" id="lot_no'.$x.'" name="lot_no['.$x.']" value="'.$detitem->lot_no.'" / readonly></td>
                        <td >'.$detitem->roll_no.' <input type="hidden" id="roll_no'.$x.'" name="roll_no['.$x.']" value="'.$detitem->roll_no.'" / readonly></td>
                        <td >'.$detitem->roll_qty.' <input type="hidden" id="qty_roll'.$x.'" name="qty_roll['.$x.']" value="'.$detitem->roll_qty.'" / readonly></td>
                        <td><input style="width:100px;text-align:right;" class="form-control" type="text" id="qty_mut'.$x.'" name="qty_mut['.$x.']" value="" onkeyup="sum_qty_mut(this.value)" /></td>
                        <td >'.$detitem->unit.' <input type="hidden" id="unit'.$x.'" name="unit['.$x.']" value="'.$detitem->unit.'" / readonly></td>
                        <td >'.$detitem->kode_rak.' <input type="hidden" id="kode_rak'.$x.'" name="kode_rak['.$x.']" value="'.$detitem->kode_rak.'" / readonly></td>
                        <td ><select class="form-control select2lok" id="selectlok'.$x.'" name="selectlok['.$x.']" style="width: 150px;">
                                '.$pilih_lokasi.'
                             </select></td>
                        <td style="display: none">'.$detitem->filter.' <input type="hidden" id="idbpbdet'.$x.'" name="idbpbdet['.$x.']" value="'.$detitem->id.'" / readonly> <input type="hidden" id="id_jo'.$x.'" name="id_jo['.$x.']" value="'.$detitem->id_jo.'" / readonly> <input type="hidden" id="no_roll_buyer'.$x.'" name="no_roll_buyer['.$x.']" value="'.$detitem->no_roll_buyer.'" / readonly></td>
                       </tr>';
                       $x++;
        }

        return $html;
    }

    public function getSumroll(Request $request)
    {
        
        $det_item = DB::connection('mysql_sb')->select("select count(id) jml from (select id,itemdesc, id_item, goods_code, supplier, bpbno_int,pono,invno,kpno,roll_no, roll_qty, lot_no, unit, kode_rak, filter from (select a.id,a.item_desc itemdesc,a.id_item,a.kode_item goods_code,b.supplier,a.no_dok bpbno_int,b.no_po pono,b.no_invoice invno,a.no_ws kpno,a.no_roll roll_no,a.qty_aktual roll_qty,a.no_lot lot_no,a.satuan unit,a.kode_lok kode_rak, CONCAT(a.id_item,'-',kode_item,'-',item_desc,'-',a.no_ws,'-',a.no_dok,'-',a.satuan) filter,COALESCE(c.qty_out,0) qty_out,(a.qty_aktual - COALESCE(c.qty_out,0)) qty_sisa from whs_lokasi_inmaterial a inner join whs_inmaterial_fabric b on b.no_dok = a.no_dok left join (select id_roll,sum(qty_out) qty_out from whs_bppb_det GROUP BY id_roll) c on c.id_roll = a.id where a.kode_lok = '" . $request->rak . "' and a.no_ws = '" . $request->no_ws . "' and LEFT(a.no_dok,2) = 'GK') a where a.qty_sisa > 0) a");

        return $det_item;
    }


    public function getWSList(Request $request)
    {
        $nomorws = DB::connection('mysql_sb')->select("
                select ac.kpno,ms.supplier from bom_jo_global_item bom INNER JOIN jo_det jd on jd.id_jo = bom.id_jo INNER JOIN so on so.id = jd.id_so INNER JOIN act_costing ac on ac.id = so.id_cost INNER JOIN mastersupplier ms on ms.id_supplier = bom.id_supplier where ms.Supplier = '" . $request->txt_supp . "' GROUP BY ac.kpno
            ");

        $html = "<option value=''>Pilih WS</option>";

        foreach ($nomorws as $ws) {
            $html .= " <option value='" . $ws->kpno . "'>" . $ws->kpno . "</option> ";
        }

        return $html;
    }


    public function approvemutlok(Request $request)
    {
            $timestamp = Carbon::now();
            $updateLokasi = MutLokasiHeader::where('no_mut', $request['txt_nodok'])->update([
                'status' => 'Approved',
                'approved_by' => Auth::user()->name,
                'approved_date' => $timestamp,
            ]);
        
        $massage = 'Approved Data Successfully';

            return array(
                "status" => 200,
                "message" => $massage,
                "additional" => [],
                "redirect" => url('/mutasi-lokasi')
            );
        
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    if (intval($request['txt_sum_roll']) > 0) {
        $mutlokasiheader = MutLokasiHeader::create([
                'no_mut' => $request['txt_no_mut'],
                'tgl_mut' => $request['txt_tgl_mut'],
                'no_ws' => $request['txt_nows'],
                'rak_asal' => $request['txt_rak'],
                'deskripsi' => $request['txt_note'],
                'status' => 'Pending',
                'created_by' => Auth::user()->name,
            ]);

            $timestamp = Carbon::now();
            $nodok = $request['txt_no_mut'];
            $tgldok = $request['txt_tgl_mut'];
            $mutasilokasi = [];
            $lokasiMaterial = [];
            for ($i = 1; $i <= intval($request['txt_sum_roll']); $i++) {
            if ($request["qty_mut"][$i] > 0) {
                array_push($mutasilokasi, [
                    "no_mut" => $nodok,
                    "tgl_mut" => $tgldok,
                    "id_item" => $request["id_item"][$i],
                    "kode_item" => $request["kode_item"][$i],
                    "item_desc" => $request["desk_item"][$i],
                    "no_ws" => $request["nows"][$i],
                    "no_bpb" => $request["no_bpb"][$i],
                    "no_lot" => $request["lot_no"][$i],
                    "no_roll" => $request["roll_no"][$i],
                    "qty_roll" => $request["qty_roll"][$i],
                    "qty_mutasi" => $request["qty_mut"][$i],
                    "unit" => $request["unit"][$i],
                    "rak_asal" => $request["kode_rak"][$i],
                    "rak_tujuan" => $request["selectlok"][$i],
                    "status" => 'Y',
                    "idbpb_det" => $request["idbpbdet"][$i],
                    "created_at" => $timestamp,
                    "updated_at" => $timestamp,
                ]);

                $Qmut = DB::connection('mysql_sb')->select("select coalesce(sum(qty_mutasi),0) qty from whs_lokasi_inmaterial where id = '".$request["idbpbdet"][$i]."'");
                $qty_mutasi = $Qmut[0]->qty;
                $ttl_Qmut = $qty_mutasi + $request["qty_mut"][$i];


                $updateLokasi = InMaterialLokasi::where('id', $request["idbpbdet"][$i])->update([
                'qty_mutasi' => $ttl_Qmut,
                ]);

                $sql_barcode = DB::connection('mysql_sb')->select("select CONCAT('F',(if(kode is null,'19999',kode)  + 1)) kode from (select max(SUBSTR(no_barcode,2,10)) kode from whs_lokasi_inmaterial where no_barcode like '%F%') a");
            $barcode = $sql_barcode[0]->kode;

                $save_lokasi = InMaterialLokasi::create([
                    "no_barcode" => $barcode,
                    "no_dok" => $request["no_bpb"][$i],
                    "no_ws" => $request["nows"][$i],
                    "id_jo" => $request["id_jo"][$i],
                    "id_item" => $request["id_item"][$i],
                    "kode_item" => $request["kode_item"][$i],
                    "item_desc" => $request["desk_item"][$i],
                    "no_roll" => $request["roll_no"][$i],
                    "no_roll_buyer" => $request["no_roll_buyer"][$i],
                    "no_lot" => $request["lot_no"][$i],
                    "qty_sj" => $request["qty_mut"][$i],
                    "qty_aktual" => $request["qty_mut"][$i],
                    "satuan" => $request["unit"][$i],
                    "kode_lok" => $request["selectlok"][$i],
                    "status" => 'Y',
                    "no_mut" => $nodok,
                    "created_by" => Auth::user()->name,
                    "created_at" => $timestamp,
                    "updated_at" => $timestamp,
                ]);

            }
            }

            $inmaterialDetailStore = MutLokasi::insert($mutasilokasi);
            $inmaterialLokasiStore = InMaterialLokasi::insert($lokasiMaterial);


            $massage = $request['txt_no_mut'] . ' Saved Succesfully';
            $stat = 200;
    }else{
        $massage = ' Please Input Data';
        $stat = 400;
    }


            return array(
                "status" =>  $stat,
                "message" => $massage,
                "additional" => [],
                "redirect" => url('/mutasi-lokasi')
            );

    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stocker  $stocker
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Stocker  $stocker
     * @return \Illuminate\Http\Response
     */
    public function edit(Stocker $stocker)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stocker  $stocker
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stocker  $stocker
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stocker $stocker)
    {
        //
    }

  

    
}
