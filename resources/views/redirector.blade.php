<h1>Redirecting Please Wait</h1>
{{ print_r($data) }}

<script>
    window.onload = funtion (){
        $.ajax({
            type: "POST",
            url: "{{$data['redirect']}}",
            data: "{{ $data }}",
            success: function() {},
            dataType: "json"
        });
    }
</script>
