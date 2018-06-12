<script type="text/javascript">
  $( ".calculate-age" ).on( "change", function(e) {
    e.preventDefault();
    var value = $(this).val();
    var key = $(this).data('key');
    value = new Date(value);
    var today = new Date();
    var age = Math.floor((today-value) / (365.25 * 24 * 60 * 60 * 1000));
    $('#'+key+'_age').val(age+' años');
    return false;
  });
</script>