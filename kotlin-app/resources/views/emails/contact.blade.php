<x-mail::message>
# You Have A Feedback!


<h3><strong>Name:</strong> {{ $data['username'] }}</h3>
<h3><strong>Email:</strong> {{ $data['email'] }}</h3>
<h3><strong>Message:</strong> {{ $data['message'] }}</h3>

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('ah3h3.name') }}
</x-mail::message>
