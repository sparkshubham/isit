<html>
<head>
	<title>SnapMailbox</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link href="https://fonts.googleapis.com/css?family=Montserrat:200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
	<style>
		*{box-sizing: border-box;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;}
		@media only screen and (max-width: 767px) {
			table{width: 100%;text-align: center;}
			.email-logo{text-align: center;}
		}
	</style>
</head>
<body style="-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100%;font-family: 'Montserrat', sans-serif;height: 100%;margin: 0;padding: 0;width: 100%;box-sizing: border-box;">

	<table bgcolor="#f2f2f2" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="-webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-family: 'Montserrat', sans-serif; border-collapse: collapse;">
		<tbody>
			<tr>
    <td colspan="2">
        <table width="100%" cellpadding="12" cellspacing="0" border="0">
            <tbody>
                <tr>
                    <td>
                        <div style="overflow: hidden;">
                            <font size="-1">
                                <u></u>
                                <div style="padding-top:24px;margin-bottom:0px;color:#666;background-color:#f5f5f5">
                                    <table cellpadding="0" cellspacing="0" align="center" valign="top" style="width:668px;background-color:#f5f5f5;border:0;word-wrap:break-word;height:100%">
                                        <tbody>
                                            <tr>
                                                <td cellpadding="0" cellspacing="0" style="width:100%">
                                                    <table cellpadding="0" cellspacing="0" style="width:100%;background-color:#fff;border:0">
                                                        <tbody>
                                                            <tr>
                                                                <td height="86" style="background-color:#ffffff">
                                                                    <a href="{{$image_path}}" style="color:#336699;font-weight:normal;text-decoration:underline" target="_blank">
                                                                        <img src="{{$image_path}}/img/email/{{$logo_image_name}}" alt="SnapMailbox Logo" width="600" height="96" style="border:0; padding-left: 25px;">
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" valign="top">
                                                                    <table border="0" cellpadding="0" cellspacing="0" width="600">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td valign="top">
                                                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td valign="top">
                                                                                                    <div style="background-color:#ffffff;color:#505050;font-family:arial;font-size:14px;line-height:150%;text-align:left">
                                                                                                    {{-- Body Start --}}
                                                                                                    {!! $body !!}
                                                                                                    {{-- ./Body Start --}}
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="600" style="padding:0 24px;box-sizing:border-box">
                                                                    <p style="font-family:'Helvetica-Regular','Helvetica Regular',Helvetica,Arial,sans-serif;font-weight:400;font-style:normal;font-size:11px;line-height:15px;text-align:center;color:#888;border-top:1px solid #ccc;padding-top:16px;padding-bottom:12px;box-sizing:border-box">
                                                                        Please view our <a href="{{ url('terms-of-use') }}" style="color:#888;text-decoration:underline" target="_blank">Terms of Service</a> and
                                                                        <a href="{{ url('privacy-policy') }}" style="color:#888;text-decoration:underline" target="_blank">Privacy Policy</a>
                                                                        <br>
                                                                        &copy; SnapMailbox.com LLC
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    &nbsp;
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </font>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
		</tbody>
	</table>
</body>

</html>
