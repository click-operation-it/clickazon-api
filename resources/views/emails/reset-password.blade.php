<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        header {
            text-align: center;
            margin-bottom: 20px;
            background-color: #fff;
            color: #fff;
            padding: 10px;
            border-radius: 8px;
        }

        header img {
            max-width: 100px;
        }

        h1 {
            color: #333;
        }

        p {
            color: #555;
        }

        .button-container {
            text-align: center;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            background-color: #4caf50;
            color: #fff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #45a049;
        }

        .password-pic {
            display: block;
            margin: 20px auto;
            max-width: 100%;
            height: auto;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            color: #777;
            font-size: 12px;
        }

        .footer a {
            color: #4caf50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #003399;">
            <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
              <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #003399;"><![endif]-->

              <!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
              <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                <div style="height: 100%;width: 100% !important;">
                  <!--[if (!mso)&(!IE)]><!-->
                  <div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                    <!--<![endif]-->

                    <table style="font-family:'Cabin',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                      <tbody>
                        <tr>
                          <td style="overflow-wrap:break-word;word-break:break-word;padding:40px 10px 10px;font-family:'Cabin',sans-serif;" align="left">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                              <tr>
                                <td style="padding-right: 0px;padding-left: 0px;" align="center">

                                  <img align="center" border="0" style="color:#fff;" src="https://clickazon.co.uk/backend-api/public/assets/images/clickazonlogo-white.png" alt="Image" title="Image" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 26%;max-width: 150.8px;"
                                    width="150.8" />

                                </td>
                              </tr>
                            </table>

                          </td>
                        </tr>
                      </tbody>
                    </table>

                    <table style="font-family:'Cabin',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                      <tbody>
                        <tr>
                          <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Cabin',sans-serif;" align="left">

                            <div style="font-size: 14px; color: #e5eaf5; line-height: 140%; text-align: center; word-wrap: break-word;">
                              <p style="font-size: 14px; line-height: 140%; color: #fff;"><strong>R E S E T&nbsp; &nbsp;P A S S W O R D&nbsp; &nbsp;</strong></p>
                            </div>

                          </td>
                        </tr>
                      </tbody>
                    </table>

                    <table style="font-family:'Cabin',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                      <tbody>
                        <tr>
                          <td style="overflow-wrap:break-word;word-break:break-word;padding:0px 10px 31px;font-family:'Cabin',sans-serif;" align="left">

                            <div style="font-size: 14px; color: #e5eaf5; line-height: 140%; text-align: center; word-wrap: break-word;">
                              <p style="font-size: 14px; line-height: 140%;"><span style="font-size: 28px; line-height: 39.2px;"><strong><span style="line-height: 39.2px; font-size: 28px; color: #fff;">RESET PASSWORD</span></strong>
                                </span>
                              </p>
                            </div>

                          </td>
                        </tr>
                      </tbody>
                    </table>

                    <!--[if (!mso)&(!IE)]><!-->
                  </div>
                  <!--<![endif]-->
                </div>
              </div>
              <!--[if (mso)|(IE)]></td><![endif]-->
              <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
            </div>
        </div>
        <p>You are receiving this email because we received a password reset request for your {{env('APP_NAME') }} account on {{\Carbon\Carbon::now()->format('j, F Y. h:i')}}</p>
        <p>Click on the button below to reset your password</p>
        <div class="button-container">
            <a href="{{env('CUSTOMER_BASE_URL')}}#/auth/reset-password/{{$data['verification_code']}}&?email={{$data['email']}}" class="button">Reset Password</a>
        </div>
        <img src="https://clickazon.co.uk/backend-api/public/assets/images/reset.jpeg" style="height: 200px;" alt="Password Reset" class="password-pic">
        <p>If you did not request for a password reset on your {{env('APP_NAME')}} account, please disregard this email.</p>
        <div class="footer">
            <img src="https://clickazon.co.uk/backend-api/public/assets/images/clickazon-logo.png" alt="Your Logo">
            <p>This email was sent from Clickazon Ecommerce. &copy; {{date('Y')}} Clickazon. All rights reserved. | <a href="https://clickazon.co.uk/#/terms-and-conditions">Terms and Conditions</a></p>
        </div>
    </div>
</body>
</html>
