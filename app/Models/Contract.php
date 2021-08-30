<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Contract
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $user_id
 * @property string $contract_number
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Domain[] $domains
 * @property-read int|null $domains_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Hosting[] $hostings
 * @property-read int|null $hostings_count
 * @method static \Database\Factories\ContractFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereContractNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereUserId($value)
 * @mixin \Eloquent
 */
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

    /**
     * Get the domains for the contract.
     *
     * @return HasMany
     */
    public function domains(): HasMany
    {
    // TODO maybe change to HasOneOrMany???
        return $this->hasMany(Domain::class);
    }

    /**
     * Get the hostings for the contract.
     *
     * @return HasMany
     */
    public function hostings(): HasMany
    {
        return $this->hasMany(Hosting::class);
    }

    // OTHER METHODS
}
