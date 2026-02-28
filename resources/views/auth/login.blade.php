<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <style>
        body { margin:0; min-height:100vh; font-family:Arial,sans-serif; color:#e6f1ff; background:radial-gradient(circle at 12% 18%, rgba(73,224,255,.18), transparent 38%), radial-gradient(circle at 90% 12%, rgba(73,126,255,.18), transparent 34%), #030b1d; display:grid; place-items:center; padding:16px; }
        .box { width:min(460px,100%); background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98)); border:1px solid #1f3d68; border-radius:16px; padding:24px; box-shadow:0 20px 50px rgba(0,0,0,.35); }
        h1 { margin:0 0 10px; }
        label { display:block; margin:10px 0 6px; color:#94acd0; font-size:13px; font-weight:700; }
        input {
            width:100%;
            background:#071327;
            color:#e6f1ff;
            border:1px solid #1f3d68;
            border-radius:10px;
            padding:12px 14px;
            line-height:1.4;
            font-size:15px;
            appearance:none;
            -webkit-appearance:none;
        }
        button {
            margin-top:14px;
            border:0;
            border-radius:10px;
            background:linear-gradient(180deg,#5ce8ff,#35d0f5);
            color:#032137;
            padding:12px 16px;
            font-weight:800;
            cursor:pointer;
            width:100%;
        }
        .err { margin-top:8px; color:#ffc5de; font-size:14px; }
        a { color:#5ce8ff; text-decoration:none; }
    </style>
</head>
<body>
<div class="box">
    <h1>Login Admin</h1>
    <form method="POST" action="/login">
        @csrf
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>
        <button type="submit">Login</button>
    </form>
    @error('email')<div class="err">{{ $message }}</div>@enderror
    <p style="margin-top:14px;"><a href="/">Kembali ke akses kode</a></p>
</div>
</body>
</html>
