@extends('master::layouts/admin')

@section('content')
<a class="btn-site pull-right" href="{{ url('inicio') }}">Volver al Sitio Web</a>
<h1>Mis Pagos Pendientes</h1>
@if(count($items)>0)
  @foreach($items as $customer_array)
    @include('customer::includes.member')
    @if(count($customer_array['payments'])>0)
      <a href="{{ url('pagostt/make-all-payments/'.$customer_array['customer']->id) }}"><div class="btn btn-site">Pagar Todo por TodoTix</div></a>
      <table class="table">
        <thead>
          <tr class="title">
            <td>Detalle</td>
            <td>Fecha</td>
            <td>Monto</td>
            <td>Acción</td>
          </tr>
        </thead>
        <tbody>
          @foreach($customer_array['payments'] as $payment)
            <tr>
              <td>{{ $payment->name }}</td>
              <td>{{ $payment->date }}</td>
              <td>{{ $payment->currency->code.' '.$payment->real_amount }}</td>
              <td class="edit"><a href="{{ url('pagostt/make-single-payment/'.$customer_array['customer']->id.'/'.$payment->id) }}">Pagar</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <p>Actualmente no tiene pagos pendientes.</p>
    @endif
  @endforeach
@else
  <p>No tiene una cuenta de miembro con pagos disponibles.</p>
@endif
@endsection