@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
<div class="box-center">
    <div class="bg-white text-center py-5">
        <h1>404!</h1>
        <h4>Halaman tidak ditemukan.</h4>
        <p class="mt-3 small text-primary"></p>
    </div>
</div>

<script>
    $(document).ready(function() {
    let timeLeft = 3;
    let $message = $('<p>').text('Mengalihkan ke halaman utama dalam 3 detik...');
    $('p').append($message);
    
    let timer = setInterval(function() {
        if(timeLeft <= 0) {
            clearInterval(timer);
            window.location.href = "/";
        } else {
            $message.text(`Mengalihkan ke halaman utama dalam ${timeLeft} detik...`);
            timeLeft--;
        }
    }, 1000);
});
</script>
@endsection