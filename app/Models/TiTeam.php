<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TiTeam extends Model {
    protected $fillable = ['name','leader_user_id'];
    public function leader() { return $this->belongsTo(User::class,'leader_user_id'); }
    public function users() { return $this->belongsToMany(User::class,'ti_team_user','team_id','user_id'); }
    public function objects() { return $this->belongsToMany(TiObject::class,'ti_object_team','team_id','object_id'); }
}
