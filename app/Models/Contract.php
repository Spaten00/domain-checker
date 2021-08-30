<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    protected $table = 'contracts';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'customer_id',
        'contract_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [];

    // SCOPES

    // RELATIONS
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    //TODO maybe change to HasOneOrMany???
    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function hostings(): HasMany
    {
        return $this->hasMany(Hosting::class);
    }
    // OTHER METHODS
}
