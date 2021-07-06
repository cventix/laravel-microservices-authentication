<h1>Logged in successfully</h1>

<script>
    var token = @json($token);
    console.log(token);
    localStorage.setItem('token', JSON.stringify(token));
    setTimeout(function() {
        window.location.replace(`http://localhost:8000/?t=${token.access_token}&r=${token.refresh_token}&e=${token.expires_in}`);
    }, 3000);
</script>