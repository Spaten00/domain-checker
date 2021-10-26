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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Returns all domains as a pagination query.
     *
     * @return Application|Factory|View
     */
    public function show(): View|Factory|Application
    {
        return view('home')->with('domains', Domain::paginate(20)->withQueryString());
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
        })->paginate(20);
        return view('home')->with('domains', $domains);
    }

    // TODO
    public function showIncomplete()
    {

    }

    public function showSearch(string $searchString)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function edit(Domain $domain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Domain $domain)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function destroy(Domain $domain)
    {
        //
    }
}
