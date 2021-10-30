<x-app-layout>
    <div class="fullscreen">
        <table class="table">
            <thead>
            <tr>
                <th>
                    <a href="{{ Request::is('sort/domains.name/asc')
                        ? route('sort', ['domains.name', 'desc'])
                        : route('sort', ['domains.name', 'asc'])}}">
                        Domain
                        @if(Request::is('sort/domains.name/desc'))
                            <i class="fas fa-sort-down"></i>
                        @elseif(Request::is('sort/domains.name/asc'))
                            <i class="fas fa-sort-up"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center">Status</th>
                <th>
                    <a href="{{Request::is('sort/customers.name/asc')
                        ? route('sort', ['customers.name', 'desc'])
                        : route('sort', ['customers.name', 'asc'])}}">
                        Kunde
                        @if(Request::is('sort/customers.name/desc'))
                            <i class="fas fa-sort-down"></i>
                        @elseif(Request::is('sort/customers.name/asc'))
                            <i class="fas fa-sort-up"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center">
                    <a href="{{Request::is('sort/tanss_entries.contract_end/asc')
                        ? route('sort', ['tanss_entries.contract_end', 'desc'])
                        : route('sort', ['tanss_entries.contract_end', 'asc'])}}">
                        Vertragsende TANSS
                        @if(Request::is('sort/tanss_entries.contract_end/desc'))
                            <i class="fas fa-sort-down"></i>
                        @elseif(Request::is('sort/tanss_entries.contract_end/asc'))
                            <i class="fas fa-sort-up"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center">
                    <a href="{{Request::is('sort/rrpproxy_entries.contract_end/asc')
                        ? route('sort', ['rrpproxy_entries.contract_end', 'desc'])
                        : route('sort', ['rrpproxy_entries.contract_end', 'asc'])}}">
                        Vertragsende RRPproxy
                        @if(Request::is('sort/rrpproxy_entries.contract_end/desc'))
                            <i class="fas fa-sort-down"></i>
                        @elseif(Request::is('sort/rrpproxy_entries.contract_end/asc'))
                            <i class="fas fa-sort-up"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center">
                    <a href="{{Request::is('sort/rrpproxy_entries.contract_renewal/asc')
                        ? route('sort', ['rrpproxy_entries.contract_renewal', 'desc'])
                        : route('sort', ['rrpproxy_entries.contract_renewal', 'asc'])}}">
                        Verlängerung RRPproxy
                        @if(Request::is('sort/rrpproxy_entries.contract_renewal/desc'))
                            <i class="fas fa-sort-down"></i>
                        @elseif(Request::is('sort/rrpproxy_entries.contract_renewal/asc'))
                            <i class="fas fa-sort-up"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center">
                    <a href="{{Request::is('sort/contracts.contract_number/asc')
                        ? route('sort', ['contracts.contract_number', 'desc'])
                        : route('sort', ['contracts.contract_number', 'asc'])}}">
                        Vertragsnummer
                        @if(Request::is('sort/contracts.contract_number/desc'))
                            <i class="fas fa-sort-down"></i>
                        @elseif(Request::is('sort/contracts.contract_number/asc'))
                            <i class="fas fa-sort-up"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center">
                    <a href="{{Request::is('sort/bills.bill_number/asc')
                        ? route('sort', ['bills.bill_number', 'desc'])
                        : route('sort', ['bills.bill_number', 'asc'])}}">
                        letzte Rechnungsnummer
                        @if(Request::is('sort/bills.bill_number/desc'))
                            <i class="fas fa-sort-down"></i>
                        @elseif(Request::is('sort/bills.bill_number/asc'))
                            <i class="fas fa-sort-up"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center">bearbeiten</th>
                <th class="text-center">
                    <a href="{{Request::is('sort/bills.date/asc')
                        ? route('sort', ['bills.date', 'desc'])
                        : route('sort', ['bills.date', 'asc'])}}">
                        letzte Rechnung am
                        @if(Request::is('sort/bills.date/desc'))
                            <i class="fas fa-sort-down"></i>
                        @elseif(Request::is('sort/bills.date/asc'))
                            <i class="fas fa-sort-up"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center">neue Rechnung</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($domains as $domain)
                {{--Modal--}}
                <div class="modal fade" id="bill-number-modal-{{$domain->getKey()}}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            @if($domain->hasContract())
                                <div class="modal-header">
                                    <h5 class="modal-title">Neue Rechnungsnummer
                                        für {{$domain->name}} eingeben</h5>
                                    <button type="button" class="btn-close" data-dismiss="modal"
                                            aria-label="Close">X
                                    </button>
                                </div>
                                <form method="POST" action="{{route('bill.store')}}">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <label for="bill_number"
                                                   class="col-sm-6 col-form-label">Rechnungsnummer:</label>
                                            <input id="bill_number" type="text" name="bill_number" autofocus>
                                        </div>
                                        <div class="form-group row">
                                            <label for="date" class="col-sm-6 col-form-label">Datum der
                                                Rechnung:</label>
                                            <input id="date" type="date" name="date" value="{{date("Y-m-d")}}">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen
                                        </button>
                                        <button type="submit" class="btn btn-primary">Speichern</button>
                                    </div>
                                    <input type="hidden" name="contract_id" value="{{$domain->getContractId()}}">
                                </form>
                            @elseif(!$domain->hasCustomer())
                                <div class="modal-header">
                                    <h5 class="modal-title">Kein Kunde für {{$domain->name}} vorhanden, bitte Kunden im
                                        TANSS hinterlegen und Importfunktion starten.</h5>
                                    <button type="button" class="btn-close" data-dismiss="modal"
                                            aria-label="Close">X
                                    </button>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen
                                    </button>
                                </div>
                            @else
                                <div class="modal-header">
                                    <h5 class="modal-title">Noch kein Vertrag für {{$domain->name}} vorhanden, bitte
                                        erst Vertrag erstellen</h5>
                                    <button type="button" class="btn-close" data-dismiss="modal"
                                            aria-label="Close">X
                                    </button>
                                </div>
                                <form method="POST" action="{{route('contract.store')}}">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <label for="contract_number"
                                                   class="col-sm-6 col-form-label">Vertragsnummer:</label>
                                            <input id="contract_number" type="text" name="contract_number" autofocus>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen
                                        </button>
                                        <button type="submit" class="btn btn-primary">Speichern</button>
                                    </div>
                                    <input type="hidden" name="customer_id" value="{{$domain->getCustomerId()}}">
                                    <input type="hidden" name="domain_id" value="{{$domain->getKey()}}">
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{--Table data--}}
                <tr>
                    <td class="align-middle">{{$domain->name}}</td>
                    <td class="align-middle text-center">{!! $domain->getStatusBadge() !!}</td>
                    {{--TODO create method and check if is already expired--}}
                    <td class="align-middle">{!! $domain->getCustomer() !!}</td>
                    <td class="align-middle text-center">{!! $domain->getTanssEnd() !!}</td>
                    <td class="align-middle text-center">{!! $domain->getRrpproxyEnd() !!}</td>
                    <td class="align-middle text-center">{!! $domain->getRrpproxyRenewal() !!}</td>
                    <td class="align-middle text-center"><input id="contract-number-{{$domain->getContractId()}}"
                                                                type="text"
                                                                value="{{$domain->getContractNumber()}}" disabled></td>
                    <td class="align-middle text-center"><input id="bill-number-{{$domain->getLastBillId()}}"
                                                                type="text"
                                                                value="{{$domain->getLastBillNumber()}}" disabled></td>
                    {{--TODO put modal here to ask the user if he is sure to change the number--}}
                    <td class="align-middle text-center">
                        @if(Auth::check())
                            <i class="fas fa-pencil-alt" role="button"
                               onclick="editValues([{{$domain->getContractId()}}, {{$domain->getLastBillId()}}])"></i>
                        @else <a href="{{route('login')}}"><i class="fas fa-pencil-alt text-dark"></i></a>

                        @endif
                    </td>
                    <td class="align-middle text-center">{{$domain->getLastBillDate()}}</td>
                    <td class="align-middle text-center">
                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#bill-number-modal-{{$domain->getKey()}}">erstellen
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
    @push('footer_js')
        <script>
            async function editValues(values) {
                let promises = [];
                let contractId = values[0];
                let billId = values[1];
                let message = '';

                if (contractId) {
                    let contractInput = document.getElementById("contract-number-" + contractId);
                    contractInput.disabled = !contractInput.disabled;
                    if (contractInput.disabled) {
                        promises.push($.ajax({
                            url: '/contract/update/' + contractId,
                            method: 'POST',
                            data: {newNumber: contractInput.value},
                        }));
                    }
                }

                if (billId) {
                    let billInput = document.getElementById("bill-number-" + billId);
                    billInput.disabled = !billInput.disabled;
                    if (billInput.disabled) {
                        promises.push($.ajax({
                            url: '/bill/update/' + billId,
                            method: 'POST',
                            data: {newNumber: billInput.value},
                        }))
                    }
                }

                await Promise.all(promises).then(responseList => {
                    if (responseList.length > 0) {
                        for (let i = 0; i < responseList.length; i++) {
                            message += responseList[i];
                        }
                        alert(message);
                    }
                });
            }

        </script>
    @endpush
</x-app-layout>
