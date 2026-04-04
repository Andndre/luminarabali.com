<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <x-seo
        title="Pembayaran Berhasil - Luminara Photobooth"
        description="Pembayaran Anda telah berhasil terkonfirmasi. Terima kasih telah memesan layanan Luminara Photobooth."
        og_image="/images/Logo Luminara Visual-BLACK-TPR.png"
        :noindex="true"
    />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0fdf4;
            color: #166534;
            text-align: center;
        }
        .container {
            padding: 2rem;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            max-width: 90%;
            width: 400px;
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #22c55e;
        }
        h1 { margin: 0 0 0.5rem 0; font-size: 1.5rem; }
        p { color: #4b5563; margin-bottom: 2rem; }
        .btn {
            background-color: #22c55e;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            cursor: pointer;
            border: none;
            font-size: 1rem;
            transition: background 0.2s;
        }
        .btn:hover { background-color: #16a34a; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">✅</div>
        <h1>Pembayaran Berhasil!</h1>
        <p>Terima kasih. Transaksi Anda telah dikonfirmasi. Tiket sedang dicetak.</p>
        
        <button onclick="window.close()" class="btn">Tutup Jendela Ini</button>
        
        <p style="font-size: 0.8rem; margin-top: 1rem; color: #9ca3af;">
            Jika tombol tidak bekerja, silakan tutup jendela (X) secara manual.
        </p>
    </div>
</body>
</html>
