<form action="/foo/bar" method="POST">
    csrf_token: {{ csrf_token() }}<br />
    method: @method('PUT')<br />
    csrf: @csrf<br />
</form>
