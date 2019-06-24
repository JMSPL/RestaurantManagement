<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Set Password Email</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
    <body>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
            <tr>
                <td bgcolor="#800000" style="color: #fff; text-align: center">
                    <h2>Restaurant Management</h2>
                </td>
            </tr>
            <tr>
                <td bgcolor="#800000" style="color: #fff; text-align: center">
                    <h3>Welcome {{ $worker->name }}!</h3>
                </td>
            </tr>
            <tr>
                <td style="padding: 50px 10px 50px 10px">
                    You have been registered in Restaurant Management. Please click in the button bellow to set your password and login.
                </td>
            </tr>
            <tr>
                <td>
                    <a href="{{ env('APP_URL') }}/workers/password/{{ $token }}" target="_blank" style="background-color:#800000;color:#ffffff;display:inline-block;line-height:44px;text-align:center;text-decoration:none;width:100%;-webkit-text-size-adjust:none;mso-hide:all;">
                        CHANGE PASSWORD
                    </a>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    &copy; Restaurant Management - 2018
                </td>
            </tr>
        </table>
    </body>
</html>