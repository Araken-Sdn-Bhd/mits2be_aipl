<!DOCTYPE html>
<html>

<head>
    <title> Verify Account! </title>
</head>

<body>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden sm:rounded-lg">

                <div class="min-h-screen flex justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
                    <div class="max-w-md w-full space-y-8">
                        Hi {{ $data['company_name']}},
                        <br>
                        Please <a href="{{$data['frontEndUrl']}}/verify-email?uky={{$data['user_id']}}">click here</a> to verify your account 
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