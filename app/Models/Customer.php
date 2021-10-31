<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Customer
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string $name
 * @property-read Collection|Contract[] $contracts
 * @property-read int|null $contracts_count
 * @method static CustomerFactory factory(...$parameters)
 * @method static Builder|Customer newModelQuery()
 * @method static Builder|Customer newQuery()
 * @method static Builder|Customer query()
 * @method static Builder|Customer whereCreatedAt($value)
 * @method static Builder|Customer whereDeletedAt($value)
 * @method static Builder|Customer whereId($value)
 * @method static Builder|Customer whereName($value)
 * @method static Builder|Customer whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string $number
 * @property mixed $tanssEntries
 * @method static Builder|Customer whereNumber($value)
 */
class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
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
    /**
     * Get contracts for the customer.
     *
     * @return HasMany
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get TANSS-Entries for the customer.
     *
     * @return HasMany
     */
    public function tanssEntries(): HasMany
    {
        return $this->hasMany(TanssEntry::class);
    }
    // OTHER METHODS

    /**
     * Create a new user with the desired id and name if it does not exist yet.
     *
     * @param int $id
     * @param string $name
     * @return Customer
     */
    public static function createCustomer(int $id, string $name): Customer
    {
        $customer = self::find($id);
        if (!$customer) {
            $customer = new Customer;
            $customer->id = $id;
            $customer->name = $name;
            $customer->save();
        }
        return $customer;
    }
}
