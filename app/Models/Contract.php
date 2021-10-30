<?php

namespace App\Models;

use Database\Factories\ContractFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Contract
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $user_id
 * @property string $contract_number
 * @property-read Customer $customer
 * @property-read Collection|Domain[] $domains
 * @property-read int|null $domains_count
 * @property-read Collection|Hosting[] $hostings
 * @property-read int|null $hostings_count
 * @method static ContractFactory factory(...$parameters)
 * @method static Builder|Contract newModelQuery()
 * @method static Builder|Contract newQuery()
 * @method static Builder|Contract query()
 * @method static Builder|Contract whereContractNumber($value)
 * @method static Builder|Contract whereCreatedAt($value)
 * @method static Builder|Contract whereDeletedAt($value)
 * @method static Builder|Contract whereId($value)
 * @method static Builder|Contract whereUpdatedAt($value)
 * @method static Builder|Contract whereUserId($value)
 * @mixin Eloquent
 * @property int $customer_id
 * @method static Builder|Contract whereCustomerId($value)
 * @property-read Collection|Bill[] $bills
 * @property-read int|null $bills_count
 */
class Contract extends Model
{
    use HasFactory, LogsActivity;

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
     * Activity log.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['contract_number']);
    }

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
    /**
     * Get the bills for the contract.
     *
     * @return HasMany
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Get the customer for the contract.
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the domains for the contract.
     *
     * @return BelongsToMany
     */
    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(Domain::class);
    }

    /**
     * Get the hostings for the contract.
     *
     * @return BelongsToMany
     */
    public function hostings(): BelongsToMany
    {
        return $this->belongsToMany(Hosting::class);
    }

    // OTHER METHODS
}
