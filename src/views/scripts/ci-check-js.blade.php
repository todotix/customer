<script type="text/javascript">
  $( "#1_ci_number" ).on( "change", function(e) {
    e.preventDefault();
    var value = $(this).val();
    $.ajax({
        url: "{{ url('admin/check-ci') }}/"+value,
        success: function(respuesta) {
            if(respuesta['exists']){
                var key = 1;
                var customer = respuesta['customer'];
                $('#'+key+'_first_name').val(customer['first_name']);
                $('#'+key+'_last_name').val(customer['last_name']);
                if(config('customer.fields.age')){
                	$('#'+key+'_age').val(customer['age']);
                }
                $('#'+key+'_birth_date').val(customer['birth_date']);
                $('#'+key+'_sex').val(customer['sex']);
                $('#'+key+'_nationality').val(customer['nationality']);
                $('#'+key+'_email').val(customer['email']);
                $('#'+key+'_address').val(customer['address']);
                $('#'+key+'_phone').val(customer['phone']);
                if(config('customer.fields.shirt')){
                	$('#'+key+'shirt').val(customer['shirt']);
                }
                if(config('customer.fields.shirt_size')){
                	$('#'+key+'_shirt_size').val(customer['shirt_size']);
                }
                if(config('customer.fields.emergency_short')){
                	$('#'+key+'_emergency').val(customer['emergency']);
            	}
                if(config('customer.fields.emergency_long')){
                	$('#'+key+'_emergency_name').val(customer['emergency_name']);
                	$('#'+key+'_emergency_number').val(customer['emergency_number']);
            	}
                $('#'+key+'_nit_name').val(customer['nit_name']);
                $('#'+key+'_nit_number').val(customer['nit_number']);
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