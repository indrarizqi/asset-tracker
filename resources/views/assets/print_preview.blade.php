<!DOCTYPE html>
<html>
<head>
    <title>Preview Label Aset</title>
    <style>
        body { font-family: sans-serif; padding: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { 
            padding: 10px 20px; text-decoration: none; color: white; border-radius: 5px; 
            display: inline-block; margin-right: 10px;
        }
        .btn-green { background-color: #28a745; }
        .btn-blue { background-color: #007bff; }
        .alert { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;}
    </style>
</head>
<body>

    <h2>Daftar Aset Siap Cetak</h2>

    @if(session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom: 20px;">
        <a href="/assets/download-pdf" class="btn btn-green" target="_blank">
            🖨️ Download PDF Label
        </a>
        
        <a href="/assets/create" class="btn btn-blue">
            + Tambah Aset Baru
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Asset Tag (ID)</th>
                <th>Nama Aset</th>
                <th>Kategori</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td style="font-family: monospace; font-weight: bold;">{{ $asset->asset_tag }}</td>
                <td>{{ $asset->name }}</td>
                <td>
                    @if($asset->category == 'mobile') <span style="background:#e1f5fe; padding:2px 5px; border-radius:4px;">Mobile</span>
                    @elseif($asset->category == 'semi-mobile') <span style="background:#fff3e0; padding:2px 5px; border-radius:4px;">Semi</span>
                    @else <span style="background:#ffebee; padding:2px 5px; border-radius:4px;">Fixed</span>
                    @endif
                </td>
                <td>{{ $asset->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">Belum ada data aset.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>