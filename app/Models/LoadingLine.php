<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SignalBit\UserLine;

class LoadingLine extends Model
{
    use HasFactory;

    protected $table = "loading_line";

    protected $guarded = [];

    /**
     * Get the userline.
     */
    public function userLine() {
        return $this->belongsTo(UserLine::class, 'line_id', 'line_id');
    }

    /**
     * Get the stocker.
     */
    public function stocker() {
        return $this->belongsTo(Stocker::class, 'stocker_id', 'id');
    }
}
