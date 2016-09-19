<!DOCTYPE html>
<html>
	<head>
		<title>Hello</title>
		<style type="text/css">
			#target {
				border: 1px dotted red;
				min-height: 12px;
				padding: 4px;
			}
		</style>
	</head>
	<body>
		<p>
			This is a test to ensure JavaScript is working: the div below will be populated
			by a piece of code.
		</p>
		<div id="target">
		</div>
		<script type="text/javascript">
			window.onload = function() {
				var elem = document.getElementById('target');
				elem.innerHTML = 'Event successful';
			};
		</script>
	</body>
</html>
