<x-app-layout>
    <div class="fullscreen">
        <table class="table">
            <thead>
            <tr>
                <th><a href="{{route('sort', 'domains.name')}}">Domain</a></th>
                <th class="text-center">Status</th>
                <th>
                    <a href="{{route('sort', 'customers.name')}}">Kunde</a>
                </th>
                <th class="text-center">
                    <a href="{{route('sort', 'tanss_entries.contract_end')}}">Vertragsende TANSS</a>
                </th>
                <th class="text-center">
                    <a href="{{route('sort', 'rrpproxy_entries.contract_end')}}">Vertragsende RRPproxy</a>
                </th>
                <th class="text-center">
                    <a href="{{route('sort', 'rrpproxy_entries.contract_renewal')}}">Verlängerung RRPproxy</a>
                </th>
                <th class="text-center">
                    <a href="{{route('sort', 'contracts.contract_number')}}">Vertragsnummer</a>
                </th>
                <th class="text-center">
                    <a href="{{route('sort', 'bills.bill_number')}}">letzte Rechnungsnummer</a>
                </th>
                <th class="text-center">bearbeiten</th>
                <th class="text-center">
                    <a href="{{route('sort', 'bills.date')}}">letzte Rechnung am</a>
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
