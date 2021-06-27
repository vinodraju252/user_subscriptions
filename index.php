<!DOCTYPE html>
<html>
	<head>
		<title>Email Subscription</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.0/css/font-awesome.min.css">
	</head>
	<body class="container">
		<div class="row">
			<div class="offset-3 col-md-6 offset-3">
				<div class="card mt-5">
					<div class="card-header"> Subscription form</div>
					<div class="card-body">
						<div class="alert alert-class d-none" role="alert"></div>
						<form id="sub-form">
							<div class="form-group">
								<label>First Name</label>
								<input type="text" name="first_name" placeholder="Enter first name" class="form-control" required>
							</div>
							<div class="form-group">
								<label>Last Name</label>
								<input type="text" name="last_name" placeholder="Enter last name"  class="form-control" required>
							</div>
							<div class="form-group">
								<label>Email Address</label>
								<input type="email" name="email" placeholder="Enter email address" class="form-control"required>
							</div>
							<div class="form-group">
								<button type="submit" name="submit" class="btn btn-primary">
									<i class="fa fa-spin btn-submit"></i> Submit
								</button>&nbsp;<span class="span-submit d-none">Processing...</span>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	</body>
</html>
<script type="text/javascript">
	$(document).ready(function() {
		$('#sub-form').on('submit', function(event) {
			event.preventDefault();
			$.ajax({
				url : "logic.php",
				data : $(this).serialize()+'&submit=submit',
				type : "post",
				beforeSend : function(data) {
					$('.btn-submit').addClass('fa-spinner');
					$('.span-submit').removeClass('d-none');
				},
				success : function(response) {
					$('.btn-submit').removeClass('fa-spinner');
					$('.span-submit').addClass('d-none');
					response = JSON.parse(response);
					$('.alert-class').addClass(response.class).removeClass('d-none').html(response.message);
					setTimeout(function() {
				        $('.alert-class').removeClass(response.class).addClass('d-none');
				        $('.alert-class').html('');
				    }, 5000);
				}
			})
		});
	});
</script>