<!DOCTYPE html>
<html>
<head>
    <title>Cetak Label Aset</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            width: 100%;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; 
        }
        .grid-table td {
            padding: 4px;
            vertical-align: top;
        }
        .label-box {
            border: 1px solid #000;
            border-radius: 8px;
            padding: 5px;
            height: 100px; 
            overflow: hidden;
            page-break-inside: avoid;
        }
        .inner-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .qr-column {
            width: 40%;
            text-align: center;
        }
        .info-column {
            width: 65%;
            padding-left: 10px;
        }
        .qr-code-img {
            width: 100%;
            height: auto;
        }
        .warning-text {
            font-size: 8px;
            color: #555;
            margin: 0;
            text-transform: uppercase;
        }
        .company-name {
            font-weight: bold;
            font-size: 10px;
            border-bottom: 2px solid #000;
            display: inline-block;
            margin-bottom: 5px;
        }
        .asset-name {
            font-size: 11px;
            margin-bottom: 8px;
            height: 30px; 
        }
        .asset-tag {
            font-family: 'Courier', monospace;
            font-size: 14px;
            font-weight: bold;
            background-color: #000;
            color: #fff;
            padding: 2px 5px;
            border-radius: 4px;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <table class="grid-table">
            @foreach($assets->chunk(3) as $chunk)
                <tr>
                    @foreach($chunk as $asset)
                        <td>
                            <div class="label-box">
                                <table class="inner-table">
                                    <tr>
                                        <td class="qr-column">
                                            <img src="data:image/png;base64, {{ base64_encode(QrCode::format('png')->size(150)->generate($asset->asset_tag)) }}" class="qr-code-img">
                                        </td>
                                        <td class="info-column">
                                            <div class="warning-text">Property of:</div>
                                            <div class="company-name">VODECO GROUP</div>
                                            <div class="asset-name">
                                                {{ Str::limit($asset->name, 40) }}
                                            </div>
                                            <div class="asset-tag">{{ $asset->asset_tag }}</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    @endforeach
                    
                    @if($chunk->count() < 2)
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>

</body>
</html>