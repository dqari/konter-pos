<?php
$keranjang = $keranjang ?? [];
$total = 0;
$totalItem = 0;
foreach ($keranjang as $item) {
    $total += (float) $item['harga_akhir'];
    $totalItem += (int) $item['qty'];
}
$tokoNama = $toko['store_name'] ?? 'WM Cellular';
$tokoLogo = $toko['logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="2">
    <title>Layar Pelanggan - <?= esc($tokoNama); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --ink: #172033; --navy: #111a2d; --amber: #f4b740; --cyan: #39b8c8; --muted: #718096; --line: #e6ebf2; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; background: #f5f7fb; color: var(--ink); font-family: 'DM Sans', sans-serif; }
        .display-shell { min-height: 100vh; display: flex; flex-direction: column; }
        .display-topbar { min-height: 86px; padding: 18px clamp(20px, 4vw, 64px); display: flex; align-items: center; justify-content: space-between; gap: 24px; color: #fff; background: var(--navy); }
        .brand { display: flex; align-items: center; gap: 14px; }
        .brand-mark { width: 48px; height: 48px; display: grid; place-items: center; border-radius: 14px; color: var(--ink); background: var(--amber); font-size: 1.5rem; }
        .brand-mark img { width: 100%; height: 100%; padding: 6px; object-fit: contain; border-radius: 11px; }
        .brand-name { font: 700 1.35rem 'Space Grotesk', sans-serif; letter-spacing: .02em; }
        .brand-caption { color: rgba(255,255,255,.55); font-size: .72rem; letter-spacing: .14em; text-transform: uppercase; }
        .live-state { color: #a8eadc; font-size: .8rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .live-dot { display: inline-block; width: 8px; height: 8px; margin-right: 7px; border-radius: 50%; background: #46d5ad; box-shadow: 0 0 0 5px rgba(70,213,173,.15); }
        .display-main { flex: 1; width: min(1500px, 100%); margin: 0 auto; padding: clamp(24px, 5vw, 64px); display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(320px, .8fr); gap: clamp(24px, 4vw, 64px); align-items: stretch; }
        .cart-panel { min-width: 0; }
        .eyebrow { color: var(--cyan); font-size: .75rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }
        h1, h2, .total-value { font-family: 'Space Grotesk', sans-serif; }
        h1 { margin: 6px 0 28px; font-size: clamp(2rem, 4vw, 3.8rem); line-height: 1; letter-spacing: -.04em; }
        .item-list { overflow: hidden; border: 1px solid var(--line); border-radius: 20px; background: #fff; box-shadow: 0 18px 44px rgba(23,32,51,.07); }
        .item-row { display: grid; grid-template-columns: 1fr auto; gap: 24px; padding: 22px 26px; border-bottom: 1px solid var(--line); }
        .item-row:last-child { border-bottom: 0; }
        .item-name { font-size: clamp(1.05rem, 2vw, 1.45rem); font-weight: 700; }
        .item-code { margin-top: 5px; color: var(--muted); font-size: .85rem; }
        .item-tag { display: inline-block; margin-top: 10px; padding: 4px 9px; border-radius: 999px; color: #08727e; background: #e5f7f8; font-size: .7rem; font-weight: 700; text-transform: uppercase; }
        .item-price { text-align: right; white-space: nowrap; font: 700 clamp(1.05rem, 2vw, 1.4rem) 'Space Grotesk', sans-serif; }
        .item-qty { display: block; margin-top: 6px; color: var(--muted); font: 500 .85rem 'DM Sans', sans-serif; }
        .empty-state { padding: clamp(50px, 10vw, 130px) 24px; text-align: center; color: var(--muted); }
        .empty-state .icon { color: #cad4e3; font-size: 5rem; }
        .summary-panel { display: flex; flex-direction: column; justify-content: space-between; min-height: 390px; padding: clamp(26px, 4vw, 44px); border-radius: 24px; color: #fff; background: linear-gradient(145deg, #111a2d, #263653); box-shadow: 0 24px 48px rgba(17,26,45,.2); }
        .summary-label { color: rgba(255,255,255,.62); font-size: .8rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
        .total-value { margin: 14px 0 0; color: var(--amber); font-size: clamp(2.6rem, 5vw, 5.2rem); line-height: .98; letter-spacing: -.05em; overflow-wrap: anywhere; }
        .summary-meta { display: flex; justify-content: space-between; gap: 18px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,.14); color: rgba(255,255,255,.72); }
        .summary-meta strong { display: block; margin-top: 4px; color: #fff; font-size: 1.2rem; }
        .welcome { margin-top: 26px; padding: 18px 20px; border: 1px solid #d6f0f3; border-radius: 16px; color: #08727e; background: #f3fbfc; font-size: 1rem; }
        @media (max-width: 820px) { .display-main { grid-template-columns: 1fr; } .summary-panel { min-height: 300px; } }
        @media (max-width: 520px) { .display-topbar { align-items: flex-start; flex-direction: column; } .item-row { padding: 18px; } .display-main { padding: 22px 14px; } }
    </style>
</head>
<body>
    <div class="display-shell">
        <header class="display-topbar">
            <div class="brand">
                <div class="brand-mark"><?php if ($tokoLogo): ?><img src="<?= base_url('uploads/logo/' . $tokoLogo); ?>" alt="Logo <?= esc($tokoNama); ?>"><?php else: ?>&#128241;<?php endif; ?></div>
                <div><div class="brand-name"><?= esc($tokoNama); ?></div><div class="brand-caption">Customer display</div></div>
            </div>
            <div class="live-state"><span class="live-dot"></span>Terhubung ke meja kasir</div>
        </header>

        <main class="display-main">
            <section class="cart-panel">
                <div class="eyebrow">Ringkasan belanja</div>
                <h1>Pesanan Anda</h1>
                <div class="item-list">
                    <?php if ($keranjang): ?>
                        <?php foreach ($keranjang as $item): ?>
                            <div class="item-row">
                                <div>
                                    <div class="item-name"><?= esc($item['nama']); ?></div>
                                    <div class="item-code"><?= esc($item['kode_unik']); ?></div>
                                    <span class="item-tag"><?= esc($item['tipe_barang']); ?></span>
                                </div>
                                <div class="item-price">
                                    Rp <?= number_format($item['harga_akhir'], 0, ',', '.'); ?>
                                    <span class="item-qty"><?= esc($item['qty']); ?> x Rp <?= number_format($item['harga_awal'], 0, ',', '.'); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="icon">&#128722;</div>
                            <h2>Silakan pilih produk</h2>
                            <p>Produk yang dipindai kasir akan muncul di layar ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <aside>
                <div class="summary-panel">
                    <div>
                        <div class="summary-label">Total yang harus dibayar</div>
                        <div class="total-value">Rp <?= number_format($total, 0, ',', '.'); ?></div>
                    </div>
                    <div class="summary-meta">
                        <div><span>Jumlah item</span><strong><?= $totalItem; ?></strong></div>
                        <div class="text-end"><span>Status</span><strong><?= $keranjang ? 'Siap checkout' : 'Menunggu scan'; ?></strong></div>
                    </div>
                </div>
                <div class="welcome"><strong>Terima kasih</strong><br>Pastikan nama dan jumlah barang sudah sesuai sebelum pembayaran.</div>
            </aside>
        </main>
    </div>
</body>
</html>
