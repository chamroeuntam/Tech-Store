@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<a href="{{ $url }}" target="_blank" rel="noopener"
    style="display:inline-block;padding:14px 32px;font-size:16px;font-weight:600;line-height:1.5;text-align:center;text-decoration:none;border-radius:8px;background-color:#667eea;color:#fff;border:none;box-shadow:0 2px 8px rgba(102,126,234,0.15);transition:background 0.2s;"
    >{!! $slot !!}</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
