<!DOCTYPE html>
<html>

<head>
    <title> Forgot Password! </title>
</head>

<body>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden sm:rounded-lg">

                <div class="min-h-screen flex justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
                    <div class="max-w-md w-full space-y-8">
                        Hi {{ $data['name']}},
                        <br>
                        Please <a href="{{$data['frontEndUrl']}}/reset-password?uky={{$data['id']}}">click here</a> to reset your account password
                        <br>
                        Thanks,
                        <br>
                        Team Mentari.
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