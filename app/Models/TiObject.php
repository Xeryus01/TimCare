<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TiObject extends Model {
    protected $fillable = ['code','name','scope','aspect','manager_label','audience','technical_type','programmer_label','pic_user_id','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function pic() { return $this->belongsTo(User::class,'pic_user_id'); }
    public function teams() { return $this->belongsToMany(TiTeam::class,'ti_object_team','object_id','team_id'); }
    public function reports() { return $this->hasMany(TiReport::class,'object_id'); }
}
