<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TiReport extends Model {
    protected $fillable = ['object_id','type','year','period','scope_snapshot','aspect_snapshot','data','version','created_by','updated_by'];
    protected $casts = ['data'=>'array','year'=>'integer','period'=>'integer','version'=>'integer'];
    public function object() { return $this->belongsTo(TiObject::class,'object_id'); }
    public function creator() { return $this->belongsTo(User::class,'created_by')->withTrashed(); }
    public function editor() { return $this->belongsTo(User::class,'updated_by')->withTrashed(); }
}
