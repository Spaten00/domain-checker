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
                                <form method="POST" action="{{route('new-bill.store')}}">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <label for="bill_number"
                                                   class="col-sm-6 col-form-label">Rechnungsnummer:</label>
                                            <input id="bill_number" type="text" name="bill_number">
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
                                <form method="POST" action="{{route('new-contract.store')}}">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <label for="contract_number"
                                                   class="col-sm-6 col-form-label">Vertragsnummer:</label>
                                            <input id="contract_number" type="text" name="contract_number">
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
                    <td>{{$domain->name}}</td>
                    <td>{!! $domain->getStatusBadge() !!}</td>
                    {{--TODO create method and check if is already expired--}}
                    <td>{!! $domain->getCustomer() !!}</td>
                    <td>{!! $domain->getTanssEnd() !!}</td>
                    <td>{!! $domain->getRrpproxyEnd() !!}</td>
                    <td>{!! $domain->getRrpproxyRenewal() !!}</td>
                    <td><input id="contract-number-{{$domain->getContractId()}}" type="text"
                               value="{{$domain->getContractNumber()}}" disabled></td>
                    <td><input id="bill-number-{{$domain->getLastBillId()}}" type="text"
                               value="{{$domain->getLastBillNumber()}}" disabled></td>
                    {{--TODO put modal here to ask the user if he is sure to change the number--}}
                    <td><i class="fas fa-pencil-alt" role="button"
                           onclick="editValues([{{$domain->getContractId()}}, {{$domain->getLastBillId()}}])"></i>
                    </td>
                    <td>{{$domain->getLastBillDate()}}</td>
                    <td>
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
            function editValues(values) {
                let contractId = values[0];
                let billId = values[1];
                if (contractId) {
                    let contractInput = document.getElementById("contract-number-" + contractId);
                    contractInput.disabled = !contractInput.disabled;
                    if (contractInput.disabled) {
                        $.ajax({
                            url: '/contract/update/' + contractId,
                            method: 'POST',
                            data: {newNumber: contractInput.value},
                            success: function (response) {
                                if (response) {
                                    alert(response);
                                }
                            },
                            fail: function () {
                                alert('Fehler beim Ändern');
                            }
                        });
                    }
                }

                if (billId) {
                    let billInput = document.getElementById("bill-number-" + billId);
                    billInput.disabled = !billInput.disabled;
                    if (billInput.disabled) {
                        $.ajax({
                            url: '/bill/update/' + billId,
                            method: 'POST',
                            data: {newNumber: billInput.value},
                            success: function (response) {
                                if (response) {
                                    alert(response);
                                }
                            },
                            fail: function () {
                                alert('Fehler beim Ändern');
                            }
                        });
                    }
                }
            }

        </script>
    @endpush
</x-app-layout>
