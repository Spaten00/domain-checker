<?php

namespace App\Models;

use Database\Factories\BillFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Bill
 *
 * @method static BillFactory factory(...$parameters)
 * @method static Builder|Bill newModelQuery()
 * @method static Builder|Bill newQuery()
 * @method static Builder|Bill query()
 * @mixin Eloquent
 * @property-read Contract $contract
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $contract_id
 * @property string $bill_number
 * @property int $sent
 * @method static Builder|Bill whereBillNumber($value)
 * @method static Builder|Bill whereContractId($value)
 * @method static Builder|Bill whereCreatedAt($value)
 * @method static Builder|Bill whereDeletedAt($value)
 * @method static Builder|Bill whereId($value)
 * @method static Builder|Bill whereSent($value)
 * @method static Builder|Bill whereUpdatedAt($value)
 */
class Bill extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'bills';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'contract_id',
        'bill_number',
        'date',
    ];

    /**
     * Activity log.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['bill_number']);
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
     * Get the contract for the bill.
     *
     * @return BelongsTo
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    // OTHER METHODS
}
