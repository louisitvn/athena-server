<span class="badge bg-{{ $verificationCampaign->status }} rd-50 {{ $verificationCampaign->isError() ? 'xtooltip' : '' }}"
    title="{{ $verificationCampaign->error }}"
>{{ trans('server::messages.verification_campaign.status.' . $verificationCampaign->status) }}</span>