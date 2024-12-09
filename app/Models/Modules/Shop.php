<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperShop
 */
class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uniqid',
        'name',
        'description',
        'location',
        'price',
        'usable',
        'npc_id',
        'usable',
        'is_visible',
        'created_by',
        'image'
    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function consumables(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Consumable::class)->withPivot(
            'quantity',
            'price',
            'comment'
        );
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Item::class)->withPivot(
            'quantity',
            'price',
            'comment'
        );
    }

    public function ressources(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Ressource::class)->withPivot(
            'quantity',
            'price',
            'comment'
        );
    }

    public function npc(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Npc::class);
    }

    public function campaigns(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Campaign::class);
    }

    public function scenarios(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Scenario::class);
    }

    public function imagePath(): string
    {
        return Storage::disk('modules')->url($this->image);
    }
}
