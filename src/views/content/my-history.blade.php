@extends('master::layouts/admin')

@section('content')
<a class="btn-site pull-right" href="{{ url('inicio') }}">Volver al Sitio Web</a>
<h1>Mi Historial de Pagos</h1>
@if(count($items)>0)
  @foreach($items as $customer_array)
    @include('customer::includes.member')
    @if(count($customer_array['payments'])>0)
      <table class="table">
        <thead>
          <tr class="title">
            <td>Código</td>
            <td>Fecha de Emisión</td>
            <td>Fecha y Hora de Pago</td>
            <td>Detalle</td>
            <td>Monto</td>
            <td>Acción</td>
          </tr>
        </thead>
        <tbody>
          @foreach($customer_array['payments'] as $payment)
            @foreach($payment->processed_transaction_payments as $transaction_payment)
              <tr>
                <td>{{ $transaction_payment->parent->payment_code }}</td>
                <td>{{ $payment->date }}</td>
                <td>{{ $payment->payment_date }}</td>
                <td>{{ $payment->name }}</td>
                <td>{{ $payment->currency->code.' '.$payment->real_amount }}</td>
                @if($payment->invoice_url)
                  <td class="restore"><a target="_blank" href="{{ $payment->invoice_url }}">Ver Factura</a></td>
                @else
                  <td class="restore">-</td>
                @endif
              </tr>
            @endforeach
          @endforeach
        </tbody>
      </table>
    @else
      <p>Actualmente no tiene pagos registrados en su cuenta.</p>
    @endif
  @endforeach
@else
  <p>No tiene una cuenta de miembro con pagos disponibles.</p>
@endif
@endsection