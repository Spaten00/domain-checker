<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Entry;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    /**
     * Returns all domains as a pagination query.
     *
     * @param string $sortBy
     * @param string $sortType
     * @return Application|Factory|View
     */
    public function show(string $sortBy = "domains.name", string $sortType = "asc"): View|Factory|Application
    {
        $domains = Domain::select(['domains.id', 'domains.name'])
            ->leftJoin('tanss_entries', 'domains.id', '=', 'tanss_entries.domain_id')
            ->leftJoin('customers', 'customers.id', '=', 'tanss_entries.customer_id')
            ->leftJoin('rrpproxy_entries', 'domains.id', '=', 'rrpproxy_entries.domain_id')
            ->leftJoin('contract_domain', 'domains.id', '=', 'contract_domain.domain_id')
            ->leftJoin('contracts', 'contracts.id', '=', 'contract_domain.contract_id')
            ->leftJoin('bills', function ($leftJoin) {
                $leftJoin->on('contracts.id', '=', 'bills.contract_id')
                    ->where('bills.date');
            })
            ->orderBy($sortBy, $sortType)
            ->paginate(20, ['domains.id', 'domains.name'])->withQueryString();
        return view('home')->with('domains', $domains);
    }

    /**
     *
     * Returns all active domains as a pagination query, which are the domains with a running entry in
     * TANSS or RRPproxy.
     *
     * @return Application|Factory|View
     */
    public function showActive(): View|Factory|Application
    {
        $domains = Domain::whereHas('tanssEntry', function (Builder $tanssExpired) {
            $tanssExpired->where('contract_end', '>', now());
        })->orWhereHas('rrpproxyEntry', function (Builder $rrpproxyExpired) {
            $rrpproxyExpired->where('contract_end', '>', now());
        })
            ->paginate(20)->withQueryString();
        return view('home')->with('domains', $domains);
    }

    /**
     * Returns all domains with a TANNS-entry which will expire soon as a pagination query.
     *
     * @return Application|Factory|View
     */
    public function showExpiring(): View|Factory|Application
    {
        $domains = Domain::whereHas('tanssEntry', function (Builder $query) {
            $query->where('contract_end', '<', now()->addDays(Entry::SOON))
                ->where('contract_end', '>=', now());
        })->paginate(20)->withQueryString();
        return view('home')->with('domains', $domains);
    }

    /**
     * Returns all domains with incomplete data, which include:
     * 1. The domains which do not have entries in TANSS and RRPproxy.
     * 2. The domains which do not have a TANSS entry but a running contract in RRPproxy.
     * 3. The domains which do not have a RRPproxy entry but a running contract in TANSS.
     * 4. The domains which have a running contract in RRPproxy but not in TANSS.
     * 5. The domains which have a running contract in TANSS but not in RRPproxy.
     *
     * @return Application|Factory|View
     */
    public function showIncomplete(): View|Factory|Application
    {
        $domains = Domain::where(function (Builder $hasNoEntries) {
            $hasNoEntries->whereDoesntHave('tanssEntry')
                ->whereDoesntHave('rrpproxyEntry');
        })
            ->orWhere(function (Builder $hasNoTanssAndRrpproxyRunning) {
                $hasNoTanssAndRrpproxyRunning->whereDoesntHave('tanssEntry')
                    ->whereHas('rrpproxyEntry', function (Builder $rrpproxyRunning) {
                        $rrpproxyRunning->where('contract_end', '>', now());
                    });
            })
            ->orWhere(function (Builder $hasNoRrpproxyAndTanssRunning) {
                $hasNoRrpproxyAndTanssRunning->whereDoesntHave('rrpproxyEntry')
                    ->whereHas('tanssEntry', function (Builder $tanssRunning) {
                        $tanssRunning->where('contract_end', '>', now());
                    });
            })
            ->orWhere(function (Builder $hasRrrpproxyRunningAndTanssExpired) {
                $hasRrrpproxyRunningAndTanssExpired->whereHas('rrpproxyEntry', function (Builder $rrpproxyRunning) {
                    $rrpproxyRunning->where('contract_end', '>', now());
                })
                    ->whereHas('tanssEntry', function (Builder $tanssExpired) {
                        $tanssExpired->where('contract_end', '<', now());
                    });
            })
            ->orWhere(function (Builder $hasTanssRunningAndRrpproxyExpired) {
                $hasTanssRunningAndRrpproxyExpired->whereHas('rrpproxyEntry', function (Builder $rrpproxyExpired) {
                    $rrpproxyExpired->where('contract_end', '<', now());
                })
                    ->whereHas('tanssEntry', function (Builder $tanssRunning) {
                        $tanssRunning->where('contract_end', '>', now());
                    });
            })
            ->paginate(20)->withQueryString();
        return view('home')->with('domains', $domains);
    }

    public function showSearch(Request $request)
    {
        $search = $request->searchString;
        $domains = Domain::where('name', 'like', '%' . $search . '%')
            ->orWhereHas('tanssEntry', function (Builder $tanssQuery) use ($search) {
                $tanssQuery
//                    ->whereRaw('CAST(`contract_end` as CHAR) like "%' . $search . '%"')
                    ->whereHas('customer', function (Builder $customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->orWhereHas('contracts', function (Builder $contractQuery) use ($search) {
                $contractQuery->where('contract_number', 'like', '%' . $search . '%')
                    ->orWhereHas('bills', function (Builder $billQuery) use ($search) {
                        $billQuery->where('bill_number', 'like', '%' . $search . '%');
                    });
            })
//            ->orWhereHas('rrpproxyEntry', function (Builder $rrpproxyBuilder) use ($search) {
//                $rrpproxyBuilder->whereRaw('CAST(`contract_end` as CHAR) like "%' . $search . '%"')
//                    ->orWhereRaw('CAST(`contract_renewal` as CHAR) like "%' . $search . '%"');
//            })
            ->paginate(20)->withQueryString();
        return view('home')->with('domains', $domains);
    }
}
