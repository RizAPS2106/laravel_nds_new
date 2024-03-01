<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stocker extends Model
{
    use HasFactory;

    protected $table = 'stocker_input';

    protected $guarded = [];

    /**
     * Get the part.
     */
    public function partDetail()
    {
        return $this->belongsTo(PartDetail::class, 'part_detail_id', 'id');
    }

    /**
     * Get the form.
     */
    public function formCut()
    {
        return $this->belongsTo(FormCutInput::class, 'form_cut_id', 'id');
    }

    /**
     * Get the stocker details.
     */
    public function stockerDetails()
    {
        return $this->hasMany(StockerDetail::class, 'stocker_id', 'id');
    }

    /**
     * Get the stocker rack.
     */
    public function rackDetailStockers()
    {
        return $this->hasMany(RackDetailStocker::class, 'stocker_id', 'id_qr_stocker');
    }

    /**
     * Get the stocker trolley.
     */
    public function trolleyStockers()
    {
        return $this->hasMany(TrolleyStocker::class, 'stocker_id', 'id');
    }
}
