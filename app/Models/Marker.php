<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    use HasFactory;

    protected $table = 'marker_input';

    protected $guarded = [];

    /**
     * Get the marker details for the marker.
     */
    public function markerDetails()
    {
        return $this->hasMany(MarkerDetail::class, 'marker_input_kode', 'kode');
    }

    public function formCutInputs()
    {
        return $this->hasMany(FormCutInput::class, 'marker_input_kode', 'kode');
    }
}
