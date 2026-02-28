<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes Kepribadian - Masukkan Kode</title>
    <style>
        body { margin:0; min-height:100vh; font-family:Arial,sans-serif; color:#e6f1ff; background:radial-gradient(circle at 12% 18%, rgba(73,224,255,.18), transparent 38%), radial-gradient(circle at 90% 12%, rgba(73,126,255,.18), transparent 34%), #030b1d; display:grid; place-items:center; padding:16px; }
        .box { width:min(460px,100%); background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98)); border:1px solid #1f3d68; border-radius:16px; padding:24px; box-shadow:0 20px 50px rgba(0,0,0,.35); }
        h1 { margin:0 0 8px; font-size:28px; }
        p { color:#94acd0; margin-top:0; }
        label { display:block; margin:14px 0 6px; color:#94acd0; font-size:13px; font-weight:700; }
        input {
            width:100%;
            background:#071327;
            color:#e6f1ff;
            border:1px solid #1f3d68;
            border-radius:10px;
            padding:12px 14px;
            line-height:1.4;
            font-size:15px;
            text-transform:uppercase;
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
        .err { margin-top:10px; color:#ffc5de; font-size:14px; }
        .admin { margin-top:16px; text-align:center; }
        .admin a { color:#5ce8ff; text-decoration:none; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Tes Kepribadian</h1>
        <p>Masukkan kode sesi dari admin untuk memulai tes.</p>
        <form method="POST" action="/access">
            @csrf
            <label for="code">Kode Sesi</label>
            <input id="code" name="code" value="{{ old('code') }}" required autofocus>
            <button type="submit">Masuk Tes</button>
        </form>
        @error('code')<div class="err">{{ $message }}</div>@enderror
        <div class="admin"><a href="/login">Login Admin</a></div>
    </div>
</body>
</html>
