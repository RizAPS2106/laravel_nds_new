<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

// class ExportLaporanPemakaian implements FromCollection
// {
//     /**
//      * @return \Illuminate\Support\Collection
//      */
//     public function collection()
//     {
//         return Marker::all();
//     }
// }

class ExportLaporanPemasukan implements FromView, WithEvents, ShouldAutoSize
{
    use Exportable;


    protected $from, $to;

    public function __construct($from, $to)
    {

        $this->from = $from;
        $this->to = $to;
        $this->rowCount = 0;
    }


    public function view(): View

    {
        $data = DB::connection('mysql_sb')->select("select a.no_dok bpbno,a.tgl_dok bpbdate,no_invoice invno,type_bc jenis_dok,right(no_aju,6) no_aju,tgl_aju, lpad(no_daftar,6,'0') bcno,tgl_daftar bcdate,a.supplier,a.no_po pono,z.tipe_com,no_invoice invno,b.id_item,goods_code,concat(itemdesc,' ',add_info) itemdesc,s.color,s.size, (b.qty_good + coalesce(b.qty_reject,0)) qty,b.qty_good as qty_good,coalesce(b.qty_reject,0) as qty_reject, b.unit,'' berat_bersih,a.deskripsi remark,CONCAT(a.created_by,' (',a.created_at, ') ') username,CONCAT(a.approved_by,' (',a.approved_date, ') ') confirm_by,tmpjo.kpno ws,tmpjo.styleno,b.curr,if(z.tipe_com !='Regular','0',b.price)price, a.type_pch jenis_trans,'' reffno,lr.rak,cp.nama_panel,cc.color_gmt from whs_inmaterial_fabric a 
inner join whs_inmaterial_fabric_det b on b.no_dok = a.no_dok
inner join masteritem s on b.id_item=s.id_item 
left join (select no_dok,id_jo,id_item, CONCAT(kode_lok,' FABRIC WAREHOUSE RACK') rak from whs_lokasi_inmaterial  where status = 'Y' group by no_dok,id_jo,id_item) lr on b.no_dok = lr.no_dok and b.id_item = lr.id_item and b.id_jo = lr.id_jo 
LEFT join (select pono,tipe_com from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft where po_header.app = 'A') z on a.no_po = z.pono 
left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo 
left join (select id_jo,bom_jo_item.id_item,group_concat(distinct(nama_panel)) nama_panel from bom_jo_item inner join masterpanel mp on bom_jo_item.id_panel = mp.id where id_panel != '0' group by id_item, id_jo) cp on s.id_gen = cp.id_item and b.id_jo = cp.id_jo 
left join (select id_item, id_jo, group_concat(distinct(color)) color_gmt from bom_jo_item k inner join so_det sd on k.id_so_det = sd.id where status = 'M' and k.cancel = 'N' group by id_item, id_jo) cc on s.id_gen = cc.id_item and b.id_jo = cc.id_jo 
where left(a.no_dok,2) ='GK' and a.tgl_dok >= '" . $this->from . "' and a.tgl_dok <= '" . $this->to . "' and matclass= 'FABRIC' and b.status != 'N' and a.status != 'cancel' order by bpbno");



        // $data = Marker::orderBy('tgl_cutting', 'asc')->get();
        $this->rowCount = count($data) + 3;


        return view('lap-det-pemasukan.export', [
            'data' => $data,
            'from' => $this->from,
            'to' => $this->to
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => [self::class, 'afterSheet']
        ];
    }



    public static function afterSheet(AfterSheet $event)
    {

        $event->sheet->styleCells(
            'A3:AJ' . $event->getConcernable()->rowCount,
            [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ]
        );
    }
}
