@if($customer_array['customer'])
<h3>Nombre de Miembro: {{ $customer_array['customer']->name }}</h3><br>
@else
<h3>Sin Cliente Asociado</h3><br>
@endif