<script type="text/javascript">
  $( "#ci_number" ).on( "change", function(e) {
    e.preventDefault();
    var value = $(this).val();
    $.ajax({
        url: "{{ url('process/check-ci') }}/"+value,
        success: function(respuesta) {
            if(respuesta['exists']){
                var customer = respuesta['customer'];
                $('#first_name').val(customer['first_name']);
                $('#last_name').val(customer['last_name']);
                @if(config('customer.fields.age'))
                    $('#age').val(customer['age']);
                @endif
                $('#birth_date').val(customer['birth_date']);
                $('#sex').val(customer['sex']);
                $('#nationality').val(customer['nationality']);
                $('#email').val(customer['email']);
                $('#address').val(customer['address']);
                $('#phone').val(customer['phone']);
                @if(config('customer.fields.shirt'))
                    $('#shirt').val(customer['shirt']);
                @endif
                @if(config('customer.fields.shirt_size'))
                    $('#shirt_size').val(customer['shirt_size']);
                @endif
                @if(config('customer.fields.emergency_short'))
                    $('#emergency').val(customer['emergency']);
                @endif
                @if(config('customer.fields.emergency_long'))
                    $('#emergency_name').val(customer['emergency_name']);
                    $('#emergency_number').val(customer['emergency_number']);
                @endif
                $('#nit_name').val(customer['nit_name']);
                $('#nit_number').val(customer['nit_number']);
                alert('Se encontró un cliente con su carnet en nuestro sistema. Sus datos fueron precargados, sin embargo puede editarlos aún.')
            } else {
                console.log("Cliente Nuevo");
            }
            $('.after-check').show();
        },
        error: function() {
            console.log("No se ha podido obtener la información");
        }
    });
    return false;
  });
</script>