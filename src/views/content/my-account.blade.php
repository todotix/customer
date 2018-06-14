@extends('master::layouts/admin')

@section('content')
@if($action=='create')
<h1>Crear Dependiente</h1>
@else
<h1>Editar Cuenta</h1>
@endif
@if($customer)
  @include('includes.member')
  <form action="{{ url('admin/edit-account') }}" method="post" class="tg-commentform help-form">
    <fieldset class="row">
      <div class="col-sm-4"><div class="form-group">
        <span class="control-label">Nombres</span>
        @if($action=='create')
          {{ Form::text('first_name', NULL, ['class'=>'form-control', 'required'=>true, 'placeholder'=>'EJ: Ricardo']) }}
        @else
          {{ Form::text('first_name', $customer->first_name, ['class'=>'form-control', 'required'=>true, 'placeholder'=>'EJ: Ricardo']) }}
        @endif
      </div></div>
      <div class="col-sm-4"><div class="form-group">
        <span class="control-label">Apellido Paterno</span>
        @if($action=='create')
          {{ Form::text('last_name', NULL, ['class'=>'form-control', 'required'=>true, 'placeholder'=>'EJ: Diaz']) }}
        @else
          {{ Form::text('last_name', $customer->last_name, ['class'=>'form-control', 'required'=>true, 'placeholder'=>'EJ: Diaz']) }}
        @endif
      </div></div>
      <div class="col-sm-4"><div class="form-group">
        <span class="control-label">Apellido Materno</span>
        @if($action=='create')
          {{ Form::text('last_name_2', NULL, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: Rodriguez']) }}
        @else
          {{ Form::text('last_name_2', $customer->last_name_2, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: Rodriguez']) }}
        @endif
      </div></div>
      <div class="col-sm-4"><div class="form-group">
        <span class="control-label">Email</span>
        @if($action=='create')
          {{ Form::text('email', NULL, ['class'=>'form-control', 'required'=>$custom_rules, 'placeholder'=>'EJ: rdiaz@gmail.com']) }}
        @else
          {{ Form::text('email', $customer->email, ['class'=>'form-control', 'required'=>$custom_rules, 'placeholder'=>'EJ: rdiaz@gmail.com']) }}
        @endif
      </div></div>
      <div class="col-sm-4"><div class="form-group">
        <span class="control-label">Teléfono Fijo</span>
        @if($action=='create')
          {{ Form::text('phone', NULL, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: 2795631']) }}
        @else
          {{ Form::text('phone', $customer->phone, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: 2795631']) }}
        @endif
      </div></div>
      <div class="col-sm-4"><div class="form-group">
        <span class="control-label">Teléfono Celular</span>
        @if($action=='create')
          {{ Form::text('cellphone', NULL, ['class'=>'form-control', 'required'=>$custom_rules, 'placeholder'=>'EJ: 7212147']) }}
        @else
          {{ Form::text('cellphone', $customer->cellphone, ['class'=>'form-control', 'required'=>$custom_rules, 'placeholder'=>'EJ: 7212147']) }}
        @endif
      </div></div>
      @if(!$dependants)
      <div class="col-sm-6"><div class="form-group">
        <span class="control-label">Número de NIT</span>
        {{ Form::text('nit_number', $customer->nit_number, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: 4765754017']) }}
      </div></div>
      <div class="col-sm-6"><div class="form-group">
        <span class="control-label">Razón Social</span>
        {{ Form::text('nit_name', $customer->nit_name, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: Diaz']) }}
      </div></div>
      @else
      <div class="col-sm-6"><div class="form-group">
        <span class="control-label">Número de Carnet</span>
        @if($action=='create')
          {{ Form::text('ci_number', NULL, ['class'=>'form-control', 'required'=>true, 'placeholder'=>'EJ: 4765754']) }}
        @else
          {{ Form::text('ci_number', $customer->ci_number, ['class'=>'form-control', 'required'=>true, 'placeholder'=>'EJ: 4765754']) }}
        @endif
      </div></div>
      <div class="col-sm-6"><div class="form-group">
        <span class="control-label">Expedición de Carnet</span>
        @if($action=='create')
          {{ Form::select('ci_expedition', $expeditions, NULL, ['class'=>'form-control', 'required'=>true]) }}
        @else
          {{ Form::select('ci_expedition', $expeditions, $customer->ci_expedition, ['class'=>'form-control', 'required'=>true]) }}
        @endif
      </div></div>
      @endif
      <div class="col-sm-6"><div class="form-group">
        <span class="control-label">Fecha de Nacimiento</span>
        @if($action=='create')
          {{ Form::text('birth_date', NULL, ['class'=>'form-control datepicker-max', 'required'=>true, 'placeholder'=>'EJ: 1980-04-27']) }}
        @else
          {{ Form::text('birth_date', $customer->birth_date, ['class'=>'form-control datepicker-max', 'required'=>true, 'placeholder'=>'EJ: 1980-04-27']) }}
        @endif
      </div></div>
      <div class="col-sm-6"><div class="form-group">
        <span class="control-label">Ocupación</span>
        @if($action=='create')
          {{ Form::text('career', NULL, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: Economista']) }}
        @else
          {{ Form::text('career', $customer->career, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: Economista']) }}
        @endif
      </div></div>
      <div class="col-sm-6"><div class="form-group">
        <span class="control-label">Teléfono de Oficina</span>
        @if($action=='create')
          {{ Form::text('office_phone', NULL, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: 2795786']) }}
        @else
          {{ Form::text('office_phone', $customer->office_phone, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: 2795786']) }}
        @endif
      </div></div>
      <div class="col-sm-6"><div class="form-group">
        <span class="control-label">Dirección de Oficina</span>
        @if($action=='create')
          {{ Form::text('office_address', NULL, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: Zona Calle # puerta']) }}
        @else
          {{ Form::text('office_address', $customer->office_address, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: Zona Calle # puerta']) }}
        @endif
      </div></div>
    </fieldset>
    <input type="hidden" name="parent_customer_id" value="{{ $parent_customer_id }}" />
    <input type="hidden" name="action" value="{{ $action }}" />
    <input type="hidden" name="customer_id" value="{{ $customer->id }}" />
    @if($action=='create')
      <p>Una vez solicite su nuevo dependiente, deberá ser aprobado por un administrador, proceso que puede demorar unos días.</p>
    @endif
    <button type="submit" class="btn btn-site">Guardar</button>
  </form>
@else
  <p>No tiene una cuenta de miembro con pagos disponibles.</p>
@endif
@endsection