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
                <th>letzte Rechnung</th>
                <th>bearbeiten</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($domains = \App\Models\Domain::paginate(15) as $domain)
                <tr>
                    <td>{{$domain->name}}</td>
                    <td><span class="badge bg-success">OK</span></td>
                    <td>{{$domain->tanssEntry ? $domain->tanssEntry->customer->name : ''}}</td>
                    <td>{{$domain->tanssEntry ? Carbon\Carbon::parse($domain->tanssEntry->contract_end)->toDateString() : ''}}</td>
                    <td>{{$domain->rrpproxyEntry ? Carbon\Carbon::parse($domain->rrpproxyEntry->contract_end)->toDateString() : ''}}</td>
                    <td>{{$domain->rrpproxyEntry ? Carbon\Carbon::parse($domain->rrpproxyEntry->contract_renewal)->toDateString() : ''}}</td>
                    <td><input type="text" placeholder="asd" disabled></td>
                    <td><input type="text" placeholder="alte Rechnung" disabled></td>
                    <td><i class="fas fa-pencil-alt"></i></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            {!! $domains->links() !!}
        </div>
    </div>
</x-app-layout>
