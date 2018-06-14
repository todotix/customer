@extends('master::layouts/admin')

@section('content')
<h1>Mi Cuenta</h1>
@if(count($customers)>0)
  @foreach($customers as $customer)
    @include('includes.member')
    <a href="{{ url('admin/my-account/'.$customer->id.'/edit') }}"><h3>Editar Cuenta</h3></a>
  @endforeach
@else
  <p>No tiene una cuenta de miembro con pagos disponibles.</p>
@endif
<br><br><br>
<h1>Editar Contraseña</h1>
  <form action="{{ url('admin/edit-password') }}" method="post" id="change-password" class="tg-commentform help-form">
    <fieldset class="row">
      <div class="col-sm-6"><div class="form-group">
        <span class="control-label">Contraseña</span>
        {!! Form::password('member_code', ['placeholder'=>'Introduzca una contraseña de al menos 6 carcteres', 'required'=>false, 'class'=>'form-control']) !!}
      </div></div>
      <div class="col-sm-6"><div class="form-group">
        <span class="control-label">Confirmar Contraseña</span>
        {!! Form::password('member_code_confirmation', ['placeholder'=>'Repita su contraseña', 'required'=>false, 'class'=>'form-control']) !!}
        @if($errors->has('member_code'))
          <p>Ambas contraseñas deben ser iguales</p>
        @endif
      </div></div>
    </fieldset>
    <button type="submit" class="btn btn-site">Cambiar Contraseña</button>
  </form>
@endsection