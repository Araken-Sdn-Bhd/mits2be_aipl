<!DOCTYPE html>
<html>

<head>
    <title>MENTARI MALAYSIA: Appointment Request</title>
</head>

<body>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden sm:rounded-lg">

                <div class="min-h-screen flex justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
                    <div class="max-w-md w-full space-y-8">
                        Dear {{ $data['name']}},
                        <br>
                        <br>
                        Your appointment with {{ $data['branch']}} has been confirmed and scheduled
                        on {{$data['date']}} at {{$data['time']}}.
                        <br>
                        <br>
                        Thank you.
                        <br>
                        {{ $data['branch']}}.
                        <br>
                        <img src="{{asset('storage/img/mentari.png')}}" alt="" title="" style="width:20px; height:auto;">
                        <br>
                        <br>
                        <div style="color: grey"><small>This is an auto-generated email. Please do not reply to this email.</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>