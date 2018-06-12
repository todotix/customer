@extends('master::layouts/admin')

@section('content')
<h1>Listado de Pagos de Jugadores</h1>
<?php $total = 0; ?>
{!! Form::open(['url'=>request()->url(), 'method'=>'GET', 'class'=>'form-horizontal filter', 'id'=>'filter']) !!}
  <input type="hidden" name="search" value="1" />
  <div class="row">
    {!! Field::form_input(NULL, $dt, ['name'=>'f_team_id','type'=>'select','options'=>$f_teams], ['label'=>'Filtrar Por Equipo', 'cols'=>3]) !!}
    {!! Field::form_input(NULL, $dt, ['name'=>'f_status','type'=>'select','options'=>$f_status], ['label'=>'Filtrar por Estado', 'cols'=>3]) !!}
    {!! Field::form_input(NULL, $dt, ['name'=>'f_from','type'=>'date'], ['label'=>'Filtrar por Fecha (Desde)', 'class'=>'datepicker', 'cols'=>3]) !!}
    {!! Field::form_input(NULL, $dt, ['name'=>'f_to','type'=>'date'], ['label'=>'Filtrar por Fecha (Hasta)', 'class'=>'datepicker', 'cols'=>3]) !!}
  </div>
{!! Form::close() !!}
@if(count($items)>0)
  <table class="admin-table editable-list table table-striped table-bordered table-hover dt-responsive">
    <thead>
      <tr class="title">
        <td>Nombre de Jugador</td>
        <td>Equipo</td>
        <td>Email</td>
        <td>Teléfono</td>
        <td>Fecha</td>
        <td>Monto en Bs.</td>
        <td>Estado</td>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $customer_payment)
        @if($customer_payment&&$customer_payment->customer)
        <tr>
          <td>{{ $customer_payment->customer->full_name }}</td>
          @if($customer_payment->customer->team_customer&&$customer_payment->customer->team_customer->team)
          <td>{{ $customer_payment->customer->team_customer->team->name }} ({{ $customer_payment->customer->team_customer->tournament->name }})</td>
          @else
          <td>-</td>
          @endif
          <td>{{ $customer_payment->customer->email }}</td>
          <td>{{ $customer_payment->customer->phone }}</td>
          <td>{{ $customer_payment->created_at->format('d/m/Y') }}</td>
          <td>{{ $customer_payment->amount }}</td>
          <td>
            @if($customer_payment->status=='pending')
              <a target="_blank" title="Pagar ahora" href="{{ url('pagostt/make-single-payment/'.$customer_payment->customer_id.'/'.$customer_payment->id) }}">
            @endif
            {{ trans('master::admin.'.$customer_payment->status) }}
            @if($customer_payment->status=='pending')
              </a>
            @endif
          </td>
        </tr>
        <?php if($customer_payment->status=='paid'){ $total += $customer_payment->amount; } ?>
        @endif
      @endforeach
    </tbody>
  </table>
@else
  <p>No tiene una cuenta de miembro con pagos disponibles.</p>
@endif
<h3>Total Recaudado: Bs. {{ $total }}</h3>
@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function() {
  $('#filter input').on('change', function() {
     $('#filter').submit();
  });
  $('#filter select').on('change', function() {
     $('#filter').submit();
  });
});
</script>
@endsection