<x-app-layout>
    <div class="fullscreen">
        <table class="table">
            <thead>
            <th>Domain</th>
            <th>Status</th>
            <th>Kunde</th>
            <th>Vertragsende TANSS</th>
            <th>Vertragsende RRPproxy</th>
            <th>Verlängerung RRPproxy</th>
            <th>Vertragsnummer</th>
            <th>letzte Rechnung</th>
            <th>bearbeiten</th>
            </thead>
            <tbody>
            @foreach (\App\Models\Domain::all() as $domain)
                <tr>
                    <td>{{$domain->name}}</td>
                    <td><span class="badge bg-success">OK</span></td>
                    <td>{{$domain->tanssEntry ? $domain->tanssEntry->customer->name : ''}}</td>
                    <td>{{$domain->tanssEntry ? Carbon\Carbon::parse($domain->tanssEntry->contract_end)->toDateString() : ''}}</td>
                    <td>{{$domain->rrpproxyEntry ? Carbon\Carbon::parse($domain->rrpproxyEntry->contract_end)->toDateString() : ''}}</td>
                    <td>{{$domain->rrpproxyEntry ? Carbon\Carbon::parse($domain->rrpproxyEntry->contract_renewal)->toDateString() : ''}}</td>
                    <td><input type="text" placeholder="asd" disabled></td>
                    <td><input type="text" placeholder="alte Rechnung" disabled></td>
                    <td><i class="fas fa-pencil-alt"></i>
                        <span class="fa-stack fa-2x">
  <i class="far fa-circle fa-stack-2x"></i>
  <i class="fas fa-lock fa-stack-1x "></i>
</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
