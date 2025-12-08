<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
}

.footer {
width: 100% !important;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
}
}
</style>
{!! $head ?? '' !!}
</head>
<body style="background: #f3f4f6; margin: 0; padding: 0; width: 100%;">
	<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background: #f3f4f6; min-height: 100vh;">
		<tr>
			<td align="center" style="padding: 32px 0;">
				<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width: 600px; margin: 0 auto; background: transparent;">
					{!! $header ?? '' !!}

					<!-- Email Body -->
					<tr>
						<td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important; background: #fff; border-radius: 18px; box-shadow: 0 2px 12px rgba(31,38,135,0.07); padding: 32px 24px 24px 24px;">
							<table class="inner-body" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="width: 100%;">
								<!-- Body content -->
								<tr>
									<td class="content-cell" style="font-family: 'Inter', Arial, sans-serif; font-size: 16px; color: #22223b;">
										{!! Illuminate\Mail\Markdown::parse($slot) !!}

										{!! $subcopy ?? '' !!}
									</td>
								</tr>
							</table>
						</td>
					</tr>
					{!! $footer ?? '' !!}
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
