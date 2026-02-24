<!DOCTYPE html>
<html>

<head>
    <title>Assets Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #003366;
        }

        .header p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .status-maintenance {
            color: orange;
            font-weight: bold;
        }

        .status-used {
            color: blue;
            font-weight: bold;
        }

        .status-broken {
            color: red;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>VODECO DIGITAL MEDIATAMA</h1>
        <p>Jl. Dagan, Procot, Kec. Slawi, Kab. Tegal</p>
        <p>Telp: +62 878-1099-2666 | Email: hello@vodeco.co.id</p>
    </div>

    <h3 style="text-align: center;">LAPORAN REKAPITULASI ASET</h3>
    <p style="text-align: center;">Per Tanggal: {{ date('d F Y') }}</p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">ID Aset</th>
                <th width="25%">Nama Aset</th>
                <th width="15%">Kategori Aset</th>
                <th width="20%">Penanggung Jawab / Info</th>
                <th width="10%">Kondisi</th>
                <th width="10%">Status</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-family: monospace;">{{ $asset->asset_tag }}</td>
                <td>{{ $asset->name }}</td>
                <td>{{ ucfirst($asset->category) }}</td>
                <td>
                    {{ $asset->person_in_charge ?? '-' }}
                    <br><small style="color: #666;">Beli: {{ $asset->purchase_date }}</small>
                </td>
                <td class="{{ $asset->condition == 'Baik' ? 'status-used' : ($asset->condition == 'Rusak' ? 'status-maintenance' : 'status-broken') }}">{{ $asset->condition ?? '-' }}</td>
                <td class="
                                    {{ $asset->status == 'maintenance' ? 'status-maintenance' :
                ($asset->status == 'in_use' ? 'status-used' : 'status-broken') }}">
                    {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                </td>
                <td>{{ $asset->description ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Tegal, {{ date('d F Y') }}</p>
        <br><br><br>
        <p><strong>( {{ Auth::user()->name }} )</strong><br></p>
    </div>

</body>

</html>