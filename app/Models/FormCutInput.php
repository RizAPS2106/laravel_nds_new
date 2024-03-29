<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\ThisYearScope;

class FormCutInput extends Model
{
    use HasFactory;

    protected $table = 'form_cut_input';

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ThisYearScope);
    }

    public function marker()
    {
        return $this->belongsTo(Marker::class, 'marker_input_kode', 'kode');
    }

    public function alokasiMeja()
    {
        return $this->belongsTo(User::class, 'meja_id', 'id');
    }

    public function cuttingPlan()
    {
        return $this->hasOne(CutPlan::class, 'no_form', 'no_form');
    }

    public function formCutInputDetails()
    {
        return $this->hasMany(FormCutInputDetail::class, 'no_form', 'no_form');
    }
}
