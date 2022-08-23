<!DOCTYPE html>
<html>

<head>
    <title>Credentials </title>
</head>

<body>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden sm:rounded-lg">

                <div class="min-h-screen flex justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
                    <div class="max-w-md w-full space-y-8">
                        Hi {{ $data['name']}},
                        <br>
                        Your Login UserId is- {{ $data['user_id']}}
                        <br>
                        Your Login Password is- {{ $data['password']}}
                        <br>
                        Thanks,
                        <br>
                        Team Mentari.
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>