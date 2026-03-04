<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes Kepribadian - Masukkan Kode</title>
    <style>
        * { box-sizing: border-box; }
        body { margin:0; min-height:100vh; font-family:Arial,sans-serif; color:#e6f1ff; background:radial-gradient(circle at 12% 18%, rgba(73,224,255,.18), transparent 38%), radial-gradient(circle at 90% 12%, rgba(73,126,255,.18), transparent 34%), #030b1d; display:grid; place-items:center; padding:24px 16px; }
        .box { width:100%; max-width:560px; background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98)); border:1px solid #1f3d68; border-radius:16px; padding:28px; box-shadow:0 20px 50px rgba(0,0,0,.35); }
        h1 { margin:0 0 10px; font-size:48px; line-height:1.1; letter-spacing:.2px; }
        p { color:#94acd0; margin:0 0 16px; font-size:14px; line-height:1.45; }
        label { display:block; margin:0 0 8px; color:#94acd0; font-size:14px; font-weight:700; }
        input {
            display:block;
            width:100%;
            max-width:100%;
            background:#071327;
            color:#e6f1ff;
            border:1px solid #1f3d68;
            border-radius:10px;
            padding:12px 14px;
            min-height:48px;
            line-height:1.2;
            font-size:16px;
            text-transform:uppercase;
            outline:none;
            appearance:none;
            -webkit-appearance:none;
        }
        input:focus { border-color:#5ce8ff; box-shadow:0 0 0 2px rgba(92,232,255,.25); }
        button {
            margin-top:14px;
            border:0;
            border-radius:10px;
            background:linear-gradient(180deg,#5ce8ff,#35d0f5);
            color:#032137;
            min-height:48px;
            padding:12px 16px;
            font-weight:800;
            font-size:18px;
            cursor:pointer;
            width:100%;
        }
        .err { margin-top:10px; color:#ffc5de; font-size:14px; }
        .admin { margin-top:16px; text-align:center; }
        .admin a { color:#5ce8ff; text-decoration:none; font-size:14px; }
        @media (max-width: 768px) {
            .box { max-width: 100%; padding:22px; }
            h1 { font-size:36px; }
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Tes Kepribadian</h1>
        <p>Masukkan kode sesi dari admin untuk memulai tes (DISC atau MBTI).</p>
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
