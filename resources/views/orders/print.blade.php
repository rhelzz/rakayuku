<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }} - Rakayuku</title>
    <style>
        @page { size: A4; margin: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 40px; color: #333; line-height: 1.6; }
        .invoice-box { max-width: 800px; margin: auto; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #f97316; padding-bottom: 20px; margin-bottom: 30px; }
        .logo h1 { color: #f97316; margin: 0; font-size: 32px; letter-spacing: -1px; }
        .logo p { margin: 0; font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-title { text-align: right; }
        .invoice-title h2 { margin: 0; color: #333; text-transform: uppercase; font-size: 24px; }
        .invoice-title p { margin: 0; color: #888; font-size: 14px; }
        
        .info-section { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .info-col h3 { font-size: 12px; text-transform: uppercase; color: #888; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .info-col p { margin: 2px 0; font-size: 14px; font-weight: 500; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table th { background: #f8fafc; color: #666; font-size: 12px; text-transform: uppercase; padding: 12px 15px; text-align: left; border-bottom: 2px solid #eee; }
        table td { padding: 15px; border-bottom: 1px solid #eee; font-size: 14px; }
        .text-right { text-align: right; }
        
        .totals { margin-left: auto; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; }
        .total-row.grand-total { border-top: 2px solid #f97316; margin-top: 10px; padding-top: 15px; font-weight: bold; font-size: 18px; color: #f97316; }
        
        .payment-status { margin-top: 50px; padding: 15px; border-radius: 8px; display: inline-block; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        .status-paid { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .status-unpaid { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        .footer { margin-top: 100px; text-align: center; font-size: 12px; color: #aaa; border-top: 1px solid #eee; padding-top: 20px; }
        .signature-grid { display: flex; justify-content: space-between; margin-top: 50px; padding: 0 40px; }
        .signature-box { text-align: center; width: 200px; }
        .signature-line { margin-top: 60px; border-top: 1px solid #333; padding-top: 5px; font-weight: bold; }

        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
        }
        
        .btn-print { background: #f97316; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; cursor: pointer; border: none; }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" class="btn-print">Cetak Sekarang</button>
        <a href="{{ route('orders.show', $order) }}" style="margin-left: 10px; font-size: 14px; color: #666;">Kembali</a>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="logo">
                <h1>RAKAYUKU</h1>
                <p>Furniture & Custom Interior</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <p>#{{ $order->order_number }}</p>
            </div>
        </div>

        <div class="info-section">
            <div class="info-col">
                <h3>Diberikan Kepada:</h3>
                <p><strong>{{ $order->customer->name }}</strong></p>
                <p>{{ $order->customer->phone }}</p>
                <p>{{ $order->customer->address ?? '-' }}</p>
            </div>
            <div class="info-col" style="text-align: right;">
                <h3>Detail Transaksi:</h3>
                <p>Tanggal: {{ date('d F Y') }}</p>
                <p>Status: <strong>{{ $order->payment_status == 'PAID' ? 'LUNAS' : 'BELUM LUNAS' }}</strong></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Deskripsi Proyek / Pesanan</th>
                    <th class="text-right">Total Harga</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $order->project_name }}</strong><br>
                        <small style="color: #666;">{{ $order->project_description ?? 'Pengerjaan custom furniture sesuai kesepakatan.' }}</small>
                    </td>
                    <td class="text-right">Rp {{ number_format($order->selling_price, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div style="display: flex; justify-content: space-between;">
            <div>
                <div class="payment-status {{ $order->payment_status == 'PAID' ? 'status-paid' : 'status-unpaid' }}">
                    Status Pembayaran: {{ $order->payment_status == 'PAID' ? 'Lunas' : 'Ada Tunggakan' }}
                </div>
            </div>
            <div class="totals">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->selling_price, 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span>Total Dibayar</span>
                    <span style="color: #166534;">- Rp {{ number_format($order->total_paid, 0, ',', '.') }}</span>
                </div>
                <div class="total-row grand-total">
                    <span>Sisa Tagihan</span>
                    <span>Rp {{ number_format($order->remaining_payment, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            Terima kasih telah mempercayakan kebutuhan furniture Anda kepada Rakayuku.<br>
            Jl. Raya Produksi Kayu No. 123, Indonesia | www.rakayuku.com
        </div>
    </div>

    <script>
        // Auto trigger print dialog
        window.onload = function() {
            // Uncomment below if you want auto-print
            // window.print();
        }
    </script>
</body>
</html>