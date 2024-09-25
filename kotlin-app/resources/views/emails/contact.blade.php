<x-mail::message>
<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" style="background-color: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
        <tr>
          <td style="text-align: center;">
            <h1 style="color: #4CAF50; font-size: 24px; margin-bottom: 10px;">You Have A New Feedback!</h1>
            <p style="color: #555; font-size: 16px; margin-bottom: 30px;">A new feedback has been submitted. Here are the details:</p>
          </td>
        </tr>
        <tr>
          <td style="padding: 20px 0; border-top: 1px solid #e0e0e0; border-bottom: 1px solid #e0e0e0;">
            <h3 style="color: #333;"><strong>Name:</strong> <span style="color: #555;">{{ $data['username'] }}</span></h3>
            <h3 style="color: #333;"><strong>Email:</strong> <span style="color: #555;">{{ $data['email'] }}</span></h3>
            <h3 style="color: #333;"><strong>Message:</strong></h3>
            <p style="color: #555; font-size: 14px; line-height: 1.6;">{{ $data['message'] }}</p>
          </td>
        </tr>
        <tr>
          <td style="text-align: center; padding: 20px;">
            <p style="color: #999; font-size: 14px;">Thank you for your feedback! We appreciate your insights and suggestions to help improve our application.</p>
            <p style="color: #4CAF50; font-size: 16px; font-weight: bold; margin-top: 20px;">{{ config('app.name') }}</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</x-mail::message>
