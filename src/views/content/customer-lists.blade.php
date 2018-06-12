@extends('master::layouts/admin')

@section('content')
<h1>Listado de Cliente</h1>
@if(count($items)>0)
  <table class="table">
    <thead>
      <tr class="title">
        <td>Nombre de Jugador</td>
        <td>Email</td>
        <td>CI</td>
        <td>Polera</td>
        <td>Teléfono</td>
        <td>Estado</td>
      </tr>
    </thead>
    <tbody>
      @foreach($customers as $customer)
        <tr>
          <td colspan="6"><strong>{{ $team->name }}</strong></td>
        </tr>
        @foreach($team->team_customers as $customer)
          @if($customer->customer)
          <tr>
            <td>{{ $customer->customer->full_name }}</td>
            <td>{{ $customer->customer->email }}</td>
            <td>{{ $customer->customer->ci_number.' '.$customer->customer->ci_expedition }}</td>
            <td>{{ $customer->shirt }}</td>
            <td>{{ $customer->customer->phone }}</td>
            @if($first = $customer->customer->paid_payments()->first())
              <td class="edit"><a target="_blank" href="{{ url('admin/model/customer-payment/view/'.$first->id.'/es') }}">Ver Pago</a></td>
            @else
              <td class="delete">No Pagado</td>
            @endif
          </tr>
          @else
          <tr><td colspan="6">Jugador no encontrado. Código: {{ $customer->id }}</td></tr>
          @endif
        @endforeach
      @endforeach
    </tbody>
  </table>
@else
  <p>No tiene una cuenta de miembro con pagos disponibles.</p>
@endif
@endsection