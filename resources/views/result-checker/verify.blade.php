<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Check Result</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
	<div class="row justify-content-center">
		<div class="col-md-6">
			<div class="card shadow-sm">
				<div class="card-body p-4">
					<h3 class="mb-3">Student Result Checker</h3>
					<p class="text-muted">Enter your registration number and the six-digit PIN printed by the school.</p>
					@if($errors->any())
						<div class="alert alert-danger">{{ $errors->first() }}</div>
					@endif
					<form method="POST" action="{{ route('result-checker.verify') }}">
						@csrf
						<input type="hidden" name="academic_year" value="{{ \App\Models\SystemSetting::getValue('academic_year') }}">
						<input type="hidden" name="term" value="{{ \App\Models\SystemSetting::getValue('term') }}">
						<label class="form-label" for="registration_number">Registration number</label>
						<input id="registration_number" name="registration_number" class="form-control mb-3" value="{{ old('registration_number') }}" required>
						<label class="form-label" for="pin">Six-digit PIN</label>
						<input id="pin" name="pin" type="text" inputmode="numeric" autocomplete="one-time-code" minlength="6" maxlength="6" pattern="[0-9]{6}" class="form-control mb-1" required>
						<div class="form-text mb-4">PIN must contain exactly 6 digits.</div>
						<button class="btn btn-primary w-100" type="submit">Check Result</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</main>
</body>
</html>
