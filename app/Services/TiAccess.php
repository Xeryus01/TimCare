<?php
namespace App\Services;
use App\Models\{User,TiObject};
use Illuminate\Database\Eloquent\Builder;
class TiAccess {
    // Deliberately no Admin override for report editing.
    public static function canEdit(User $user, TiObject $object): bool {
        return $object->is_active && ((int)$object->pic_user_id === (int)$user->id
            || $object->teams()->where('leader_user_id',$user->id)->exists());
    }
    public static function canView(User $user, TiObject $object): bool {
        return $user->hasRole('Admin') || (int)$object->pic_user_id === (int)$user->id
            || $object->teams()->where(function ($q) use ($user) {
                $q->where('leader_user_id',$user->id)->orWhereHas('users', fn ($q) => $q->where('users.id',$user->id));
            })->exists();
    }
    public static function editable(Builder $q, User $user): Builder {
        return $q->where('is_active',true)->where(function ($q) use ($user) {
            $q->where('pic_user_id',$user->id)->orWhereHas('teams',fn($q)=>$q->where('leader_user_id',$user->id));
        });
    }
    public static function visible(Builder $q, User $user): Builder {
        if ($user->hasRole('Admin')) return $q;
        return $q->where(fn ($q) => $q->where('pic_user_id',$user->id)->orWhereHas('teams', function ($q) use ($user) {
            $q->where('leader_user_id',$user->id)->orWhereHas('users', fn ($q) => $q->where('users.id',$user->id));
        }));
    }
}
