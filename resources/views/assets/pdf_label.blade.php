<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 0.2cm; }
        body { font-family: 'Arial', sans-serif; }
        
        /* Grid Layout */
        .container { width: 100%; }
        .label-box {
            width: 48%; 
            float: left;
            margin: 5px;
            border: 3px solid #000;
            border-radius: 12px;
            padding: 8px;
            height: 140px; 
            box-sizing: border-box;
            page-break-inside: avoid; 
            background: #fff;
        }

        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: middle; }
        
        /* QR Code Style */
        .qr-cell { 
            width: 35%; 
            text-align: center; 
            padding-right: 10px; 
            border-right: 2px solid #000;
        }

        /* Text Vodeco Style */
        .info-cell { width: 65%; padding-left: 10px; position: relative; }

        .top-text {
            font-size: 10px;
            color: #000;
            margin-bottom: 5px;
            font-weight: normal;
        }

        /* PROPERTY OF VODECO */
        .main-text { 
            font-size: 18px; 
            font-weight: 900; /* Paling Tebal */
            text-transform: uppercase; 
            line-height: 0.9;
            margin-bottom: 5px;
            color: #000;
        }

        .sub-text {
            font-size: 9px;
            font-weight: normal;
        }

        .asset-id { 
            font-family: 'Courier New', monospace; 
            font-weight: bold; 
            font-size: 12px; 
            margin-top: 5px; 
        }
        
        /* Logo V Kecil */
        .logo-placeholder {
            position: absolute; 
            top: 0; 
            right: 0;
            width: 20px; 
            height: 20px; 
            border-left: 8px solid transparent;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        @foreach($assets as $asset)
        <div class="label-box">
            <table>
                <tr>
                    <td class="qr-cell">
                        <img src="data:image/png;base64, {!! $asset->qr_code !!}" width="85">
                        <div class="asset-id">{{ $asset->asset_tag }}</div>
                    </td>
                    
                    <td class="info-cell">
                        <div class="logo-placeholder"></div>
                        <div class="top-text">Unauthorized Use Is<br>Prohibited.</div>
                        <div class="main-text">PROPERTY OF<br>VODECO</div>
                        <div class="sub-text">&copy; All Rights Reserved</div>
                    </td>
                </tr>
            </table>
        </div>
        @endforeach
    </div>
</body>
</html>