<?php

namespace App\Models;

use Database\Factories\DomainFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Domain
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $contract_id
 * @property string $domain_name
 * @property-read Collection|Contract[] $contracts
 * @method static DomainFactory factory(...$parameters)
 * @method static Builder|Domain newModelQuery()
 * @method static Builder|Domain newQuery()
 * @method static Builder|Domain query()
 * @method static Builder|Domain whereContractId($value)
 * @method static Builder|Domain whereCreatedAt($value)
 * @method static Builder|Domain whereDeletedAt($value)
 * @method static Builder|Domain whereDomainName($value)
 * @method static Builder|Domain whereId($value)
 * @method static Builder|Domain whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string $name
 * @property-read int|null $contracts_count
 * @method static Builder|Domain whereName($value)
 * @property-read RrpproxyEntry $rrpproxyEntry
 * @property-read int|null $rrpproxy_entries_count
 * @property-read TanssEntry $tanssEntry
 * @property-read int|null $tanss_entries_count
 * @property mixed $id
 */
class Domain extends Model
{
    use HasFactory;

    protected $table = 'domains';

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
     * Get the contract for the domain.
     *
     * @return BelongsToMany
     */
    public function contracts(): BelongsToMany
    {
        return $this->belongsToMany(Contract::class);
    }

    /**
     * Get the tanssEntries for the Domain.
     *
     * @return HasOne
     */
    public function tanssEntry(): HasOne
    {
        return $this->hasOne(TanssEntry::class);
    }

    /**
     * Get the rrpproxyEntries for the Domain.
     *
     * @return HasOne
     */
    public function rrpproxyEntry(): HasOne
    {
        return $this->hasOne(RrpproxyEntry::class);
    }

    // OTHER METHODS

    /**
     * Create a new domain with the desired name if it does not exist yet.
     *
     * @param string $domainName
     * @return Domain
     */
    public static function createDomain(string $domainName): Domain
    {
        $domain = self::where('name', $domainName)->first();
        if (!$domain) {
            $domain = new Domain;
            $domain->name = $domainName;
            $domain->save();
        }
        return $domain;
    }

    /**
     * Returns true if the domain has no TANSS-entry.
     *
     * @return bool
     */
    private function hasNoTanss(): bool
    {
        return !$this->tanssEntry;
    }

    /**
     * Returns true if the domain has no RRPproxy-entry.
     *
     * @return bool
     */
    private function hasNoRrpproxy(): bool
    {
        return !$this->rrpproxyEntry;
    }

    /**
     * Returns true if the domain has no TANSS-entry and no RRPproxy-entry.
     *
     * @return bool
     */
    private function hasNoEntries(): bool
    {
        return $this->hasNoTanss() && $this->hasNoRrpproxy();
    }

    /**
     * Returns true if the domain has an expired TANSS-entry.
     *
     * @return bool
     */
    private function hasTanssExpired(): bool
    {
        return $this->tanssEntry->isExpired();
    }

    /**
     * Returns true if the domain has an expired RRPproxy-entry.
     *
     * @return bool
     */
    private function hasRrpproxyExpired(): bool
    {
        return $this->rrpproxyEntry->isExpired();
    }

    /**
     * Returns true if the domain has an expired RRPproxy-entry and an expired TANSS-entry.
     *
     * @return bool
     */
    private function hasBothExpired(): bool
    {
        return $this->hasTanssExpired() && $this->hasRrpproxyExpired();
    }

    /**
     * Returns true if the domain has an expired RRPproxy-entry or an expired TANSS-entry.
     *
     * @return bool
     */
    private function hasEitherExpired(): bool
    {
        return $this->hasTanssExpired() || $this->hasRrpproxyExpired();
    }

    /**
     * Returns true if the domain has an expired RRPproxy-entry and an expired TANSS-entry.
     *
     * @return bool
     */
    private function hasEitherExpireSoon(): bool
    {
        return $this->tanssEntry->willExpireSoon() || $this->rrpproxyEntry->willExpireSoon();
    }

    /**
     * Returns an array containing two strings. The first string is the CSS-class and the second string is the text.
     *
     * @return array
     */
    private function getClassAndText(): array
    {
        if ($this->hasNoEntries()) {
            return ['badge bg-info', 'Keine Einträge'];
        }

        if ($this->hasNoTanss()) {
            if (!$this->hasRrpproxyExpired()) {
                return ['badge bg-danger', 'TANSS fehlt'];
            }
            return ['badge bg-success', 'OK'];
        }

        if ($this->hasNoRrpproxy()) {
            if (!$this->hasTanssExpired()) {
                return ['badge bg-danger', 'RRPproxy fehlt'];
            }
            return ['badge bg-danger', 'Kein Ablaufdatum hinterlegt'];
        }

        if ($this->hasBothExpired()) {
            return ['badge bg-success', 'OK'];
        }

        if ($this->hasEitherExpired()) {
            return ['badge bg-danger', 'Vertrag ausgelaufen'];
        }

        if ($this->hasEitherExpireSoon()) {
            return ['badge bg-warning', 'Vertrag läuft aus'];
        }

        return ['badge bg-success', 'OK'];
    }

    /**
     * Returns a string which builds an HTML-span element to be shown as a badge in the calling blade.
     *
     * @return string
     */
    public function getStatusBadge(): string
    {
        [$statusClass, $statusText] = $this->getClassAndText();
        return '<span class="' . $statusClass . '">' . $statusText . '</span>';
    }

    /**
     * Returns true if the domain has a customer.
     *
     * @return bool
     */
    public function hasCustomer(): bool
    {
        return $this->tanssEntry && $this->tanssEntry->customer;
    }

    /**
     * Returns the customer id or an empty string.
     *
     * @return string
     */
    public function getCustomerId(): string
    {
        if ($this->hasCustomer()) {
            return $this->tanssEntry->customer->getKey();
        }
        return '';
    }

    /**
     * Returns a string containing the customer of the domain or an HTML-span element with a badge class in the
     * calling blade if there is no customer.
     *
     * @return string
     */
    public function getCustomer(): string
    {
        if ($this->hasCustomer()) {
            return $this->tanssEntry->customer->name;
        }

        if ($this->hasBothExpired()) {
            return 'Kunde fehlt';
        }
        return '<span class="badge bg-danger">Kunde fehlt</span>';
    }

    /**
     * Returns a string containing the date on which the contract of the TANSS-entry will end. If there is no
     * TANSS-entry the method returns an HTML-span element with a badge class.
     *
     * @return string
     */
    public function getTanssEnd(): string
    {
        if ($this->tanssEntry) {
            if ($this->tanssEntry->contract_end) {
                $returnString = Carbon::parse($this->tanssEntry->contract_end)->format('d-m-Y');
            } else {
//                self::dd("asd");
                $returnString = "fehlt";
            }

            if ($this->tanssEntry->willExpireSoon() && !$this->tanssEntry->isExpired()) {
                $returnString = '<span class="badge bg-warning">' . $returnString . '</span>';
            } elseif ($this->rrpproxyEntry && $this->hasTanssExpired() && !$this->hasRrpproxyExpired()) {
                $returnString = '<span class="badge bg-danger">' . $returnString . '</span>';
            }

            return $returnString;
        }

        if ($this->rrpproxyEntry && $this->hasRrpproxyExpired()) {
            return "fehlt";
        }

        return '<span class="badge bg-danger">fehlt</span>';
    }

    /**
     * Returns a string containing the date on which the contract of the RRPproxy-entry will end. If there is no
     * RRPproxy-entry the method returns an HTML-span element with a badge class.
     *
     * @return string
     */
    public function getRrpproxyEnd(): string
    {
        if ($this->rrpproxyEntry) {
            return Carbon::parse($this->rrpproxyEntry->contract_end)->format('d-m-Y');
        }
        return '<span class="badge bg-danger">fehlt</span>';
    }

    /**
     * Returns a string containing the date on which the contract of the RRPproxy-entry will be renewed. If there is no
     * RRPproxy-entry the method returns an HTML-span element with a badge class.
     *
     * @return string
     */
    public function getRrpproxyRenewal(): string
    {
        if ($this->rrpproxyEntry) {
            return Carbon::parse($this->rrpproxyEntry->contract_renewal)->format('d-m-Y');
        }
        return '<span class="badge bg-danger">fehlt</span>';
    }

    /**
     * Returns the contract number or an empty string.
     *
     * @return string
     */
    public function getContractNumber(): string
    {
        return $this->contracts->last()->contract_number ?? '';
    }

    /**
     * Returns the contract id or an empty string.
     *
     * @return string
     */
    public function getContractId(): string
    {
        return $this->contracts->last()->id ?? '';
    }

    public function hasContract(): bool
    {
        return (bool)$this->contracts->last();
    }

    /**
     * Returns true if the domain has a bill.
     *
     * @return bool
     */
    public function hasBill(): bool
    {
        $lastContract = $this->contracts->last();
        if ($lastContract) {
            $lastBill = $lastContract->bills->last();
            if ($lastBill) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the billNumber of the latest bill for the domain or an empty string if it does not exist.
     *
     * @return string
     */
    public function getLastBillNumber(): string
    {
        if ($this->hasBill()) {
            return $this->contracts->last()->bills->last()->bill_number;
        }
        return '';
    }

    /**
     * Get the date of the last bill for the domain or an empty string if it does not exist.
     *
     * @return string
     */
    public function getLastBillDate(): string
    {
        if ($this->hasBill()) {
            return Carbon::parse($this->contracts->last()->bills->last()->date)->format('d-m-Y');
        }
        return '';
    }

    /**
     * Returns the last bill id or an empty string.
     *
     * @return string
     */
    public function getLastBillId(): string
    {
        if ($this->hasBill()) {
            return $this->contracts->last()->bills->last()->getKey();
        }
        return '';
    }
}
