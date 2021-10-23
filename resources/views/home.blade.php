<x-app-layout>
    <div class="fullscreen">
        <table class="table">
            <thead>
            <tr>
                <th>Domain</th>
                <th>Status</th>
                <th>Kunde</th>
                <th>Vertragsende TANSS</th>
                <th>Vertragsende RRPproxy</th>
                <th>Verlängerung RRPproxy</th>
                <th>Vertragsnummer</th>
                <th>letzte Rechnungsnummer</th>
                <th>bearbeiten</th>
                <th>letzte Rechnung am</th>
                <th>neue Rechnung</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($domains as $domain)
                {{--Modal--}}
                <div class="modal fade" id="bill-number-modal-{{$domain->id}}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Neue Rechnungsnummer
                                    für {{$domain->name}} eingeben</h5>
                                <button type="button" class="btn-close" data-dismiss="modal"
                                        aria-label="Close">X
                                </button>
                            </div>
                            <form method="POST" action="{{route('new-bill.store')}}">
                                @csrf
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label for="bill_number"
                                               class="col-sm-6 col-form-label">Rechnungsnummer:</label>
                                        <input id="bill_number" type="text" name="bill_number">
                                    </div>
                                    <div class="form-group row">
                                        <label for="date" class="col-sm-6 col-form-label">Datum der Rechnung:</label>
                                        <input id="date" type="text" name="date">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen
                                    </button>
                                    <button type="submit" class="btn btn-primary">Speichern</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{--Table data--}}
                <tr>
                    <td>{{$domain->name}}</td>
                    <td>{!! $domain->getStatusBadge() !!}</td>
                    <td>{!! $domain->tanssEntry ? $domain->tanssEntry->customer->name : '<span class="badge bg-danger">Kunde fehlt</span>' !!}</td>
                    {{--                    <td>{{$domain->tanssEntry ? Carbon\Carbon::parse($domain->tanssEntry->contract_end)->toDateString() : ''}}</td>--}}
                    {{--                    <td>{{$domain->rrpproxyEntry ? Carbon\Carbon::parse($domain->rrpproxyEntry->contract_end)->toDateString() : ''}}</td>--}}
                    {{--                    <td>{{$domain->rrpproxyEntry ? Carbon\Carbon::parse($domain->rrpproxyEntry->contract_renewal)->toDateString() : ''}}</td>--}}
                    <td>{{$domain->getTanssEnd()}}</td>
                    <td>{{$domain->getRrpproxyEnd()}}</td>
                    <td>{{$domain->getRrpproxyRenewal()}}</td>
                    <td><input type="text" placeholder="asd" disabled></td>
                    <td><input type="text" placeholder="alte Rechnung" disabled></td>
                    <td><i class="fas fa-pencil-alt"></i></td>
                    <td>{{$domain->getLastBillDate()}}</td>
                    <td>
                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#bill-number-modal-{{$domain->id}}">erstellen
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            {{ $domains->links() }}
        </div>
    </div>
</x-app-layout>
